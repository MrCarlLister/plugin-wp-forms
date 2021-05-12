<?php

class emailHandling
{
    function sendEmail($ID, $type=null, $to, $subject, $html, $headers=array())
    {
        if(null == $type)
        {
            $type = 'client';
        }

        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $html = $this->createEmail($ID,$type);

        // dd($to,true);
        wp_mail($to,$subject,$html,$headers);
    }

    function createEmail($ID,$type)
    {
        $header = $this->renderHeader($ID);
        $body = $this->renderBody($ID,$type);
        $footer = $this->renderfooter($ID);
        $html = $header.$body.$footer;

        return $html;
    }

    function renderLogo($ID)
    {
        return get_field('email_logo',$ID);

    }

    function renderHeader($ID)
    {
        $logo = (string) $this->renderLogo($ID);
        $logo = wp_upload_dir()['basedir'].substr($logo, strpos($logo, "/wp-content/uploads/") + 19);    
        $uid = 'logo';

        global $phpmailer;
        add_action( 'phpmailer_init', function(&$phpmailer)use($logo,$uid){
            $phpmailer->SMTPKeepAlive = true;
            $phpmailer->AddEmbeddedImage($logo, $uid);
        });

        $hex = get_field('email_header_colour',$ID);
        $header = '
        <!doctype html>
            <html>
                
                <body class="">
                <span class="preheader">preheader here</span>
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body" style="width: 100%;">
                    <tr>
                    <td>&nbsp;</td>
                    <td class="container">
                        <div class="content">
                        
                        <!-- START HEADER -->
                        <div class="header" style="background-color:'.$hex.';clear: both;
                        margin-top: 10px;
                        text-align: center;
                        width: 100%;">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                            <tbody><tr>
                            <td class="content-block" style="padding-bottom: 10px;
                            padding-top: 10px;">
                                <img src="cid:logo" width="208" height="208">
                    
                            </td>
                            </tr>
                        </tbody></table>
                        </div>
                        <!-- END HEADER -->';

        return $header;
    }

    function renderFooter($ID)
    {
        // $footer = '<!-- START FOOTER -->
        // <div class="footer" style="background-color:black;padding:40px 0;">
        //     <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin:auto;">
        //     <tr>
        //         <td style="width:180px;"></td><td>asdsds</td><td style="width:180px;"></td>
        //     </tr>
        //     </table>
        // </div>
        // <!-- END FOOTER -->';

        $footer = '';

        return $footer;
    }

    function renderBody($ID,$type)
    {
        $formName = get_the_title($ID);
        $content = '';

        if($type == 'client')
        {

            // Santizes the _POST DATA
            $post = array_map("strip_tags",$_POST);
            
            // New instance for form handling, could also use (new FormHandling)->method()
            $f = new FormHandling();

            // Retrieves the admin url for the form submissions page
            $formSubmissionURL = $f->getFormPageURL($ID);

            // Creates a message
            $message = '<h1 style="line-height:1;text-align:center;">`'.$formName.'` enquiry received from `'.get_bloginfo().'`</h1>
            <p style="text-align:center;">You\'ve received an enquiry from the `'.$formName.'` form, details below. Alternatively, <a href="'.$formSubmissionURL.'">login</a> to view all enquiries for this form.</p>';

            
            // Compares post data with database columns so as to only use data defined when table created (removes extra field data not required for storage)
            $customerData = $f->getUsefulData($ID,$post);

            if(!empty($customerData)):
                ob_start();

                echo '<tr>
                        <td class="wrapper" style="box-sizing: border-box;
                        padding: 20px;">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                            <tr>
                                <td>
                                    <h2 style="text-align:center;">Customer enquiry details</h2>
                                </td>
                            </tr>
                            <tr>
                            <td>';
                            foreach($customerData as $key=>$val){
                                echo '<p style="display:block;text-align:center;"><strong>'.$key.'</strong>: '.$val.'</p>';
                            }
                            echo '
                            </td>
                            </tr>
                        </table>
                        </td>
                    </tr>';

                $content = ob_get_contents();

                ob_end_clean();
            endif;
        } else {
            $message = get_field('customer_email_message') ?? 'Thank you for your email, we\'ll be in touch soon.';
            $message = '<h1 style="line-height:1;text-align:center;">Thank you.</h1>
            <p style="text-align:center;">'.$message.'</p>';
        };

        
        $body = '
        <!-- START CENTERED WHITE CONTAINER -->
        <table role="presentation" class="main" style="width: 100%;">

          <!-- START MAIN CONTENT AREA -->
          <tr>
            <td class="wrapper" style="box-sizing: border-box;
            padding: 20px;">
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                <tr>
                  <td>
                    '.$message.'
                  </td>
                </tr>
              </table>
            </td>
          </tr>'.
          $content
          .'
        <!-- END MAIN CONTENT AREA -->
        </table>
        <!-- END CENTERED WHITE CONTAINER -->';
        return $body;
    }
}

