<?php
/**
 * Táve Integration using Contact Form 7
 *
 * @link https://help.tave.com/getting-started/contact-forms/using-contact-form-7
 * @author Jason Pirkey <jason@tave.com>
 * @copyright Táve 2017
 * @version 1.1.11
 * @since 1.0.0
 * @package Tave_CF7
 *
 * @wordpress-plugin
 * Plugin Name:       Táve Contact Form 7 Integration
 * Plugin URI:        https://github.com/tave/wordpress-cf7-plugin
 * Description:       Integrate Contact Form 7 with Táve Studio Manager
 * Version:           1.1.11
 * Author:            Táve
 * Author URI:        https://tave.com
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tave_cf7
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @author     Jason Pirkey <jason@tave.com>
 */
class Tave_CF7
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $plugin_file;

    /**
     * @var string
     */
    private $admin_settings_group_name;

    /**
     * @var boolean
     */
    private $initialized = false;

    /**
     * Run the plugin
     */
    public static function run()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new Tave_CF7();
        }
    }

    /**
     * Activate the plugin
     */
    public static function activate()
    {
        // nothing to do
    }

    /**
     * Deactivate the plugin
     */
    public static function deactivate()
    {
        // nothing to do
    }


    /**
     * Instantiate plugin
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Initialize the plugin
     */
    protected function init()
    {
        if ($this->initialized) {
            return;
        }

        $this->prefix = strtolower(__CLASS__);
        $this->version = '1.1.11';
        $this->name = $this->prefix . '_' . str_replace('.', '_', $this->version);
        $this->plugin_file = plugin_basename(__FILE__);
        $this->admin_settings_group_name = $this->prefix . '_settings_group';

        add_filter('plugin_action_links_' . $this->plugin_file, array($this, 'register_action_links'));

        add_action('admin_menu', array($this, 'create_admin_menu'));

        // hook into the contact form 7 send
        add_action('wpcf7_before_send_mail', array($this, 'send_data'), 1);

        $this->initialized = true;
    }

    public function register_action_links($links)
    {
        $links[] = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=' . dirname(__FILE__) . '/' . basename(__FILE__))) . '">' . __('Settings') . '</a>';
        return $links;
    }

    public function create_admin_menu()
    {

        // add the menu to the contact form 7 menu
        if (function_exists('wpcf7_admin_menu')) {
            add_submenu_page('wpcf7', __('T&aacute;ve Plugin Settings', 'wpcf7'), __('T&aacute;ve Settings', 'wpcf7'), 'administrator', __FILE__, array($this, 'admin_settings_page'));
        }

        add_action('admin_init', array($this, 'register_admin_settings'));
    }


    /**
     * Register the admin settings
     */
    public function register_admin_settings()
    {
        register_setting($this->admin_settings_group_name, $this->prefix . '_api_key');
        register_setting($this->admin_settings_group_name, $this->prefix . '_studio_id');
        register_setting($this->admin_settings_group_name, $this->prefix . '_ignore_fields');
        register_setting($this->admin_settings_group_name, $this->prefix . '_send_cf7_email', array($this, 'sanitize_checkbox'));
        register_setting($this->admin_settings_group_name, $this->prefix . '_send_tave_email', array($this, 'sanitize_checkbox'));
        register_setting($this->admin_settings_group_name, $this->prefix . '_debug_url');
        register_setting($this->admin_settings_group_name, $this->prefix . '_error_log');
    }


    /**
     * Sanitize the checkbox
     *
     * @param string $value
     * @return int
     */
    public function sanitize_checkbox($value)
    {
        return empty($value) ? 0 : 1;
    }


    /**
     * Display the admin settings page
     */
    public function admin_settings_page()
    {
        include dirname(__FILE__) . '/includes/admin_settings.php';
    }

    /**
     * Handle sending the form data to Táve
     */
    public function send_data()
    {
        $ignoreFields = array_merge(
            // default fields
            array(
                '_wpnonce',
                'g-recaptcha-response',
            ),
            // additional fields set in the _ignore_fields option
            explode(', ', get_option($this->prefix . '_ignore_fields'))
        );

        // trim the field names
        foreach ($ignoreFields as $i => $field) {
            $ignoreFields[$i] = trim($field);
        }

        $form = WPCF7_Submission::get_instance();

        $secret_key = trim(get_option($this->prefix . '_api_key'));
        $studio = trim(get_option($this->prefix . '_studio_id'));

        if (empty($secret_key)) {
            update_option($this->prefix . '_error_log', 'T&aacute;ve Secret Key is not set.');
            return false;
        }

        if (empty($studio)) {
            update_option($this->prefix . '_error_log', 'T&aacute;ve Studio ID is not set.');
            return false;
        }

        $send_tave_email = get_option($this->prefix . '_send_tave_email', 1);
        $send_cf7_email = get_option($this->prefix . '_send_cf7_email', 1);

        if (!$send_cf7_email) {
            // Tell Contact Form 7 not to send email
            $wpcf7 = WPCF7_ContactForm::get_current();
            $wpcf7->skip_mail = true;
        }

        // throw out any fields we don't want to send to Táve
        $convert_data = function_exists('mb_convert_encoding'); // used for converting form data to UTF-8
        $data = array();
        foreach ($form->get_posted_data() as $key => $value) {
            if (in_array($key, $ignoreFields) || strpos($key, '_wpcf7') === 0) {
                continue;
            }

            if (is_array($value)) {
                $value = reset($value);
            }

            $data[$key] = $convert_data ? mb_convert_encoding(trim($value), 'HTML-ENTITIES', 'UTF-8') : trim($value);
        }

        if (! array_key_exists('FirstName', $data)) {
            update_option($this->prefix . '_error_log', 'Missing FirstName field which is required.');
            return false;
        }

        if (! array_key_exists('JobType', $data)) {
            update_option($this->prefix . '_error_log', 'Missing JobType field which is required.');
            return false;
        }

        // setting the secretkey from the admin
        $data['SecretKey'] = $secret_key;

        $headers = array();
        if (!$send_tave_email) {
            $headers['X-Tave-No-Email-Notification'] = 'true';
        }

        // send this data to Táve
        $request = array(
            'timeout' => 20,
            'redirection' => 3,
            'blocking' => true,
            'headers' => $headers,
            'body' => $data,
        );

        // check the curl version to determine the domain
        $curl_version_info = curl_version();
        $domain = version_compare($curl_version_info['version'], '7.18.1', '>=') ? /* SNI supported */ 'tave.com' : /* SNI NOT supported */ 'legacy-ssl.tave.com';
        $debug_url = trim(get_option($this->prefix . '_debug_url'));
        if (!empty($debug_url)) {
            $url = $debug_url;
        } else {
            $url = "https://$domain/app/webservice/create-lead";
        }

        $url = add_query_arg(array(
            'X-Tave-PHP' => phpversion(),
            'X-Tave-Curl' => $curl_version_info['version'],
            'X-Tave-WP' => get_bloginfo('version'),
            'X-Tave-CF7' => defined('WPCF7_VERSION') ? WPCF7_VERSION : 'unknown',
            'X-Tave-CF7Plugin' => $this->version,
        ), rtrim($url, '/') . "/{$studio}");

        // send the data to tave.com
        $response = wp_remote_post($url, $request);

        if (empty($response)) {
            $errors = 'response is empty';
        } elseif (is_wp_error($response)) {
            $errors = array(
                'errors' => $response->errors,
                'error_data' => $response->error_data,
            );
        } elseif (wp_remote_retrieve_response_code($response) == 200 && trim(wp_remote_retrieve_body($response)) == 'OK') {
            update_option($this->prefix . '_error_log', 'Last request was successful at ' . date('r'));
            return true;
        } else {
            $errors = 'unhandled error encountered';
        }

        // format nice debugging info
        $errorlog = array(
            '',
            '=== ' . date('r') . ' ===============================',
            '',
            '--- curl version info --- ',
            htmlentities(var_export($curl_version_info, true)),
            '',
            '--- errors --- ',
            htmlentities(var_export($errors, true)),
            '',
            '--- request --- ',
            htmlentities(var_export($request, true)),
            '',
            '--- response --- ',
            htmlentities(var_export($response, true)),
        );

        update_option($this->prefix . '_error_log', implode("\n", $errorlog));

        return false;
    }
}


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

// Register activiation/deactivation hooks
register_activation_hook(__FILE__, array('Tave_CF7', 'activate'));
register_deactivation_hook(__FILE__, array('Tave_CF7', 'deactivate'));

// Run the plugin
Tave_CF7::run();
