jQuery(function(){
    jQuery(window).load(function(){
        ycb_process_full_width_banners();
    });

    jQuery(window).on('resize', function(){
        ycb_process_full_width_banners();
    });

    function ycb_process_full_width_banners(){
        if(jQuery('.yoohoo_badge_square_wide').length > 0){
            let gallery_width = jQuery('.yoohoo_badge_square_wide').next('.woocommerce-product-gallery').width();
            jQuery('.yoohoo_badge_square_wide').css('width', gallery_width + "px");

            let related_images_width = jQuery('.products .yoohoo_badge_square_wide').next('img').width();
            jQuery('.products .yoohoo_badge_square_wide').css('width', related_images_width + "px");
        }
    }
});