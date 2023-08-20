(function ($) {
  // Show uploader?
  $('#add_icon_btn, #jvm-rich-text-icons_custom_icon_uploader .close').on('click', function(e){
    e.preventDefault();
    $('#jvm-rich-text-icons_custom_icon_uploader').toggle();
  });

  var $svgFileList = $('#svg-file-list');

  // Dropzone for uploader
  var $dismissErrors = $('#upload-dismiss-errors');
  var $uploadErrors = $('#upload-errors');
  var $uploadStatus = $('#media-uploader-status');
  var $uploadTotalProgress = $('#upload-progess-bar-inner');
  var $uploadIndex = $('#upload-index');
  var $uploadTotal = $('#upload-total');
  var $uploadFilename = $('#upload-filename');
  var iconDropZone = new Dropzone('#jvm-rich-text-icons_custom_icon_uploader', {
    paramName: "file", // The name that will be used to transfer the file
    maxFilesize: 2, // MB
    acceptedFiles: '.svg',
    clickable: '.browser.button',
    disablePreviews: true,
    maxFilesize: jvm_richtext_icon_settings.max_upload_size,
    parallelUploads: 1,
    error: function(file, message) {
      var error = '<div class="upload-error"><span class="upload-error-filename">'+file.upload.filename+'</span><span class="upload-error-message">'+message+'</span></div>';
      $uploadErrors.append(error);
      $uploadStatus.addClass('errors');
      $dismissErrors.show();
    },
    queuecomplete: function() {
      this.removeAllFiles();
      $uploadStatus.removeClass('uploading');
      $uploadTotalProgress.css({width: '0%'});
    },
    processing: function(file) {
      var index = 1;
      var files = this.getAcceptedFiles();
      // Get the index
      index = files.indexOf(file) +1;
      var totalFileCount = files.length;

      $uploadStatus.addClass('uploading');
      $uploadFilename.html(file.upload.filename);
      $uploadIndex.html(index);

      $uploadTotal.html(totalFileCount);      
      
      var totalUploadProgress = (index / totalFileCount) * 100;
      $uploadTotalProgress.css({width: totalUploadProgress+'%'});      
    },
    complete: function(file) {
      var res = JSON.parse(file.xhr.response);
      if (res.success) {
        var icon = '<a id="icon-dialog-link-'+res.icon_class+'" href="#icon-dialog" class="icon-dialog-link icon" data-icon-class-full="'+res.icon_class_full+'" data-icon-class="'+res.icon_class+'" data-file="'+res.file+'"><i class="icon '+res.icon_class_full+'" aria-hidden="true"> </i></a>\n';
        $svgFileList.prepend(icon);
        $svgFileList.show();
        $('#svg-file-list-empty').hide();

        // Refresh the css as we have a new icon in it.
        $('#jvm-rich-text-icons-svg-inline-css').html(res.css_code);
        
      }
    }
  });

  // Clear upload errors
  $dismissErrors.on('click', function(e) {
    e.preventDefault();
    $uploadErrors.html('');
    $dismissErrors.hide();
    $uploadStatus.removeClass('errors');
  });

  // initialize the dialog
  $('#icon-dialog').dialog({
    dialogClass: 'wp-dialog',
    autoOpen: false,
    draggable: false,
    width: '300px',
    modal: true,
    resizable: false,
    closeOnEscape: true,
    buttons:[
      {
        text: jvm_richtext_icon_settings.text.delete_icon,
        click: function() {
          //if (confirm(jvm_richtext_icon_settings.text.delete_icon_confirm)) {
            var className = $(this).data('icon-class');

            // Ajax call for delete file
            var data = {
              action : 'jvm-rich-text-icons-delete-icon',
              file : $(this).data('file')
            }
            $.ajax({
              type: "POST",
              url: jvm_richtext_icon_settings.ajax_url,
              data: data,
              success: function(r) {
                if (r.success) {
                  $('#icon-dialog-link-'+className).remove();
                  
                  if ($svgFileList.find('a').length == 0) {
                    $svgFileList.hide();
                    $('#svg-file-list-empty').show();
                  }
                }
              }
            });
          //}
          
          $(this).dialog( "close" );  
        }
      }
    ],
    position: {
      my: "center",
      at: "center",
      of: window
    },
    open: function () {
      // close dialog by clicking the overlay behind it
      $('.ui-widget-overlay').bind('click', function(){
        $('#my-dialog').dialog('close');
      })
    },
    create: function () {
      // style fix for WordPress admin
      $('.ui-dialog-titlebar-close').addClass('ui-button');
    },
  });

  // bind a button or a link to open the dialog
  //$('a.icon-dialog-link').click(function(e) {
  $svgFileList.on('click', 'a', function(e) {
    e.preventDefault();
    var $this = $(this);
    var href = $this.attr('href');;
    var $info = $(href);
    
    // Modify the title
    $info.dialog({
      title: $this.data('icon-class')
    });

    // Modify the icon preview
    $('#icon-dialog-preview').attr('class', $this.data('icon-class-full'));
    $info.data('file', $this.data('file'));
    $info.data('icon-class', $this.data('icon-class'));

    $info.dialog('open');
  });
})(jQuery);