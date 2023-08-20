<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class JVM_Richtext_icons_settings {
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct() {
        add_action('after_setup_theme', array($this, 'try_add_settings'));
    }

    /**
     * Only load the settings screen if it was not disabled by a hook.
     */
    public function try_add_settings() {
        $show_settings = apply_filters('jvm_richtext_icons_show_settings', true);
        if ($show_settings) {
            add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
            add_action( 'admin_init', array( $this, 'page_init' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ));

            // Ajax calls
            add_action('wp_ajax_jvm-rich-text-icons-delete-icon', array( $this, 'ajax_delete_icon'));
            add_action('wp_ajax_jvm-rich-text-icons-upload-icon', array( $this, 'ajax_upload_icon'));

            // Notice on settings screen if a custom icon set is loaded
            add_action('admin_notices', array($this, 'admin_notice'));
        }
    }

    public function admin_notice() {
        $current_screen = get_current_screen();
        // Only if we are in the settings page
        if ( $current_screen->base == 'settings_page_jvm-rich-text-icons' ) {
            if (isset($this->options['icon_set'])) {
                if ($this->options['icon_set'] != 'default') {
                    $iconFileDefault = plugin_dir_path( __DIR__ ).'src/icons.json';
                    $iconFileLoaded = apply_filters('jvm_richtext_icons_iconset_file', $iconFileDefault);

                    if ($iconFileDefault != $iconFileLoaded) {
                        echo '<div class="notice notice-warning">';
                        echo '<p>'.sprintf(__("A custom icon set is being loaded from: %s. Keep your setting set to the default Font Awsome icon set to keep this working. The custom icon set can't be loaded if you are creating a SVG icon set from this page.", 'jvm-rich-text-icons'), $iconFileLoaded).'</p>';
                        echo '</div>';
                    }
                }
            }
        }
    }

    public function enqueue_scripts() {
        $current_screen = get_current_screen();
        // Only if we are in the settings page
        if ( $current_screen->base == 'settings_page_jvm-rich-text-icons' ) {
            wp_enqueue_script( 'jvm-rich-text-icons-dropzone', plugins_url( '/dist/dropzone.min.js', dirname( __FILE__ ) ) );
            wp_enqueue_script( 'jquery-ui-dialog' ); // jquery and jquery-ui should be dependencies, didn't check though...
            wp_enqueue_style( 'wp-jquery-ui-dialog' );
            wp_enqueue_style(
                'jvm-rich-text-icons-admin-settings', // Handle.
                plugins_url( 'dist/css/admin-settings.css', dirname( __FILE__ ) ),
                array(),
                filemtime( plugin_dir_path( __DIR__ ) . 'dist/css/admin-settings.css' ) // Version: File modification time.
            );

            // Register the settings script for the backend.
            wp_enqueue_script(
                'jvm-rich-text-icons-settings-js', // Handle.
                plugins_url( '/dist/settings.js', dirname( __FILE__ ) ),
                array( 'jquery-ui-dialog'), // Dependencies, defined above.
                null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
                true // Enqueue the script in the footer.
            );

            wp_localize_script(
                'jvm-rich-text-icons-settings-js',
                'jvm_richtext_icon_settings', // Array containing dynamic data for a JS Global.
                [
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'max_upload_size' => wp_max_upload_size(),
                    'text' => [
                        'delete_icon' => __('Delete Icon', 'jvm-rich-text-icons'),
                        'delete_icon_confirm' => __("You are about to permanently delete this icon from your site. This action cannot be undone.\n'Cancel' to stop 'OK' to delete.", 'jvm-rich-text-icons'),
                    ]
                ]
            );
        } 
    }

    /**
     * Remove an icon
     */
    public function ajax_delete_icon() {

        if (isset($_POST['file'])) {
            $file = $_POST['file'];
            $base = JVM_Richtext_icons::get_svg_directory();

            if (file_exists($base.$file)) {
                if (unlink($base.$file)) {
                    wp_send_json(["success" => true]);
                    exit;
                }
            }
        }
        
        wp_send_json(["success" => false]);
        exit;
    }

    /**
     * Upload an icon
     */
    public function ajax_upload_icon() {
        if (isset($_FILES['file'])) {
            // Check if file is SVG as we only accept SVG files
            if ($_FILES['file']['type'] == 'image/svg+xml') {
                
                $base = JVM_Richtext_icons::get_svg_directory();
                $new_file_name = $this->generate_unique_svg_file_name($_FILES['file']['name']);
                if (move_uploaded_file($_FILES['file']['tmp_name'], $base.$new_file_name)) {
                    $pi = pathinfo($new_file_name);
                    $css_class = JVM_Richtext_icons::get_class_prefix();
                    $icon_class = sanitize_title($pi['filename']);
                    wp_send_json([
                        "success" => true,
                        "icon_class_full" => $css_class.' '.$icon_class,
                        "icon_class" => $icon_class,
                        "file" => $new_file_name,
                        'css_code' => JVM_Richtext_icons::parse_dynamic_css()
                    ]);
                    exit();
                }
            }
        }

        wp_send_json(["success" => false]);
        exit();
    }

    /**
     * Generate a unuique file name for a SVG upload to prevent duplicates
     */
    private function generate_unique_svg_file_name($name, $addon='') {
        $base = JVM_Richtext_icons::get_svg_directory();
        $pi = pathinfo($name);
        $namecheck= $pi['filename'].$addon.'.'.$pi['extension'];
        if (file_exists($base.$namecheck)) {
            if(empty($addon)) {
                $addon = 1;
            }else {
                $addon = str_replace('-', '', $addon);
                $addon = (int) $addon;
                $addon ++;
            }

            return $this->generate_unique_svg_file_name($name, '-'.$addon);
        }

        return $namecheck;
    }

    /**
     * Add options page
     */
    public function add_plugin_page() {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            __('JVM rich text icons', 'jvm-rich-text-icons'), 
            'manage_options', 
            'jvm-rich-text-icons', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        // Taken from media-new.php
        if ( ! current_user_can( 'upload_files' ) ) {
            wp_die( __( 'Sorry, you are not allowed to upload files.' ) );
        }

        echo JVM_Richtext_icons::render_view('settings.php');
    }

    /**
     * Register and add settings
     */
    public function page_init() {        
        // Set class property
        $this->options = JVM_Richtext_icons::get_settings();

        add_settings_section(
            'general', // ID
            '', // Title
            '',
            'jvm-rich-text-icons' // Page
        );

        add_settings_field(
            'icon_set', // ID
            __('Icon set', 'jvm-rich-text-icons'), // Title 
            function () {
                echo '<select id="jvm-rich-text-icons_icon_set" name="jvm-rich-text-icons[icon_set]">';

                $checked = $this->options['icon_set'] == 'default' ? ' selected' : '';
                echo '<option value="default"'.$checked.'>'.__('Font Awsome 4.7', 'jvm-rich-text-icons').'</option>';
                $checked = $this->options['icon_set'] == 'fa-5' ? ' selected' : '';
                echo '<option value="fa-5"'.$checked.'>'.__('Font Awsome Free 5.15.4', 'jvm-rich-text-icons').'</option>';
                $checked = $this->options['icon_set'] == 'fa-6' ? ' selected' : '';
                echo '<option value="fa-6"'.$checked.'>'.__('Font Awsome Free 6.2.0', 'jvm-rich-text-icons').'</option>';
                $checked = $this->options['icon_set'] == 'custom-svg' ? ' selected' : '';
                echo '<option value="custom-svg"'.$checked.'>'.__('Custom SVG icon set', 'jvm-rich-text-icons').'</option>';
                echo '</select>';
            }, 
            'jvm-rich-text-icons', // Page
            'general' // Section           
        );

        /*add_settings_field(
            'technology', // ID
            __('Technology', 'jvm-rich-text-icons'), // Title 
            function () {
                echo '<select name="jvm-rich-text-icons[icon_set]">';
                echo '<option value="html-css">'.__('HTML + CSS', 'jvm-rich-text-icons').'</option>';
                echo '<option value="inline-svg">'.__('Inline SVG elements', 'jvm-rich-text-icons').'</option>';
                echo '</select>';
            }, 
            'jvm-rich-text-icons', // Page
            'general' // Section           
        );*/

        register_setting(
            'jvm-rich-text-icons', // Option group
            'jvm-rich-text-icons', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );
    }

    public function validation_notice($text) {
        return '<div id="setting-error-empty" class="error settings-error notice"><p><strong>'.esc_html($text).'</strong></p></div>';
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ) {

        // Hmmm....
        return $input;
    }
}

$JVM_Richtext_icons_settings = new JVM_Richtext_icons_settings();