/**
 * Admin JavaScript for DS Wine Guy
 * Handles media uploads for galleries, logos, and files
 */

(function($) {
    'use strict';

    $(document).ready(function() {
    
    /**
     * Producer Gallery Management
     */
    var galleryFrame;
    
    $('.dswg-add-images').on('click', function(e) {
        e.preventDefault();
        
        // If the media frame already exists, reopen it
        if (galleryFrame) {
            galleryFrame.open();
            return;
        }
        
        // Create the media frame
        galleryFrame = wp.media({
            title: 'Select Gallery Images',
            button: {
                text: 'Add to Gallery'
            },
            multiple: true
        });
        
        // When images are selected, run a callback
        galleryFrame.on('select', function() {
            var selection = galleryFrame.state().get('selection');
            var ids = $('#dswg_gallery_ids').val();
            var idsArray = ids ? ids.split(',') : [];
            
            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                
                // Add to array if not already there
                if (idsArray.indexOf(attachment.id.toString()) === -1) {
                    idsArray.push(attachment.id);
                    
                    // Add thumbnail to display
                    var imageHtml = '<div class="dswg-gallery-image" data-id="' + attachment.id + '">' +
                                  '<img src="' + attachment.sizes.thumbnail.url + '" />' +
                                  '<button type="button" class="dswg-remove-image">×</button>' +
                                  '</div>';
                    
                    $('.dswg-gallery-images').append(imageHtml);
                }
            });
            
            // Update hidden field
            $('#dswg_gallery_ids').val(idsArray.join(','));
        });
        
        // Open the modal
        galleryFrame.open();
    });
    
    // Remove gallery image
    $(document).on('click', '.dswg-remove-image', function(e) {
        e.preventDefault();
        
        var $image = $(this).closest('.dswg-gallery-image');
        var id = $image.data('id').toString();
        var ids = $('#dswg_gallery_ids').val();
        var idsArray = ids.split(',');
        
        // Remove from array
        var index = idsArray.indexOf(id);
        if (index > -1) {
            idsArray.splice(index, 1);
        }
        
        // Update hidden field
        $('#dswg_gallery_ids').val(idsArray.join(','));
        
        // Remove from display
        $image.remove();
    });
    
    /**
     * Wine Logo Upload
     */
    var logoFrame;
    
    $('.dswg-upload-logo').on('click', function(e) {
        e.preventDefault();
        
        // If the media frame already exists, reopen it
        if (logoFrame) {
            logoFrame.open();
            return;
        }
        
        // Create the media frame
        logoFrame = wp.media({
            title: 'Select Wine Logo',
            button: {
                text: 'Use as Logo'
            },
            multiple: false
        });
        
        // When an image is selected, run a callback
        logoFrame.on('select', function() {
            var attachment = logoFrame.state().get('selection').first().toJSON();
            
            // Update hidden field
            $('#dswg_wine_logo').val(attachment.id);
            
            // Handle SVGs and files without thumbnail sizes
            var imageUrl = attachment.sizes && attachment.sizes.thumbnail
                ? attachment.sizes.thumbnail.url
                : attachment.url;
            
            // Update preview
            $('.dswg-logo-preview').html('<img src="' + imageUrl + '" />');
            
            // Show remove button
            if ($('.dswg-remove-logo').length === 0) {
                $('.dswg-upload-logo').after('<button type="button" class="button dswg-remove-logo">Remove Logo</button>');
            }
        });
        
        // Open the modal
        logoFrame.open();
    });
    
    // Remove logo
    $(document).on('click', '.dswg-remove-logo', function(e) {
        e.preventDefault();
        
        $('#dswg_wine_logo').val('');
        $('.dswg-logo-preview').html('');
        $(this).remove();
    });
    
    /**
     * Wine Files Upload (PDFs, etc.)
     */
    var filesFrame;
    
    $('.dswg-add-files').on('click', function(e) {
        e.preventDefault();
        
        // If the media frame already exists, reopen it
        if (filesFrame) {
            filesFrame.open();
            return;
        }
        
        // Create the media frame
        filesFrame = wp.media({
            title: 'Select Files',
            button: {
                text: 'Add Files'
            },
            multiple: true
        });
        
        // When files are selected, run a callback
        filesFrame.on('select', function() {
            var selection = filesFrame.state().get('selection');
            var files = $('#dswg_wine_files').val();
            var filesArray = files ? files.split(',') : [];
            
            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                
                // Add to array if not already there
                if (filesArray.indexOf(attachment.id.toString()) === -1) {
                    filesArray.push(attachment.id);
                    
                    // Add file to display
                    var fileHtml = '<div class="dswg-file-item" data-id="' + attachment.id + '">' +
                                 '<span class="dashicons dashicons-media-document"></span>' +
                                 '<a href="' + attachment.url + '" target="_blank">' + attachment.filename + '</a>' +
                                 '<button type="button" class="button-link dswg-remove-file">Remove</button>' +
                                 '</div>';
                    
                    $('.dswg-files-list').append(fileHtml);
                }
            });
            
            // Update hidden field
            $('#dswg_wine_files').val(filesArray.join(','));
        });
        
        // Open the modal
        filesFrame.open();
    });
    
    // Remove file
    $(document).on('click', '.dswg-remove-file', function(e) {
        e.preventDefault();
        
        var $file = $(this).closest('.dswg-file-item');
        var id = $file.data('id').toString();
        var files = $('#dswg_wine_files').val();
        var filesArray = files.split(',');
        
        // Remove from array
        var index = filesArray.indexOf(id);
        if (index > -1) {
            filesArray.splice(index, 1);
        }
        
        // Update hidden field
        $('#dswg_wine_files').val(filesArray.join(','));
        
        // Remove from display
        $file.remove();
    });
    
    /**
     * Producer Logo Upload
     */
    var producerLogoFrame;
    
    $('.dswg-upload-producer-logo').on('click', function(e) {
        e.preventDefault();
        
        if (producerLogoFrame) {
            producerLogoFrame.open();
            return;
        }
        
        producerLogoFrame = wp.media({
            title: 'Select Producer Logo',
            button: {
                text: 'Use as Logo'
            },
            multiple: false
        });
        
        producerLogoFrame.on('select', function() {
            var attachment = producerLogoFrame.state().get('selection').first().toJSON();
            
            $('#dswg_producer_logo').val(attachment.id);
            
            // Handle both regular images and SVG
            var imageUrl = attachment.sizes && attachment.sizes.thumbnail 
                ? attachment.sizes.thumbnail.url 
                : attachment.url;
            
            $('.dswg-logo-preview').html('<img src="' + imageUrl + '" />');
            
            if ($('.dswg-remove-producer-logo').length === 0) {
                $('.dswg-upload-producer-logo').after('<button type="button" class="button dswg-remove-producer-logo">Remove Logo</button>');
            }
        });
        
        producerLogoFrame.open();
    });
    
    $(document).on('click', '.dswg-remove-producer-logo', function(e) {
        e.preventDefault();
        $('#dswg_producer_logo').val('');
        $('.dswg-logo-preview').html('');
        $(this).remove();
    });
    
    /**
     * Producer Files Upload
     */
    var producerFilesFrame;
    
    $('.dswg-add-producer-files').on('click', function(e) {
        e.preventDefault();
        
        if (producerFilesFrame) {
            producerFilesFrame.open();
            return;
        }
        
        producerFilesFrame = wp.media({
            title: 'Select Files',
            button: {
                text: 'Add Files'
            },
            multiple: true
        });
        
        producerFilesFrame.on('select', function() {
            var selection = producerFilesFrame.state().get('selection');
            var files = $('#dswg_producer_files').val();
            var filesArray = files ? files.split(',') : [];
            
            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                
                if (filesArray.indexOf(attachment.id.toString()) === -1) {
                    filesArray.push(attachment.id);
                    
                    var fileHtml = '<div class="dswg-file-item" data-id="' + attachment.id + '">' +
                                 '<span class="dashicons dashicons-media-document"></span>' +
                                 '<a href="' + attachment.url + '" target="_blank">' + attachment.filename + '</a>' +
                                 '<button type="button" class="button-link dswg-remove-producer-file">Remove</button>' +
                                 '</div>';
                    
                    $('.dswg-producer-files-list').append(fileHtml);
                }
            });
            
            $('#dswg_producer_files').val(filesArray.join(','));
        });
        
        producerFilesFrame.open();
    });
    
    $(document).on('click', '.dswg-remove-producer-file', function(e) {
        e.preventDefault();
        
        var $file = $(this).closest('.dswg-file-item');
        var id = $file.data('id').toString();
        var files = $('#dswg_producer_files').val();
        var filesArray = files.split(',');
        
        var index = filesArray.indexOf(id);
        if (index > -1) {
            filesArray.splice(index, 1);
        }
        
        $('#dswg_producer_files').val(filesArray.join(','));
        $file.remove();
    });
    
    /**
     * Geocoding from Address
     */
    $('#dswg_geocode_btn').on('click', function(e) {
        e.preventDefault();
        
        var address = $('#dswg_address').val();
        var $status = $('#dswg_geocode_status');
        var $btn = $(this);
        
        if (!address) {
            $status.html('<span style="color: #d63638;">Please enter an address first</span>');
            return;
        }
        
        $btn.prop('disabled', true).text('Getting coordinates...');
        $status.html('<span style="color: #2271b1;">Searching...</span>');
        
        // Use OpenStreetMap Nominatim API (free, no key required)
        $.ajax({
            url: 'https://nominatim.openstreetmap.org/search',
            data: {
                q: address,
                format: 'json',
                limit: 1
            },
            dataType: 'json',
            success: function(data) {
                if (data && data.length > 0) {
                    var lat = data[0].lat;
                    var lon = data[0].lon;
                    
                    $('#dswg_latitude').val(lat);
                    $('#dswg_longitude').val(lon);
                    
                    $status.html('<span style="color: #00a32a;">✓ Coordinates found!</span>');
                    
                    setTimeout(function() {
                        $status.html('');
                    }, 3000);
                } else {
                    $status.html('<span style="color: #d63638;">No coordinates found for this address</span>');
                }
            },
            error: function() {
                $status.html('<span style="color: #d63638;">Error contacting geocoding service</span>');
            },
            complete: function() {
                $btn.prop('disabled', false).text('Get Coordinates from Address');
            }
        });
    });
    
    /**
     * Make gallery images sortable
     */
    if (typeof $.fn.sortable !== 'undefined') {
        $('.dswg-gallery-images').sortable({
            items: '.dswg-gallery-image',
            cursor: 'move',
            update: function() {
                var ids = [];
                $('.dswg-gallery-image').each(function() {
                    ids.push($(this).data('id'));
                });
                $('#dswg_gallery_ids').val(ids.join(','));
            }
        });
    }
    
    }); // end document.ready

})(jQuery);
