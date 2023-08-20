<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class JVM_Richtext_icons {
    /**
     * This plugin's instance.
     *
     * @var JVM_Richttext_icons
     */
    private static $instance;

    /**
     * Registers the plugin.
     */
    public static function register() {
        if ( null === self::$instance ) {
            self::$instance = new JVM_Richtext_icons();
        }
    }

    /**
     * The Constructor.
     */
    private function __construct() {
        add_filter( 'block_editor_settings_all', array( $this, 'block_editor_settings' ), 10, 2 );
        add_action( 'init', array( $this, 'load_css') );
        add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_assets') );
        add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
        add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );


        /**
         * Register Gutenberg block on server-side.
         *
         * Register the block on server-side to ensure that the block
         * scripts and styles for both frontend and backend are
         * enqueued when the editor loads.
         *
         * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
         * @since 1.16.0
         */
        register_block_type(
            'jvm/single-icon', array(
                // Enqueue blocks.style.build.css on both frontend & backend.
                //'style'         => 'jvm_details_summary-cgb-style-css',
                // Enqueue blocks.build.js in the editor only.
                'editor_script' => 'jvm-rich-text-icons-js',
                // Enqueue blocks.editor.build.css in the editor only.
                'editor_style'  => 'jvm-rich-text-icons-editor-css',
            )
        );
    }

    /**
     * Add settings as link from the plugin screen
     */
    public function plugin_action_links($links, $plugin_file) {

        if ($plugin_file == 'jvm-rich-text-icons/plugin.php' && apply_filters('jvm_richtext_icons_show_settings', true)) {
            $setting_link = array(
                '<a href="' . admin_url( 'options-general.php?page=jvm-rich-text-icons' ) . '">' . esc_html__( 'Settings' ) . '</a>',
            );
            return array_merge( $links, $setting_link );
        }

        return $links;
    }

    /**
     * Add settings as link from the plugin screen
     */
    public function plugin_row_meta($links, $plugin_file) {
        if ($plugin_file == 'jvm-rich-text-icons/plugin.php') {
            $donate_link = array(
                '<a href="https://www.paypal.com/donate/?hosted_button_id=VXZJG9GC34JJU" target="_blank">' . esc_html__( 'Donate to Support', 'jvm-rich-text-icons' ) . '</a>',
            );
            return array_merge( $links, $donate_link );
        }
        return $links;
    }

    /**
     * Filters the settings to pass to the block editor.
     *
     * @param array  $editor_settings The editor settings.
     * @param object $post The post being edited.
     *
     * @return array Returns updated editors settings.
     */
    public function block_editor_settings( $editor_settings, $post ) {
        if ( ! isset( $editor_settings['jvm_richtext_icons'] ) ) {

            $editor_settings['jvm_richtext_icons'] = [
                'formats'    => array(
                    'name'  => 'formats',
                    'label' => __( 'Formats', 'block-options' ),
                    'items' => array(
                        'icons'        => array(
                            'name'  => 'icon',
                            'label' => __( 'Insert icon', 'jvm-rich-text-icons' ),
                            'value' => true,
                        )
                    )
                )
            ];

        }

        return $editor_settings;
    }


    /**
     * Enqueue Gutenberg block assets for both admin backend.
     */
    public function load_admin_assets($hook_suffix) {



        if( 'post.php' == $hook_suffix 
            || 'post-new.php' == $hook_suffix 
            || 'widgets.php' == $hook_suffix
            || 'site-editor.php' == $hook_suffix) {

            // Register block editor script for backend.
            wp_enqueue_script(
                'jvm-rich-text-icons-js', // Handle.
                plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
                array(),
                //array( 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
                '1.2.3',
                true // Enqueue the script in the footer.
            );

            // Register block editor styles for backend.
            wp_enqueue_style(
                'jvm-rich-text-icons-editor-css', // Handle.
                plugins_url( 'dist/editor.css', dirname( __FILE__ ) ), // Block editor CSS.
                array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
                filemtime( plugin_dir_path( __DIR__ ) . 'dist/editor.css' ) // Version: File modification time.
            );

            $icons = JVM_Richtext_icons::get_icons();
            $base_class = JVM_Richtext_icons::get_class_prefix();
            wp_localize_script(
                'jvm-rich-text-icons-js',
                'jvm_richtext_icon_settings', // Array containing dynamic data for a JS Global.
                [
                    'iconset' => $icons,
                    'base_class' => $base_class,
                    'text' => [
                        'delete_icon' => __('Delete Icon', 'jvm-rich-text-icons')
                    ]
                ]
            );
        }
    }

    /**
     * Enqueue Gutenberg block assets for both frontend + backend.
     */
    public static function load_css() {
        $settings = self::get_settings();
        $icons = [];
        $icon_set = 'default';
        if (isset($settings['icon_set'])) {
            $icon_set = $settings['icon_set'];
        }
        
        if ($icon_set == 'custom-svg') {
            wp_register_style('jvm-rich-text-icons-svg', false);
            wp_enqueue_style( 'jvm-rich-text-icons-svg' );

            wp_add_inline_style('jvm-rich-text-icons-svg', JVM_Richtext_icons::parse_dynamic_css());
        }else {

            $folder = $icon_set;
            if ($icon_set == 'default') {
                $fontCssFile = plugins_url( 'dist/fa-4.7/font-awesome.min.css', dirname( __FILE__ ));
            }else if ($icon_set == 'fa-5') {
                $fontCssFile = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';
            }else if ($icon_set == 'fa-6') {
                $fontCssFile = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css';
            }

            // Icon set CSS (font awesome 4.7 is shipped by default).
            $fontCssFile = apply_filters('jvm_richtext_icons_css_file', $fontCssFile);

            if (!empty($fontCssFile)) {
                wp_enqueue_style(
                    'jvm-rich-text-icons-icon-font-css', // Handle.
                    $fontCssFile
                );
            }
        }
    }

    /**
     * Get the class prefix for the css
     * @return [string]
     */
    public static function get_class_prefix() {
        return apply_filters('jvm_richtext_icons_base_class', 'icon');
    }

    /**
     * Get the icon config
     * @return [array]
     */
    public static function get_icons() {
        $settings = self::get_settings();
        $icons = [];
        $icon_set = 'default';
        if (isset($settings['icon_set'])) {
            $icon_set = $settings['icon_set'];
        }
        

        if ($icon_set == 'custom-svg') {
                $svg_files = self::get_svg_file_list();
                foreach ($svg_files as $file) {
                    $pi = pathinfo($file);
                    if ($pi['extension'] == 'svg') {
                        $icon_class = sanitize_title($pi['filename']);
                        $icons[] = $icon_class;
                    }
                }
        }else {
            $folder = $icon_set;
            if ($icon_set == 'default') {
                $folder = 'fa-4.7';
            }

            // WP Localized globals. Use dynamic PHP stuff in JavaScript via `cgbGlobal` object.
            $iconFile = plugin_dir_path( __DIR__ ).'dist/'.$folder.'/icons.json';
            $iconFile = apply_filters('jvm_richtext_icons_iconset_file', $iconFile);

            if (file_exists($iconFile)) {
                $iconData = file_get_contents($iconFile);
                $data = json_decode($iconData);
                $icons = [];
                // Check if data is fontello format
                if (isset($data->glyphs)) {
                    foreach($data->glyphs as $g) {
                        $icons[] = $data->css_prefix_text.$g->css;
                    }
                }else {
                    $icons = $data;
                }

                $icons = apply_filters('jvm_richtext_icons_iconset', $icons);            
            }            
        }

        return $icons;
    }

    /**
     * Get the plugin settings
     * @return [array] [options]
     */
    public static function get_settings() {
        $settings = get_option('jvm-rich-text-icons');

        // Array if no options
        if (false == $settings) {
            $settings = [];
        }

        if (!isset($settings['icon_set'])) {
            $settings['icon_set'] = 'default';
        }

        return $settings;
    }

    /**
     * Render a view file
     * @param  [string] $fileName
     * @param  array  $dataForView
     * @return [string] rendered view
     */
    public static function render_view($fileName, $dataForView=array()) {

        if (!file_exists(plugin_dir_path( __DIR__ ).'views/'.$fileName)) {
            return plugin_dir_path( __DIR__ ).'views/'.$fileName. ' not found.';
        }else {

            // Extract vars to local namespace
            extract($dataForView, EXTR_SKIP);
            ob_start();

            include plugin_dir_path( __DIR__ ).'views/'.$fileName;
            
            $out = ob_get_clean();

            return $out;
        }
    }

    /**
     * Get the css for custom svg settings
     * @return [string] [css styling]
     */
    public static function parse_dynamic_css() {
        return JVM_Richtext_icons::render_view('dynamic_css.php', ['files' => JVM_Richtext_icons::get_svg_file_list(), 'settings', JVM_Richtext_icons::get_settings()]);
    }

    /**
     * Get a list of uploaded custom SVG icons
     * @return [array] [icons]
     */
    public static function get_svg_file_list() {
        $base = self::get_svg_directory();
        $files = scandir($base);
        $files_out = [];
        foreach ($files as $file) {
            $pi = pathinfo($base.$file);

            if ($pi['extension'] == 'svg') {
                $files_out[] = $base.$file;
            }
        }

        return $files_out;
    }

    /**
     * Get the icon upload base directory
     * @return [string]
     */
    public static function get_svg_directory() {
        $upload = wp_upload_dir();
        $base = $upload['basedir'].'/jvm-rich-text-icons/';
        if (!is_dir($base)) {
            mkdir($base);
        }
        return $base;
    }
}

JVM_Richtext_icons::register();