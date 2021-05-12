<?php
/**
 * Handles form submissions
 */
class submissionHandling
{
    const sandbox = FALSE;

    
    static private function redirect() {
        return $_POST['redirect'] ?? '/thank-you';
    }

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

        header('Location: '. submissionHandling::redirect());
    }

    public function uploadAndStoreFiles($file){
        
        // Create a random string for folder structure
        $hash = uniqid('',true);
        
        // Gets uploads dir
        $wp_uploads = wp_upload_dir(null,false);

        // Sets the target directory
        $target_dir = $wp_uploads['basedir'].'/ee_forms/'.$hash.'/';

        // Creates directory (if it doesn't exist)
        wp_mkdir_p( $target_dir );

        // Sets target file path
        $target_file = $target_dir .  basename($file["name"]);

        // Sets image upload to 1 (uploaded) by default
        $uploadOk = 1;

        // Gets the file type
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));


        // Check if file already exists
        if (file_exists($target_file)) {
            $file =  "Sorry, file already exists – File not uploaded";  // sets message to be stored, sent in email to customer
            $uploadOk = 0; // sets to upload error
        }

        // Check file size
        if ($file["size"] > 4000000) {
            $file =  "Sorry, your file is too large – File not uploaded";  // sets message to be stored, sent in email to customer
            $uploadOk = 0; // sets to upload error
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "pdf" && $imageFileType != "gif" ) {
            $file =  "Sorry, only JPG, JPEG, PNG & GIF files are allowed – File not uploaded";  // sets message to be stored, sent in email to customer
            $uploadOk = 0; // sets to upload error
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                $file = '<a href="'.$wp_uploads['baseurl'].'/ee_forms/'.$hash.'/'.$file["name"].'">Link to file</a>'; // sets message to be stored, sent in email to customer
            } else {
                $file = "Sorry, there was an error uploading your file."; // sets message to be stored, sent in email to customer
            }
        }

        return $file;


    }

    public function storeSubmission()
    {
        
        global $wpdb;

        // Get and sanitize all post data
        $post = array_map("strip_tags",$_POST);
        
        // Handle files
        if($_FILES):
        
            // Create empty files array in prep
            $files = array();

            // Loop through files
            foreach($_FILES as $key => $file):

                // Set files key to input name, return will either be link to file or string with error
                $files[$key] = submissionHandling::uploadAndStoreFiles($file);

            endforeach;

            // Merge the rest of the post data with new files array
            $post = array_merge($post,$files);

        endif;


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
