jQuery(function(){
    jQuery(document).ready(function(){
        jQuery(".yoohoo_wrap .tab-container .tab-clickable").on("click", function(){
            jQuery(".tab-clickable").removeClass('active');
            jQuery(this).addClass('active');

            let target_tab = jQuery(this).attr('data-tab-id');
            jQuery("[id^=tab_]").hide();
            jQuery("#" + target_tab).show();
        });

        jQuery('#ycb_badge_shape').on('change', function(){
            let current_val = parseInt(jQuery(this).val());
            jQuery('.badge_shape_preview').removeClass('circle').removeClass('square').removeClass('rounded');

            switch(current_val){
                case 0:
                    jQuery('.badge_shape_preview').addClass('circle');
                    break;
                case 1:
                    jQuery('.badge_shape_preview').addClass('square');
                    break;
                case 2:
                    jQuery('.badge_shape_preview').addClass('rounded');
                    break;
            }
        });
    });
});