<?php
/**
 * Handles form submissions
 */
class submissionHandling
{
    const sandbox = FALSE;

    static private function submitEmail()
    {
        // Gets the form id
        $id = $_POST['formid'];

        // Initiates new form handling instance
        $f = new FormHandling;

        // Gets the client email list (to, bcc, cc)
        $clientEmailList = $f->getClientEmail($id);

        // Gets the customer email (if set)
        $customerEmail = $f->getCustomerEmail($_POST);

        // dd($customerEmail);

        // Checks if in sandbox mode before sending email
        if(FALSE == submissionHandling::sandbox){

            // New email instance
            $e = new emailHandling();

            // render and send client email(s)
            if($clientEmailList['to']){
                $e->sendEmail($id,'client',$clientEmailList['to'],'Enquiry received','Hi',$clientEmailList['headers']);
            }

            // render and send customer email
            if($customerEmail){
                $e->sendEmail($id,'customer',$customerEmail,'Thanks for getting in touch','We will be in touch soon');
            }

        }
    }

    public function storeSubmission()
    {
        
        global $wpdb;

        // Get and sanitize all post data
        $post = array_map("strip_tags",$_POST);

        // Get form id
        $id = $post['formid'];

        // Class for handling form
        $f = new FormHandling;
        
        // Compares post data with database columns so as to only use data defined when table created (removes extra field data not required for storage)
        $usefulData = $f->getUsefulData($id,$post);

        // Define table name
        $table_name = $wpdb->prefix.'eeforms_'.str_replace( '-', '_', sanitize_title_with_dashes( get_the_title($id) ) );

        // Checks if sandbox mode enabled
        if(FALSE == submissionHandling::sandbox){
            // Insert submission data
            $wpdb->insert(
                $table_name,
                $usefulData
            );
        }


        submissionHandling::submitEmail();
    }

}


  
add_action( 'admin_post_nopriv_catch_submission', array('submissionHandling','storeSubmission') );
add_action( 'admin_post_catch_submission', array('submissionHandling','storeSubmission') );
