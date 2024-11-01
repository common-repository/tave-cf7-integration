=== Plugin Name ===
Contributors: jpirkey
Tags: comments, contact form 7, tave studio manager, táve, tave
Requires at least: 4.1
Tested up to: 6.2
Stable tag: 1.1.11
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Integrate Contact Form 7 with Táve Studio Manager

== Description ==

This plugin adds Táve integration to all Contact Form 7 forms on your wordpress
installation. This allows you to create forms that will transmit that form data
to Táve and creating a new lead.

For complete help details see the [Support Site](https://help.tave.com/getting-started/contact-forms/using-contact-form-7)

== Installation ==

Install [Contact Form 7](https://wordpress.org/extend/plugins/contact-form-7/)
v3.9+ and set up a form.

Install the plugin as per usual. Add your Táve Studio Manager `Secret Key` and
Táve Studio Manager `Alias` info to the settings page. Check your Contact Form 7
forms to ensure they use fields with these names below. All field names should
be available in [Settings › Integrations › New Lead API](https://tave.com/app/settings/new-lead-api)
section of Táve and are case sensitive.  Any field passed in that is not
recognized will be created as a custom field.

The most common fields are below:
  * FirstName
  * LastName
  * Email
  * HomePhone
  * MobilePhone
  * WorkPhone
  * Source
  * EventDate
  * JobType
  * Message

See the [Support Site](https://help.tave.com/getting-started/contact-forms/using-contact-form-7) for a more detailed explanation.

== Frequently Asked Questions ==

= Does this work with the forms from ProPhoto template?  =
No, not currently.

== Changelog ==

= 1.1.11 =
* Updating tested up to Wordpress 6.2

= 1.1.10 =
* Updating tested up to Wordpress 5.8

= 1.1.9 =
* Updating tested up to Wordpress 5.7

= 1.1.8 =
* Fixing invalid header bug.

= 1.1.7 =
* Updating tested up to Wordpress 5.6

= 1.1.6 =
* Updating tested up to Wordpress 5.5

= 1.1.5 =
* Updating tested up to Wordpress 5.3

= 1.1.4 =
* Updating tested up to Wordpress 5.2

= 1.1.3 =
* Updating description and support site links.

= 1.1.2 =
* Fixing checkbox field mapping bug.

= 1.1.1 =
* Fixing broken "Settings" link on the plugins list page.

= 1.1.0 =
* Adding icons for the plugin directory.
* Updating stable tag to 4.8
* Added a debug url for testing.
* Miscellaneous cleanup and analytics.

= 1.0.0 =
* Initial official release by Táve.
