<?php
/*
Plugin Name: Simple Members Only
Plugin URI: https://github.com/mmirus/simple-members-only
Description: Secure parts of your WordPress site for logged-in users only.
Author: Matt Mirus
Author URI: https://github.com/mmirus
Version: 1.0
GitHub Plugin URI: https://github.com/mmirus/simple-members-only
*/

namespace SMO;

class SMO {
  function __construct() {
    // check if ACF is present and active, and fail if not
		add_action('plugins_loaded', array($this, 'acf_check'));
  }
  
  // Check if Advanced Custom Fields is loaded and deactivate if not
  public function acf_check() {
    if(! class_exists('acf')) {
      add_action('admin_init', array($this, 'deactivate'));
      add_action('admin_notices', array($this, 'admin_notice_no_acf'));
    }
  }
  
  // deactivate this plugin
  public function deactivate() {
    deactivate_plugins(plugin_basename(__FILE__));
  }
  
	// notify that ACF is required and plugin has been deactivated
	public function admin_notice_no_acf() {
		printf('<div class="error notice is-dismissible"><p class="extension-message"><strong>Advanced Custom Fields Pro</strong> is required by <strong>Simple Members Only</strong>. Deactivating the plugin.</p></div>');
	}
}

new SMO();
