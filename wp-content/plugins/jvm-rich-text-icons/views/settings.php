<div class="wrap">
    <h1><?php _e('Rich Text Icons Settings', 'jvm-rich-text-icons');?></h1>
    <form id="jvm-rich-text-icons-settings-form" method="post" action="options.php">
    <?php
        // Prints out all the setting fields
        settings_fields( 'jvm-rich-text-icons' );
        do_settings_sections( 'jvm-rich-text-icons' );
        submit_button();

        $settings = JVM_Richtext_icons::get_settings();
    ?>
    </form>
    <div id="jvm-rich-text-icons_custom_icon_generator" <?php echo $settings['icon_set'] != "custom-svg" ? ' style="display: none;"' : "";?>>
        <h2 class="title"><?php _e('Custom SVG icon set', 'jvm-rich-text-icons');?></h2><a id="add_icon_btn" href="#" class="page-title-action"><?php _e('Add Icon', 'jvm-rich-text-icons');?></a>
        <?php
            echo JVM_Richtext_icons::render_view('uploader.php');
            echo JVM_Richtext_icons::render_view('icon-list.php');  
        ?>
    </div>
    <form action="https://www.paypal.com/donate" method="post" target="_top">
        <p style="max-width: 500px;">
            I am Joris van Montfort and I created and maintain the free plugin <strong>JVM Rich Text Icons</strong> for you. If you like using this plugin and want to keep this plugin free, please donate a small amount of money. Thank you!
        </p>
        <input type="hidden" name="hosted_button_id" value="VXZJG9GC34JJU" />
        <input type="image" src="https://www.paypalobjects.com/en_US/NL/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
        <img alt="" border="0" src="https://www.paypal.com/en_NL/i/scr/pixel.gif" width="1" height="1" />
    </form>

</div>