<?php
// If this file is called directly, abort.
if (! defined('WPINC') || empty($this)) {
    die;
}
?>
<div class="wrap">
  <h2>Contact Form 7 T&aacute;ve Integration</h2>

  <?php if (!empty($_GET['settings-updated'])):?>
    <div id="message" class="updated">
      <p><strong><?php _e('Settings saved.') ?></strong></p>
    </div>
  <?php endif?>

  <div>
    <form method="post" action="options.php">
      <?php settings_fields($this->admin_settings_group_name); ?>
      <?php do_settings_sections($this->admin_settings_group_name); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">T&aacute;ve Studio ID:</th>
          <td>
            <input type="text" name="<?php echo $this->prefix . '_studio_id'?>" value="<?php echo get_option($this->prefix . '_studio_id'); ?>" size="50"/>
            <br>
            Your Studio ID can be found in your T&aacute;ve dashboard in <a href="https://tave.com/app/settings/new-lead-api">Settings &rsaquo; Integrations &rsaquo; New Lead API</a>.
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">T&aacute;ve Secret Key:</th>
          <td>
            <input type="text" name="<?php echo $this->prefix . '_api_key'?>" value="<?php echo get_option($this->prefix . '_api_key'); ?>" size="50"/>
            <br>
            Your secret key can be found in your T&aacute;ve dashboard in <a href="https://tave.com/app/settings/new-lead-api">Settings &rsaquo; Integrations &rsaquo; New Lead API</a>.
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Excluded Input Field Names:</th>
          <td>
            <input type="text" name="<?php echo $this->prefix . '_ignore_fields'?>" value="<?php echo get_option($this->prefix . '_ignore_fields'); ?>" size="50"/>
            <br>These are input fields you dont want to pass to T&aacute;ve. This is a comma separated list of field names (eg: FirstName, JobType, etc).
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Send contact form 7 email?</th>
          <td><input type="checkbox" name="<?php echo $this->prefix . '_send_cf7_email'?>" value="checked" <?php checked(get_option($this->prefix . '_send_cf7_email', 1)); ?> />Check this box to receive the Contact Form 7 email.</td>
        </tr>
        <tr valign="top">
          <th scope="row">Send T&aacute;ve email?</th>
          <td><input type="checkbox" name="<?php echo $this->prefix . '_send_tave_email'?>" value="checked" <?php checked(get_option($this->prefix . '_send_tave_email', 1)); ?> />Check this box to receive the T&aacute;ve email.</td>
        </tr>
        <tr valign="top">
          <th scope="row">Debug URL:</th>
          <td>
            <input type="text" name="<?php echo $this->prefix . '_debug_url'?>" value="<?php echo get_option($this->prefix . '_debug_url'); ?>" size="50"/>
            <br>
            (internal use only)
          </td>
        </tr>
      </table>
      <?php submit_button(); ?>
    </form>
  </div>
  <?php $errorlog = get_option($this->prefix . '_error_log')?>
  <?php if (strlen($errorlog)):?>
    <div class="postbox-container" style="width:100%">
      <div class="postbox">
        <div class="inside">
          <h3 style="padding:5px;">Debugging information</h3>
          <pre>
            <?php echo htmlentities(get_option($this->prefix . '_error_log'));?>
          </pre>
        </div>
      </div>
    </div>
  <?php endif?>

</div>
