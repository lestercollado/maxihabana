<?php
// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('JVM_acf_plugin_jvm_rich_text_icons') )  {

class JVM_acf_plugin_jvm_rich_text_icons {
    
    // vars
    var $settings;
    
    
    /*
    *  __construct
    *
    *  This function will setup the class functionality
    *
    *  @type    function
    *  @since   1.0.0
    *
    *  @param   void
    *  @return  void
    */
    
    public function __construct() {
        
        // settings
        // - these will be passed into the field class.
        $this->settings = array(
            'version'   => '1.0.5',
            'url'       => plugin_dir_url( __FILE__ ),
            'path'      => plugin_dir_path( __FILE__ )
        );
        
        
        // include field
        add_action('acf/include_field_types',   array($this, 'include_field')); // v5

        add_action( 'admin_init', array( $this, 'load_admin_assets') );
        add_action( 'wp_ajax_acf/fields/jvm-richtext-insert-icons/query', array( $this, 'select2_ajax_request' ) );
    }
    

    public function select2_ajax_request () {
        if ( ! acf_verify_ajax() ) {
            die();
        }

        $icons = JVM_Richtext_icons::get_icons();
        $css_class = JVM_Richtext_icons::get_class_prefix();

        $s = false;
        if (isset($_POST['s'])) {
            if (!empty($_POST['s'])) {
                $s = $_POST['s'];
            }
        }

        $icons_out = [];
        foreach ($icons as $ic) {
            $pos = empty($s) ? 1 : strpos($ic, $s);
            if (($pos === 0 || $pos >= 1)) {
                $icons_out[] = [
                    'id' => $ic,
                    'text' => '<i class="'.$css_class.' '.$ic. '" aria-hidden="true"></i> '.$ic
                ];
            }     
        }

        $response = [
            'more' => false,
            'limit' => 0,
            'results' => $icons_out,
        ];

        acf_send_ajax_results( $response );
    }
    

    public function load_admin_assets() {
        $js_file = apply_filters('jvm_richtext_icons_editor_js_file', plugins_url( '/dist/acf.js', dirname( __FILE__ ) ));

        wp_enqueue_script(
            'jvm-rich-text-icons-acf', // Handle.
            $js_file, // Block.build.js: We register the block here. Built with Webpack.
            //array( 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
            array(),
            null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
            true // Enqueue the script in the footer.
        );

        JVM_Richtext_icons::load_css();
    }

    /*
    *  include_field
    *
    *  This function will include the field type class
    *
    *  @type    function
    *  @date    17/02/2016
    *  @since   1.0.0
    *
    *  @param   $version (int) major ACF version. Defaults to false
    *  @return  void
    */
    
    public function include_field( $version = false ) {
        
        // support empty $version
        if( !$version ) $version = 5;
        
        
        // load textdomain
        load_plugin_textdomain( 'jvm-richtext-insert-icons', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' ); 
        
        
        // include
        include_once('fields/class-jvm_acf_jvm_rich_text_icons' . $version . '.php');
    }
    
}


// initialize
new JVM_acf_plugin_jvm_rich_text_icons();

// class_exists check
}