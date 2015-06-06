//Media Uploader Javascript
jQuery(document).ready(function($) {
    $(document).on("click", ".pt_image_button", function() {

        jQuery.data(document.body, 'prevElement', $(this).prev());

        window.send_to_editor = function(html) {

            if ( html.indexOf("img") > -1 ){
                var imgurl = jQuery('img',html).attr('src');
            }else{
                var imgurl = $(html).attr('href');
            }

            var inputText = jQuery.data(document.body, 'prevElement');

            if(inputText != undefined && inputText != '')
            {
                inputText.val(imgurl);
            }

            tb_remove();
        };

        tb_show('', 'media-upload.php?type=image&TB_iframe=true');
        return false;
    });
});