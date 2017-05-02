<?php
/*
Plugin Name: Simple Members Only
Plugin URI: https://github.com/mmirus/simple-members-only
Description: Secure parts of your WordPress site for logged-in users only.
Author: Matt Mirus
Author URI: https://github.com/mmirus
Version: 1.2
GitHub Plugin URI: https://github.com/mmirus/simple-members-only
*/

namespace SMO;

class SMO {
  function __construct() {
    // check if ACF is present and active, and fail if not
		add_action('plugins_loaded', array($this, 'acf_check'));
    
    // add load point for JSON field definitions
		add_filter('acf/settings/load_json', array($this, 'acf_add_json_load_point'));
    
    // populate permitted roles field
    add_filter('acf/load_field/name=smo_permitted_roles', array($this, 'populate_permitted_roles'));
    
    // add custom ACF location rule to match pages and all post types
    add_filter('acf/location/rule_values/post_type', array($this, 'acf_location_rules_values_post_types'));
    add_filter('acf/location/rule_match/post_type',  array($this, 'acf_location_rules_match_any_post_type'), 10, 3);
    
    // perform securit check; load alternative content or redirect to login as needed
    add_action('template_include', array($this, 'check_access'), 99);
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
  
  // append this plugin's ACF JSON field definitions load point to list of load points
  public function acf_add_json_load_point($paths) {
    $paths[] = plugin_dir_path(__FILE__) . '/acf-json';

    return $paths;
  }

  // populate permitted roles field
  public function populate_permitted_roles($field) {
  	// reset choices
  	$field['choices'] = array();
  	
  	global $wp_roles;
  	$roles = $wp_roles->get_names();
  	
  	foreach ($roles as $role) {
  		$field['choices'][strtolower($role)] = $role;
  	}

  	return $field;
  }

  // add custom ACF location value to match pages and all post types
  public function acf_location_rules_values_post_types($choices) {
    $choices['any'] = 'Any';

    return $choices;
  }
  
  // custom ACF location matching rule to match pages and all post types
  public function acf_location_rules_match_any_post_type($match, $rule, $options) {
    if ($rule['param'] === 'post_type' && $rule['operator'] === '==' && $rule['value'] === 'any') {
      return ($options['post_type'] !== 0);
    }
    
    return $match;
  }

  // check if current visitor is permitted to access requested item; load login page if not
  public function check_access($template) {
    global $wp_query;
    
    // if in admin OR not page/post, there's no security check to apply; bail
    if (is_admin() || (!$wp_query->is_single && !$wp_query->is_page)) {
      return $template;
    }
    
    $post_id = $wp_query->queried_object_id;
    
    // load login template if user does not have a permitted role
    $permitted_roles = get_field('smo_permitted_roles', $post_id);
    if (!$this->has_permitted_role($permitted_roles)) {
      $template_name = 'login.php';
      // Check if a custom template exists in the theme folder, if not, load the plugin template file
      if ($theme_file = locate_template(array('smo/' . $template_name))) {
        $template = $theme_file;
      }
      else {
        $template = dirname( __FILE__ ) . '/templates/' . $template_name;
      }
    }
    
    return $template;
  }
  
  // check user role against permitted roles for requested item
  public function has_permitted_role($permitted_roles) {
    $can_access = true;
    
    // if a role is required...
    if (!empty($permitted_roles)) {
      // fail if user isn't logged in
      if (!is_user_logged_in()) {
        $can_access = false;
      }
      
      // get user roles
      $user_id = get_current_user_id();
      $user_info = get_userdata($user_id);
      $roles = $user_info->roles;
      
      // fail if user's role does not match
      if (empty(array_intersect($permitted_roles, $roles))) {
        $can_access = false;
      }
    }
    
    return $can_access;
  }
}

new SMO();
