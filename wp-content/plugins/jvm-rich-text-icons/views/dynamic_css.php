<?php
$prefix_class = JVM_Richtext_icons::get_class_prefix();
?>
i.<?php echo $prefix_class;?> {
    width: 1em;
    display: inline-block;
    height: 1em;
    background-color: currentColor; 
    mask-repeat: no-repeat; 
    mask-repeat: no-repeat;
    -webkit-mask-repeat: no-repeat;
    mask-size:contain;
    -webkit-mask-size:contain;
    mask-position: 0% 50%;
    -webkit-mask-position: 0% 50%;
    white-space: break-spaces;
}


<?php 
    foreach ($files as $file) {
        $pi = pathinfo($file);
        
        $icon_class = sanitize_title($pi['filename']);
        $file_content = file_get_contents($file);
        $width = 0;
        $height = 0;
        $ratio = 1;

        $dom = new DOMDocument();
        $dom->load($file);
        $svg = $dom->getElementsByTagName('svg');
        if ($svg) {
            $viewBox = $svg[0]->getAttribute('viewBox');
            
            if ($viewBox) {
                list($x, $y, $width, $height) = explode(' ', $viewBox);

                //echo $width;    
                //echo $height;

                

                //echo 'ratio:'.$ratio."\n";  
            }else {
                // Might have width and height on svg
                $width = str_replace('px', '', $svg[0]->getAttribute('width'));
                $height = str_replace('px', '', $svg[0]->getAttribute('height'));
            }
            
            if (!empty($width) && !empty($height)) {
                $ratio = $width / $height;
            }
        }
            

?>
i.<?php echo $prefix_class;?>.<?php echo $icon_class;?> {
    <?php if ($ratio != 1) {
        echo 'width: '.$ratio.'em;'."\n";
    }?>
    --icon-bg: url("data:image/svg+xml;base64,<?php echo base64_encode($file_content);?>");
    -webkit-mask-image: var(--icon-bg);
    mask-image: var(--icon-bg);
}
<?php } ?>