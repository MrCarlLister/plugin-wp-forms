<?php
if ( class_exists('ACF') ) {

add_action( 'wp_enqueue_scripts', function(){


  wp_register_script('formValidation', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery'), null, true);

  wp_localize_script( 'formValidation', 'wpAdmin', array(
      'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php'
  ) );
  });


/**
* Create a table
*
* @param [type] $table_name
* @param [type] $array
* @return void
*/
function ee__create_new_table($table_name,$array)
{
 
  global $wpdb;
  include_once ABSPATH . '/wp-admin/includes/upgrade.php';
  $table_charset = '';
  $prefix = $wpdb->prefix.'ee_';
  $users_table = $prefix . $table_name;

  
  if ($wpdb->has_cap('collation')) {
      if (!empty($wpdb->charset)) {
          $table_charset = "DEFAULT CHARACTER SET {$wpdb->charset}";
      }
      if (!empty($wpdb->collate)) {
          $table_charset .= " COLLATE {$wpdb->collate}";
      }
  }

  $specifics='';

  foreach($array as $key => $value){

    /**
     * Backtick used for input names with spaces
     */
    $specifics .= '`'.$key.'`'.' TEXT NOT NULL, ';
  }

  $statement = "CREATE TABLE {$users_table} (id int(11) NOT NULL auto_increment, {$specifics} Date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id)) ENGINE = MyISAM {$table_charset};";
  // echo json_encode(array('success' => $statement)); die();
  maybe_create_table($users_table, $statement);
}

/**
* Updates a table
*
* @param [type] $table_name
* @param [type] $enq
* @return void
*/
function ee__update_table($table_name,$enq)
{
  global $wpdb;
  $prefix = $wpdb->prefix;
 
  $wpdb->insert(
      $prefix .'eeforms_'. $table_name,
      $enq
  );
}


//
// ──────────────────────────────────────────────────── I ──────────
//   :::::: E M A I L S : :  :   :    :     :        :          :
// ──────────────────────────────────────────────────────────────
//



function ee__email_header()
{



$header = '<!-- START HEADER -->
<div class="header" style="background-color:#2b3af9;clear: both;
margin-top: 10px;
text-align: center;
width: 100%;">
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
    <tbody><tr>
      <td class="content-block" style="padding-bottom: 10px;
      padding-top: 10px;">
        <img src="cid:company-logo" width="208" height="208">

      </td>
    </tr>
  </tbody></table>
</div>
<!-- END HEADER -->';

return $header;
}

function ee__email_get_style()
{
return '<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Simple Transactional Email</title>
<style>
  /* -------------------------------------
      GLOBAL RESETS
  ------------------------------------- */
  
  /*All the styling goes here*/
  
  img {
    border: none;
    -ms-interpolation-mode: bicubic;
    max-width: 100%; 
  }

  body {
    background-color: #f6f6f6;
    font-family: sans-serif;
    -webkit-font-smoothing: antialiased;
    font-size: 14px;
    line-height: 1.4;
    margin: 0;
    padding: 0;
    -ms-text-size-adjust: 100%;
    -webkit-text-size-adjust: 100%; 
  }

  table {
    border-collapse: separate;
    mso-table-lspace: 0pt;
    mso-table-rspace: 0pt;
    width: 100%; }
    table td {
      font-family: sans-serif;
      font-size: 14px;
      vertical-align: top; 
  }

  /* -------------------------------------
      BODY & CONTAINER
  ------------------------------------- */

  .body {
    background-color: #f6f6f6;
    width: 100%; 
  }

  /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
  .container {
    display: block;
    margin: 0 auto !important;
    /* makes it centered */
    max-width: 580px;
    padding: 10px;
    width: 580px; 
  }

  /* This should also be a block element, so that it will fill 100% of the .container */
  .content {
    box-sizing: border-box;
    display: block;
    margin: 0 auto;
    max-width: 580px;
    padding: 10px; 
  }

  /* -------------------------------------
      HEADER, FOOTER, MAIN
  ------------------------------------- */
  .main {
    background: #ffffff;
    border-radius: 3px;
    width: 100%; 
  }

  .wrapper {
    box-sizing: border-box;
    padding: 20px; 
  }

  .content-block {
    padding-bottom: 10px;
    padding-top: 10px;
  }

  .header
  {
    clear: both;
    margin-top: 10px;
    text-align: center;
    width: 100%;
    background-color:#2B3AF9;
  }

  .footer {
    clear: both;
    margin-top: 10px;
    text-align: center;
    width: 100%; 
  }
    .footer td,
    .footer p,
    .footer span,
    .footer a {
      color: #999999;
      font-size: 12px;
      text-align: center; 
  }

  /* -------------------------------------
      TYPOGRAPHY
  ------------------------------------- */
  h1,
  h2,
  h3,
  h4 {
    color: #000000;
    font-family: sans-serif;
    font-weight: 400;
    line-height: 1.4;
    margin: 0;
    margin-bottom: 30px; 
  }

  h1 {
    font-size: 35px;
    font-weight: 300;
    text-align: center;
  }

  p,
  ul,
  ol {
    font-family: sans-serif;
    font-size: 14px;
    font-weight: normal;
    margin: 0;
    margin-bottom: 15px; 
  }
    p li,
    ul li,
    ol li {
      list-style-position: inside;
      margin-left: 5px; 
  }

  a {
    color: #3498db;
    text-decoration: underline; 
  }

  /* -------------------------------------
      BUTTONS
  ------------------------------------- */
  .btn {
    box-sizing: border-box;
    width: 100%; }
    .btn > tbody > tr > td {
      padding-bottom: 15px; }
    .btn table {
      width: auto; 
  }
    .btn table td {
      background-color: #ffffff;
      border-radius: 5px;
      text-align: center; 
  }
    .btn a {
      background-color: #ffffff;
      border: solid 1px #3498db;
      border-radius: 5px;
      box-sizing: border-box;
      color: #3498db;
      cursor: pointer;
      display: inline-block;
      font-size: 14px;
      font-weight: bold;
      margin: 0;
      padding: 12px 25px;
      text-decoration: none;
      text-transform: capitalize; 
  }

  .btn-primary table td {
    background-color: #3498db; 
  }

  .btn-primary a {
    background-color: #3498db;
    border-color: #3498db;
    color: #ffffff; 
  }

  /* -------------------------------------
      OTHER STYLES THAT MIGHT BE USEFUL
  ------------------------------------- */
  .last {
    margin-bottom: 0; 
  }

  .first {
    margin-top: 0; 
  }

  .align-center {
    text-align: center; 
  }

  .align-right {
    text-align: right; 
  }

  .align-left {
    text-align: left; 
  }

  .clear {
    clear: both; 
  }

  .mt0 {
    margin-top: 0; 
  }

  .mb0 {
    margin-bottom: 0; 
  }

  .preheader {
    color: transparent;
    display: none;
    height: 0;
    max-height: 0;
    max-width: 0;
    opacity: 0;
    overflow: hidden;
    mso-hide: all;
    visibility: hidden;
    width: 0; 
  }

  .powered-by a {
    text-decoration: none; 
  }

  hr {
    border: 0;
    border-bottom: 1px solid #f6f6f6;
    margin: 20px 0; 
  }

  /* -------------------------------------
      RESPONSIVE AND MOBILE FRIENDLY STYLES
  ------------------------------------- */
  @media only screen and (max-width: 620px) {
    table[class=body] h1 {
      font-size: 28px !important;
      margin-bottom: 10px !important; 
    }
    table[class=body] p,
    table[class=body] ul,
    table[class=body] ol,
    table[class=body] td,
    table[class=body] span,
    table[class=body] a {
      font-size: 16px !important; 
    }
    table[class=body] .wrapper,
    table[class=body] .article {
      padding: 10px !important; 
    }
    table[class=body] .content {
      padding: 0 !important; 
    }
    table[class=body] .container {
      padding: 0 !important;
      width: 100% !important; 
    }
    table[class=body] .main {
      border-left-width: 0 !important;
      border-radius: 0 !important;
      border-right-width: 0 !important; 
    }
    table[class=body] .btn table {
      width: 100% !important; 
    }
    table[class=body] .btn a {
      width: 100% !important; 
    }
    table[class=body] .img-responsive {
      height: auto !important;
      max-width: 100% !important;
      width: auto !important; 
    }
  }

  /* -------------------------------------
      PRESERVE THESE STYLES IN THE HEAD
  ------------------------------------- */
  @media all {
    .ExternalClass {
      width: 100%; 
    }
    .ExternalClass,
    .ExternalClass p,
    .ExternalClass span,
    .ExternalClass font,
    .ExternalClass td,
    .ExternalClass div {
      line-height: 100%; 
    }
    .apple-link a {
      color: inherit !important;
      font-family: inherit !important;
      font-size: inherit !important;
      font-weight: inherit !important;
      line-height: inherit !important;
      text-decoration: none !important; 
    }
    #MessageViewBody a {
      color: inherit;
      text-decoration: none;
      font-size: inherit;
      font-family: inherit;
      font-weight: inherit;
      line-height: inherit;
    }
    .btn-primary table td:hover {
      background-color: #34495e !important; 
    }
    .btn-primary a:hover {
      background-color: #34495e !important;
      border-color: #34495e !important; 
    } 
  }

</style>
</head>';
}

function ee__email_footer()
{
$social = '';
$platforms = get__social_media_plats();

if($platforms):
  foreach ($platforms as $plat) {
      $social .= '<td style="padding: 0 5px;text-align: center;font-family: sans-serif;font-size: 12px;vertical-align: top;color: #999999;"><a href="' . $plat['link'] . '" target="_blank"><img src="cid:social-'.$plat['platform'].'" style="width:40px;height:auto;" /></a></td>';
  }
endif;

$footer = '<!-- START FOOTER -->
<div class="footer" style="background-color:black;padding:40px 0;">
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin:auto;">
    <tr>
      <td style="width:180px;"></td>'.$social.'<td style="width:180px;"></td>
    </tr>
  </table>
</div>
<!-- END FOOTER -->';

return $footer;
}

function ee__email_customer($data ='',$type ='')
{
$html = '<!doctype html>
<html>
  
  <body class="">
    <span class="preheader">'.get_field('global--customer-email','options')['customer--email-message'].'</span>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body" style="width: 100%;">
      <tr>
        <td>&nbsp;</td>
        <td class="container">
          <div class="content">

          '.
              ee__email_header()
            .'
            

            <!-- START CENTERED WHITE CONTAINER -->
            <table role="presentation" class="main" style="width: 100%;">

              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="wrapper" style="box-sizing: border-box;
                padding: 20px;">
                  <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                    <tr>
                      <td>
                        <h1 style="line-height:1;text-align:center;">'.get_field('global--customer-email','options')['customer--email-headline'].'</h1>
                        <p style="text-align:center;">'.get_field('global--customer-email','options')['customer--email-message'] .'</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

            <!-- END MAIN CONTENT AREA -->
            </table>
            <!-- END CENTERED WHITE CONTAINER -->

            '.
              ee__email_footer()
            .'

          </div>
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>
  </body>
</html>';

return $html;
}

function ee__email_client($data)
{
$html = '<!doctype html>
<html>
  <body class="">
    <span class="preheader">You`ve received a form submission.</span>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td>&nbsp;</td>
        <td class="container">
          <div class="content">

          '.
              ee__email_header()
            .'

            <!-- START CENTERED WHITE CONTAINER -->
            <table role="presentation" class="main">

              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="wrapper" style="box-sizing: border-box;
                padding: 20px;">
                  <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td>
                        <h1 style="line-height:1;">You`ve received a form submission</h1>
                        <p  style="text-align:center;">You can access all submissions <a href="'.get_admin_url(null,'/admin.php?page=ee_list_form_submissions').'">here</a>, or you can view them below:</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td class="wrapper" style="box-sizing: border-box;
                padding: 20px;">
                  <table role="presentation" border="0" cellpadding="0" cellspacing="0">';

                  foreach($data as $label => $value):
                    $html .= '<tr><td>'.$label . '</td><td>'. $value .'</td></tr>';
                  endforeach;
                  
                  $html .= '
                  </table>
                </td>
              </tr>

            <!-- END MAIN CONTENT AREA -->
            </table>
            <!-- END CENTERED WHITE CONTAINER -->

            '.
              ee__email_footer()
            .'

          </div>
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>
  </body>
</html>';

return $html;
}


add_action( 'wp_ajax_nopriv_ee__form_validate', 'ee__form_validate' );
add_action( 'wp_ajax_ee__form_validate', 'ee__form_validate' );

function ee__form_validate() {

//
// ────────────────────────────────────────────────────────────────── I ──────────
//   :::::: D A T A   H A N D L I N G : :  :   :    :     :        :          :
// ────────────────────────────────────────────────────────────────────────────
//


// ARRAY OF KEYS TO UNSET
$array_keys = array('token', 'g-recaptcha-response','RECAPT_SITE','action');

$captcha = $_POST['token']; // STORE THE TOKEN


foreach ($array_keys as $key) {
// UNSET ARRAY KEYS SPECIFIED; AKA INFO NOT FOR DATA STORAGE
unset($_POST[$key]);
}

foreach ($_POST as $key => $value) {
// STRING REPLACE FOR KEYS
// $key = str_replace('-', ' ', $key);
$customer_data[$key] = $value;
}




//
// ──────────────────────────────────────────────────────────────────────────────────── II ──────────
//   :::::: R E C A P T C H A   A N D   S E C U R I T Y : :  :   :    :     :        :          :
// ──────────────────────────────────────────────────────────────────────────────────────────────
//


$secretKey = RECAPT_SECRET;
$ip = $_SERVER['REMOTE_ADDR'];

// post request to server
$url = 'https://www.google.com/recaptcha/api/siteverify';
$data = array('secret' => $secretKey, 'response' => $captcha);

$options = array(
'http' => array(
  'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
  'method'  => 'POST',
  'content' => http_build_query($data)
)
);

$context  = stream_context_create($options);
$response = file_get_contents($url, false, $context);
$responseKeys = json_decode($response, true);

$threshold = 0.5; // threshold for recaptcha pass


header('Content-type: application/json');

if ($responseKeys["score"] > $threshold) { // acheived threshold


//
// ──────────────────────────────────────────────────────────────────────────────────────────────────────────── III ──────────
//   :::::: C R E A T E   T A B L E   A N D   S T O R E   I N F O R M A T I O N : :  :   :    :     :        :          :
// ──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────
//

 
ee__create_new_table('contact', $customer_data);
ee__update_table('contact', $customer_data);

//
// ──────────────────────────────────────────────────────────────────── IV ──────────
//   :::::: E M A I L  : :  :   :    :     :        :          :
// ──────────────────────────────────────────────────────────────────────────────
//


/**
 * General email details
 */

$form_type = $_POST['label'];
// $form_type = 'signup';
$customer_email = $_POST['email'];
$client_email = get__email();
$customer_name = $_POST['name'];

$headers = array('Content-Type: text/html; charset=UTF-8');

$platforms = get__social_media_plats();
$files[] = array('company-logo','cwd-logo.jpg',dirname(__FILE__).'/img/logo--white.png');

if($platforms):
  foreach ($platforms as $plat) {
      $files[] = array('social-'.$plat['platform'],'social-'.$plat['platform'].'.png',dirname(__FILE__).'/img/icon--'.$plat['platform'].'.png');
  }
endif;

global $phpmailer;
add_action( 'phpmailer_init', function(&$phpmailer)use($files){
    $phpmailer->SMTPKeepAlive = true;
    // $phpmailer->AddEmbeddedImage($file, $uid, $name);
    foreach($files as $item):
      echo json_encode(array('success' => $item));
      $phpmailer->AddEmbeddedImage($item[2], $item[0], $item[1]);
    endforeach;
});


/**
 * Customer mailing details
 */
$customer_subject = get_field('global--customer-email','options')['customer--email-subject'] ?? "We've got it!";
$customer_message = ee__email_customer($customer_data);


/**
 * Client mailing details
 */

  $client_subject = "You've received a '".$form_type."' form submission from ".$customer_name; 
  $client_message = ee__email_client($customer_data);


/**
 * Customer email
 */
// wp_mail( $customer_email, $customer_subject, $customer_message, $headers );

/**
 * CWD email
 */
wp_mail( $client_email, $client_subject, $client_message, $headers );


echo json_encode(array('success' => true));
die();
}
}

}