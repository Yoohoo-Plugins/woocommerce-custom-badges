jQuery(function(){
    jQuery(document).ready(function(){
        jQuery(".emoji_button").on("click", function(event){
            event.preventDefault();
            const emoji_button = jQuery(this);
            emoji_button.parent().find('.emoji_popup').toggleClass('active');

        });

        const emoji_ref_array_html = emoji_ref_array.map((code) =>{
            return `<i class="em ${code}" data-emoji-code="em ${code}"></i>`;
        });

        jQuery('.emoji_selector_container .emoji_popup').html(emoji_ref_array_html.join(' '));

        jQuery('body').on('click', '.emoji_selector_container .emoji_popup .em', function(){
            const current_emoji = jQuery(this);
            const emoji_code = current_emoji.attr('data-emoji-code');
            const for_input = current_emoji.parent().attr('data-input-asso');

            if(jQuery('#' + for_input).length > 0){
                let the_value = jQuery('#' + for_input).val();
                jQuery('#' + for_input).val(the_value + "##" + emoji_code + "##");

                current_emoji.parent().removeClass('active');

                jQuery('#' + for_input).keyup();
            }
        });
    });
});