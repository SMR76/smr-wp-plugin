<?php
/**
 * @package smr plugin
 */


namespace Src\Elements;

use \Src\Base\BaseController;

class CustomFooter extends BaseController{ 
    public function register() {
        add_action( 'after_woodmart_footer_content', array($this, 'footerContent'));
    }
    
    public function footerContent() {
        wp_register_script('lottiePlayer',"$this->pluginUrl/assets/js/lottie-player.js");
        wp_enqueue_script('lottiePlayer');
        
        //---------------------- custom footer html start ----------------------
        ?>
            <div class="row" id="custom_row_bottom_section" style="justify-content: center;margin-bottom: 50px;" >
                <a referrerpolicy="origin" target="_blank" href="https://trustseal.enamad.ir/?id=213194&amp;Code=ISG7iXEnj2oPNlwOlRVW" style="background-color: #ffffff87;border-radius: 15px;margin-left: 10px;width:136px;">
                <img referrerpolicy="origin" src="https://Trustseal.eNamad.ir/logo.aspx?id=213194&amp;Code=ISG7iXEnj2oPNlwOlRVW" alt="" style="cursor:pointer;margin-top: 30px;" id="ISG7iXEnj2oPNlwOlRVW">
                </a>
                
                <a href="https://<?php echo $_SERVER['SERVER_NAME'];?>" style="background-color: #ffffff87;border-radius: 15px;">
                    <lottie-player id="HSG-lottie" src="http://localhost/hamtabiz_test/wp-content/uploads/2021/07/HSG-Lottie-Logo.json" background="transparent" speed="1" style="width: 136px; height: 136px;"></lottie-player>
                </a>
                
                <script>
                    jQuery(document).ready(function(){
                        var player = jQuery('#HSG-lottie')[0];
                        setTimeout(()=>{player.play();},5000);     //enable animation after 10 sec

                        player.addEventListener('complete',()=>{
                            player.seek("100%");
                            setInterval(()=>{player.play();},5000);    //repeat animation every 50 sec
                        });
                    });
                </script>
            </div>    
        <?php
        //---------------------- custom footer html ends ----------------------
    }
}
?>
