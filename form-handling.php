<?php

class FormHandling {


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
  
    public function deleteFormTable($id)
    {
      global $wpdb;
      $table_name = str_replace( '-', '_', sanitize_title_with_dashes( get_the_title($id) ) );
      $prefix = $wpdb->prefix.'eeforms_';
      $users_table = $prefix . $table_name;
      $wpdb->query( "DROP TABLE IF EXISTS ".$users_table );
    }

    public function getAllForms()
    {
      $args = array(
        'post_type'=>'eeforms'
      );
      return get_posts($args);
    }
  
  }
  
  
  function ee__delete_eeform( $postid ) {
    
    // We check if the global post type isn't ours and just return
    global $post_type;   
    
    if ( 'eeforms' !== $post_type ) {
      return;
    }
  
    $form = new FormHandling();
    $form->deleteFormTable($postid);
  
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


  function ee__create_shortcode($atts)
  {
    
    $slug = str_replace( '_', '-', sanitize_title_with_dashes( get_the_title($atts['id']) ) );
    ob_start();
    include(locate_template('eeforms/'.$slug.'.php'));
    return ob_get_clean();  

  }

  add_shortcode( 'eeform', 'ee__create_shortcode' );

  
  