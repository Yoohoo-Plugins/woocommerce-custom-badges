jQuery(function(){
    jQuery(window).load(function(){
        ycb_eval_url_hash();
    });

    jQuery(document).ready(function(){
        jQuery('#toplevel_page_ycb_main_menu ul li').on('click', function(){
            setTimeout(ycb_eval_url_hash, 400);
        });

        jQuery(".yoohoo_wrap .tab-container .tab-clickable").on("click", function(){
            jQuery(".tab-clickable").removeClass('active');
            jQuery(this).addClass('active');

            let target_tab = jQuery(this).attr('data-tab-id');
            jQuery("[id^=tab_]").hide();
            jQuery("#" + target_tab).show();

            window.location.hash = target_tab;
        });

        jQuery('#ycb_badge_shape').on('change', function(){
            let current_val = parseInt(jQuery(this).val());
            jQuery('.badge_shape_preview').removeClass('circle').removeClass('square').removeClass('rounded').removeClass('square_wide');

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
                case 3:
                    jQuery('.badge_shape_preview').addClass('square_wide');
                    break;
            }
        });
    });

    function ycb_eval_url_hash(){
        if(typeof window.location.hash !== 'undefined' && window.location.hash !== ''){
            let active_tab = window.location.hash.replace('#', '');
            jQuery('.tab-clickable[data-tab-id=' + active_tab + ']').click();

            if(jQuery('.tab-clickable[data-tab-id=' + active_tab + ']').length > 0 && active_tab !== 'tab_1'){
                jQuery('#toplevel_page_ycb_main_menu ul li').removeClass('current');
                jQuery('#toplevel_page_ycb_main_menu ul').find('a[href*=' + active_tab + ']').parent().addClass('current');
            }
        };
    }
});