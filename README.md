# simple-members-only
Secure parts of your WordPress site for logged-in users only.

This plugin becomes useful as part of a more complete security/members system when combined with:

1. Custom roles (created however you prefer).
2. Role checks where needed in your templates (for example, in your header file to display alternative menus based on the user's role).

Note: this plugin requires the Advanced Custom Fields plugin.

## Features

* Option to specify required user roles for pages and posts.
* Show message and login form when security check fails.
* Login page template can be overwritten in your theme (create the file my-theme/smo/login.php).

Additionally:

* You can invoke the has_permitted_role($permitted_roles) method in your theme. It accepts an array of permitted roles (e.g., array('editor')) and returns true if access should be permitted and false if not.
* You can clone the included "Permitted Roles" field for use in your own ACF field sets (e.g., if you wish to lock down some content added with ACF).

## Possible future features

* Shortcode to only display content for specified roles.
* ACF field type to select roles (useful when you use ACF to build page layouts--e.g., specify that a content row is only visible for certain roles).
* Ability to set alternate versions for different roles on pages and posts.
