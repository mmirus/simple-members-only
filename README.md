# simple-members-only

Secure parts of your WordPress site for logged-in users only.

This plugin becomes useful as part of a more complete security/members system when combined with:

1. Custom roles (created however you prefer).
2. Role checks where needed in your templates (for example, in your header file to display alternative menus based on the user's role).

Note: this plugin requires the Advanced Custom Fields plugin.

## Features

- Option to specify required user roles for pages.
- Show message and login form when security check fails.
- Login page template can be overwritten in your theme (create the file my-theme/smo/login.php).

Additionally:

- You can invoke the has_permitted_role(\$permitted_roles) method in your theme. It accepts an array of permitted roles (e.g., array('editor')) and returns true if access should be permitted and false if not.
- You can clone the included "Permitted Roles" field for use in your own ACF field sets (e.g., if you wish to lock down some content added with ACF).

## Enabling Additional Post Types

Out of the box, the plugin is set up for use with pages. You can enable it for use with additional post types with two steps:

1. Tell SMO to run security checks on your post type: hook onto the filter `SMO\post_types`, returning an array of all post types SMO should check, e.g., `return array('page');`.
2. Edit the ACF field group and add additional location rules as needed for the extra post types.

## Filtering default permitted roles

By default, admins pass all permitted roles checks. If you want to filter this to remove admins or add other roles, you can use the `SMO\permitted_roles` filter. For example:

```php
// add editor to SMO permitted roles
add_filter('SMO\\permitted_roles', function ($roles) {
    $roles[] = 'editor';
    return $roles;
});
```
