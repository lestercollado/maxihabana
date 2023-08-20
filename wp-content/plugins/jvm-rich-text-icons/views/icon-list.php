<?php
$files = JVM_Richtext_icons::get_svg_file_list();
?>
<div id="svg-file-list"<?php echo empty($files) ? ' style="display:none;"' : '';?>>
<?php
    $css_class = JVM_Richtext_icons::get_class_prefix();
    foreach ($files as $file) {
        $pi = pathinfo($file);
        
        $icon_class = sanitize_title($pi['filename']);

        echo '<a id="icon-dialog-link-'.$icon_class.'" href="#icon-dialog" class="icon-dialog-link icon" data-icon-class-full="'.$css_class . ' ' . $icon_class .'" data-icon-class="'. $icon_class .'" data-file="'.esc_js(basename($file)).'">';
        echo '<i class="' . $css_class . ' ' . $icon_class . '" aria-hidden="true"> </i>';
        echo '</a>'."\n";
    }
?>
</div>
<div id="icon-dialog" >
    <div style="font-size:72px;text-align:center;">
        <i id="icon-dialog-preview" aria-hidden="true"> </i>
    </div>
</div>
<p id="svg-file-list-empty" <?php echo empty($files) ? '' : 'style="display:none;"';?>><?php _e('No custom icons have been uploaded. Please upload some SVG files to create your custom icon set.', 'jvm-richt-text-icons');?></p>
