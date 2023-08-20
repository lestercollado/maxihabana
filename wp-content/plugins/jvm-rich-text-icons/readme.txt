=== JVM Gutenberg Rich Text Icons ===
Contributors: jorisvanmontfort
Donate link: https://www.paypal.com/donate/?hosted_button_id=VXZJG9GC34JJU
Tags: gutenberg, editor, icons, icon set, font awesome, fontello, ACF, SVG icons
Requires at least: 5.4
Tested up to: 6.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add Font Awesome icons, or icons from a custom icon set to rich text fields anywhere in the Gutenberg block editor!

== Description ==
This plugin is a nice toolset for anyone wanting to integrate icons into the Gutenberg editor or options created with Advanced Custom Fields. 

Add inline icons to rich text fields like: paragraphs, headings, lists or buttons anywhere in the Gutenberg block editor, or add a dedicated icon block from within the Gutenberg editor.

This plugin ships with the free Font Awesome icon set as default icon set. You can choose between version 4.x, 5.x and version 6.x.
This plugin also provides an easy to use interface for creating a custom icon set based on SVG icons. Custom icons can be upload from the plugin settings using an easy to use drag & drop uploader.

The plugin also creates a new field type for Advanced Custom Fields (ACF) : JVM Icon. Now you can create custom fields that work with a the font awsome icon set or even beter: A custom created icon set.

If font awesome or the built in custom icon set configurator do not meet your needs you can also create your own custom icon set and load it using hooks provided by the plugin.

The plugin simply inserts icons in the following HTML format:

`
<i class="icon fa fa-address-book" aria-hidden="true"> </i>
`

The CSS class names and available icons can be all be modified to your liking if you are prepared to write some PHP hooks for your WordPress theme. Please note that you should keep the plugin settings set to use 'Font Awesome 4.7'. 
If you would like to load a custom created webfont or icon set you crafted yourself please read on. If you have the SVG files you can set the plugin settings to 'Custom SVG icon set' and upload your SVG files from the plugin settings.

**CSS file** 
A slightly customized version of the Font Awesome 4.7 CSS file is loaded by default on the front end and backend to make the plugin work out of the box, but you can also choose Font Awesome Free version 5.x or 6.x from the settings screen. 
If you want to use a custom created icon set it is advised to overide the icon set json file and CSS file using hooks provided by this plugin.

**Custom icon set file** 
If the plugin is set to Font Awesome 4.7 icon set (default behaviour) the icons are loaded from: wp-content/plugins/jvm-richtext-insert-icons/dist/fa-4.7/icons.json. The json file contains all css classes that can be turned into icons by Font Awesome 4.7 CSS file. You can load a custom json icon set file  by calling a filter hook in your (child) theme functions.php. 
For example:

`
function add_my_icons($file) {
    $file = get_stylesheet_directory().'/path_to_my/icons.json';
    return $file;
}

add_filter( 'jvm_richtext_icons_iconset_file', 'add_my_icons');
`

The icon config file can also be in fontello format since version 1.0.3. Have a look at: <https://fontello.com> to create your customized icon set.

**Custom CSS file** 
By default the Font Awesome 4.7 CSS is loaded from: wp-content/plugins/jvm-richtext-insert-icons/dist/fa-4.7/font-awesome.min.css. You can load a custom CSS file for your icon set by calling a filter hook in your (child) theme functions.php. 
For example:

`
function add_my_css($cssfile) {
    $cssfile = get_stylesheet_directory_uri().'/path_to_my/cssfile.css';
    return $cssfile;
}

add_filter( 'jvm_richtext_icons_css_file', 'add_my_css');
`

If you choose the load your own CSS file and want to disable the default CSS file use the following code:

`
add_filter( 'jvm_richtext_icons_css_file', '__return_false');
`
All icon markup has the classname "icon" prefixed to the icon HTML inserted. If you want to use some other prefix you can add a filter. Like this:

`
function my_icon_class($css_class_name) {
    return 'my-custom-css-class-name';
}

add_filter( 'jvm_richtext_icons_base_class', 'my_icon_class');
`

Use this hook to disable the entire plugin settings screen that was added in 1.0.9:
`
add_filter('jvm_richtext_icons_show_settings', '__return_false');
`

Please note that settings will still be loaded so please make sure you have set the settings to default font awesome if you are loading a custom icon set with the plugin hooks.

== Changelog ==

= 1.2.3 =
Fixed the thick border around the toolbar button by using the correct toolbar button markup.

= 1.2.2 =
Bugfix WordPress 6.2 site editor rich text blocks not editable.

= 1.2.1 =
Bugfix for the single icon block using incomplete css classes.

= 1.2.0 =
Added a dedicated single icon block for Gutenberg.

= 1.1.9 =
Fixed some deprecation errors to get this plugin compatible with the site editor and future WordPress versions. Some work is still needed on this.

= 1.1.8 =
Got rid of position relative for custom icon sets.

= 1.1.7 =
Fixed editor dialog position on smaller screens.

= 1.1.5 =
Font Awesome 4.7 webfont URL's fixed.

= 1.1.4 =
Now also load in the site editor. Not all block however.

= 1.1.3 =
Fixed a deprecated warning in php 8.1.

= 1.1.2 =
Added Font Awesome Free 5.15.4 and Font Awesome Free 6.2.0 to the settings. The CSS for these verions are loaded from a CDN. Font Awesome version 4.7 is still the default.

= 1.1.1 =
Added a notice on the settings screen if a custom icon set is loaded and the SVG icon set is selected. These options won't work together.

= 1.1.0 =
Added a hook to disable the plugin settings page altogether for those who like a clean WordPress admin.

Use this in your functions.php to disable the settings screen that was added in 1.0.9:
`add_filter('jvm_richtext_icons_show_settings', '__return_false');`

= 1.0.9 =
Added a plugin settings screen and a nice interface to upload and create a custom SVG file based icon set. If you like this feature please consinder donating: https://www.paypal.com/donate/?hosted_button_id=VXZJG9GC34JJU

= 1.0.8 =
Fixed some WordPress coding convenstions and tested and fixed some minor issues for WordPress 6.0.

= 1.0.7 =
Fixed the styling of the editor pop-over. It was to large since WordPress 5.9.

= 1.0.6 =
The addon is now also loaded in the widget screen (widget.php)

= 1.0.5 =
Added a hook for modifying the editor javascript file loaded for advanced users. 
Example usage:

`
function add_my_js_file($file) {
    $file = '/path_to_my/js_file.js';
    return $file;
}

add_filter( 'jvm_richtext_icons_editor_js_file', 'add_my_js_file');
`

= 1.0.4 =
Bug fix: Replaced the deprecated block_editor_settings hook by the new block_editor_settings_all hook. This fixes a deprecated notice.

= 1.0.3 =
New feature: ACF field for the JVM icon set loaded.
New feature: Font icon config file can now also ben in fontello format

= 1.0.2 =
Bugfix: Changed backend asset loading to load only on new posts and edit post pages. In version 1.0.1 scripts for this plugin loaded on all backend pages and kept breaking the widget text editor.

= 1.0.1 =
Php error fix for some php versions on plugin activation.

= 1.0.0 =
Initial release

= Stable =
1.0.0