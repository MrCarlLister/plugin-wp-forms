<?php

class FormHandling {

    /**
     * Creates a table matching the columns set
     *
     * @param int $ID post id of the form create
     * @return void
     */
    public function createFormTable($ID)
    {
      $slug = str_replace( '-', '_', sanitize_title_with_dashes( get_the_title($ID) ) );
      $column_headings = $this->getAllColumns($ID);

      global $wpdb;
      include_once ABSPATH . '/wp-admin/includes/upgrade.php';
      $table_charset = '';
      $prefix = $wpdb->prefix.'eeforms_';
      $users_table = $prefix . $slug;
    
      
      if ($wpdb->has_cap('collation')) {
          if (!empty($wpdb->charset)) {
              $table_charset = "DEFAULT CHARACTER SET {$wpdb->charset}";
          }
          if (!empty($wpdb->collate)) {
              $table_charset .= " COLLATE {$wpdb->collate}";
          }
      }
    
      $specifics='';
    
      foreach($column_headings as $column=>$type){
    
        /**
         * Backtick used for input names with spaces
         */
        $specifics .= '`'.$column.'`'.' '.$type.' NOT NULL, ';
      }
    
      $statement = "CREATE TABLE {$users_table} (id int(11) NOT NULL auto_increment, {$specifics} Date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id)) ENGINE = MyISAM {$table_charset};";
      maybe_create_table($users_table, $statement);

    }
  
  
    public function editFormTable()
    {
      //
    }

    public function getFormPageURL($ID)
    {
      return get_site_url(null,'/wp-admin/edit.php?post_type=eeforms&page='.$ID);
    }

    public function getFieldObj($ID)
    {
       //
       $array = array();

       // Check rows exists.
       if( have_rows('input',$ID) ):
 
         // Loop through rows.
         while( have_rows('input',$ID) ) : the_row();
            $type = get_sub_field('input_type');
            $options = null;

            $fieldList = array(
              'select','radio','checkbox'
            );

            if(in_array($type,$fieldList)){
              $options = get_sub_field('options');
            }
             // Load sub field value.
             $array[] = array(
               'input_type'         =>  $type,
               'required'           =>  get_sub_field('required'),
               'input_label'        =>  get_sub_field('input_label'),
               'placeholder'        =>  get_sub_field('placeholder'),
               'input_column_name'  =>  get_sub_field('input_column_name'),
               'options'            =>  $options
             );
             // Do something...
 
         // End loop.
         endwhile;
 
       // No value.
       else :
         // Do something...
       endif;
 
       return $array;
    }
  
    /**
     * Returns all the columns
     *
     * @param [type] $ID post id of the form
     * @return array [column name][column type]
     */
    public function getAllColumns($ID)
    {
      //
      $array = array();

      // Check rows exists.
      if( have_rows('input',$ID) ):

        // Loop through rows.
        while( have_rows('input',$ID) ) : the_row();

            // Load sub field value.
            $array[get_sub_field('input_column_name')] = get_sub_field('input_column_type');
            // Do something...

        // End loop.
        endwhile;

      // No value.
      else :
        // Do something...
      endif;

      return $array;
    }

    public function getClientEmail($ID)
    {


      $emailList =  get_field('client_email',$ID);

        if(is_array($emailList)){
          $to = array();
          $headers = array();
      
          foreach($emailList as $recipient)
          {
      
              $type = $recipient['type'] ?? 'to';
              if($type=='to')
              {
                  $clientEmail[$type][] = $recipient['email'];
              } else {
                  
                $clientEmail['headers'][] = $type.': '.$recipient['email'];
              }
              
              
          }

      }
      return $clientEmail;


    }

    /**
     * Compares post data with database columns so as to only use data defined when table created (removes extra field data not required for storage)
     *
     * @param int $ID form id
     * @param array $post the $_POST data
     * @return array of column names only with submitted data
     */
    public function getUsefulData($ID, $post)
    {
       // Method for retreiving all column headlings
       $columns = $this->getAllColumns($ID);

       // Compares column headings with posted data, retrieves column data submitted only
       $usefulData = array_intersect_key($post,$columns);


       return $usefulData;
    }

    public function getCustomerEmail($postData)
    {

      $ID = $postData['formid'];
      $emailList = false;

      if(get_field('customer_email',$ID))
      {
        $x = get_field('customer_email_list',$ID);
        $emailList = $postData[$x];
      }

      return $emailList;


    }
  
    /**
     * Delete table when form is deleted (permanently deleted)
     *
     * @param [type] $id post id of the form
     * @return void sql statement deletes table
     */
    public function deleteFormTable($id)
    {
      global $wpdb;
      $table_name = str_replace( '-', '_', sanitize_title_with_dashes( get_the_title($id) ) );
      $prefix = $wpdb->prefix.'eeforms_';
      $users_table = $prefix . $table_name;
      $wpdb->query( "DROP TABLE IF EXISTS ".$users_table );
    }

    /**
     * Gets all forms
     *
     * @return array array of post objects
     */
    public function getAllForms()
    {
      $args = array(
        'post_type'=>'eeforms'
      );
      return get_posts($args);
    }
  
  }
  
  /**
   * Triggers deleting of form for table
   *
   * @param [type] $id post id of form being deleted
   * @return void
   */
  function ee__delete_eeform( $id ) {
    
    // We check if the global post type isn't ours and just return
    global $post_type;   
    
    if ( 'eeforms' !== $post_type ) {
      return;
    }
  
    $form = new FormHandling();
    $form->deleteFormTable($id);
  
  }
  add_action( 'before_delete_post', 'ee__delete_eeform' );
  
  
  

  
  /**
  * Updates a table
  *
  * @param [type] $table_name
  * @param [type] $enq
  * @return void
  */
  // function ee__update_table($table_name,$enq)
  // {
  //   global $wpdb;
  //   $prefix = $wpdb->prefix;
   
  //   $wpdb->insert(
  //       $prefix .'eeforms_'. $table_name,
  //       $enq
  //   );
  // }

  /**
   * Creates a shortcode for displaying the form
   *
   * @param [type] $atts
   * @return void
   */
  function ee__create_shortcode($atts)
  {
    
    if($id = $atts['id']) {
      $slug = sanitize_title_with_dashes( get_the_title($id) );

      $f = new FormHandling();
      $fields = $f->getFieldObj($id);

      ob_start();
      include(locate_template('eeforms/'.$slug.'.php'));
      return ob_get_clean();  

    };

  }

  add_shortcode( 'eeform', 'ee__create_shortcode' );