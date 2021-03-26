<?php
class formsPostType {

    function createFormsPostType(){
        
        $labels = array(
            'name'               => _x('Forms', 'post type Forms name'),
            'singular_name'      => _x('Forms', 'post type Forms name'),
            'add_new'            => _x('Add New', 'Forms'),
            'add_new_item'       => __('Add New Forms'),
            'edit_item'          => __('Edit Forms'),
            'new_item'           => __('New Forms'),
            'all_items'          => __('All Forms'),
            'view_item'          => __('View Forms'),
            'search_items'       => __('Search Forms'),
            'not_found'          => __('No Forms found'),
            'not_found_in_trash' => __('No Forms found in the trash'),
            'parent_item_colon'  => '',
            'menu_name'          => 'Forms'
        );
        $args = array(
            'labels'        => $labels,
            'description'   => 'Holds our Forms specific data',
            'public'        => false,
            'show_ui'       => true,
            'menu_position' => 15,
            'show_in_rest' => true,
            'supports'      => array('title', 'revisions', 'excerpt'),
            'has_archive'   => false,
            'hierarchical'  => false,
            'rewrite'       => array('with_front' => false, 'slug' => 'eeforms'),
            'menu_icon'     => plugin_dir_url( __FILE__ ).'img/dashicons-contact-form-16x16.png',
        );
        register_post_type('eeforms', $args);


    }


    function saveForm( $post_id ) {
        
        if (get_post_type($post_id) != 'eeforms') {
            return;
        }

        $form = new FormHandling();
        $form->createFormTable($post_id);
    }


}
add_action('init', array('formsPostType','createFormsPostType'));
add_action('acf/save_post', array('formsPostType','saveForm') );


class acfOptions
{
    function getCustomerEmailOptions($field)
    {
         // reset choices
        $field['choices'] = array();


        // if has rows
        if( have_rows('input') ) {
            
            // while has rows
            while( have_rows('input') ) {
                
                // instantiate row
                the_row();
                
                
                // vars
                $value = get_sub_field('input_column_name');
                $label = get_sub_field('input_label');

                
                // append to choices
                $field['choices'][ $value ] = $label;
                
            }
            
        }


        // return the field
        return $field;
        
    }

    function readOnlyField($field)
    {
        if(in_array('administrator',wp_get_current_user()->roles))
            return $field;
            
        $field['sub_fields'][2]['disabled'] = 1;
        $field['sub_fields'][3]['disabled'] = 1;
        return $field;
    }

    
    function setJSONLoadPoint($paths)
    {
        // remove original path (optional)
        // unset($paths[0]);


        // append path
        $paths[] = plugin_dir_path( __FILE__ ) . '/acf-json';


        // return
        return $paths;
    }
    

    function updateMessage( $field ) {
        global $post;
        if($title = $post->post_title){
            $slug = sanitize_title_with_dashes( $title );
            $field['message'] = str_replace('form-setup',$slug,$field['message']);
        }

        return $field;
    }
}


add_filter('acf/load_field/name=input', array('acfOptions','readOnlyField') );
add_filter('acf/load_field/name=customer_email_list', array('acfOptions','getCustomerEmailOptions') );
add_filter('acf/load_field/key=field_602fbbb88d9d2', array('acfOptions','updateMessage'));
add_filter('acf/settings/load_json', array('acfOptions','setJSONLoadPoint'));