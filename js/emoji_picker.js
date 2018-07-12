jQuery(function(){
    jQuery(document).ready(function(){
        jQuery(".emoji_button").on("click", function(event){
            event.preventDefault();

            if(!jQuery(".emoji_search_input").is(":focus")){
                const emoji_button = jQuery(this);
                emoji_button.parent().find('.emoji_popup').toggleClass('active');
            }
        });

        const emoji_ref_array_html = emoji_ref_array.map((code) =>{
            return `<i class="em ${code}" data-emoji-code="em ${code}"></i>`;
        });

        const fa_ref_array_html = fa_ref_array.map((code) =>{
            return `<i class="em ${code}" data-fa-code="em ${code}"></i>`;
        });

        const popup_html = `
            <strong>Emoji:</strong><hr>
            ${emoji_ref_array_html.join(' ')}

            <br><br><strong>FontAwesome:</strong><hr>
            ${fa_ref_array_html.join(' ')}
        `;


        jQuery('.emoji_selector_container .emoji_popup').html(popup_html);

        jQuery('body').on('click', '.emoji_selector_container .emoji_popup .em', function(){
            const current_emoji = jQuery(this);
            const emoji_code = current_emoji.attr('data-emoji-code');
            const fa_code = current_emoji.attr('data-fa-code');
            const for_input = current_emoji.parent().attr('data-input-asso');

            if(jQuery('#' + for_input).length > 0){
                const addition = emoji_code !== undefined ? "##" + emoji_code + "##" : "%%" + fa_code + "%%";

                let the_value = jQuery('#' + for_input).val();
                jQuery('#' + for_input).val(the_value + addition);
                current_emoji.parent().removeClass('active');
                jQuery('#' + for_input).keyup();
            }
        });

        jQuery('body').on('keyup', '.emoji_search_input', function(e){
            let code = e.which;
            jQuery('.emoji_button').blur();
            if(code == 13){
                e.preventDefault();
                let input_value = jQuery(this).val().toLowerCase().trim();

                let all_selectables = jQuery(this).parent().prev().find('i');

                if(input_value !== ""){
                    all_selectables.hide();

                    jQuery(all_selectables).each(function(){
                        let current_content = jQuery(this).attr('class');
                        if(current_content.toLowerCase().indexOf(input_value) !== -1){
                            jQuery(this).show();
                        }
                    });
                } else {
                    all_selectables.show();
                }
            }
        });
    });
});