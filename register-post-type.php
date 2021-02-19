<?php

function cpt_eeforms()
{
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
        'menu_icon'     => 'dashicons-editor-quote',
    );
    register_post_type('eeforms', $args);
}

add_action('init', 'cpt_eeforms');


class setupACF {

    function setJSONLoadPoint()
    {
        // remove original path (optional)
        unset($paths[0]);


        // append path
        $paths[] = plugin_dir_path( __FILE__ ) . '/acf-json';


        // return
        return $paths;
    }
    

    function updateMessage( $field ) {
        global $post;
        if($title = $post->post_title){
            $slug = str_replace( '_', '-', sanitize_title_with_dashes( $title ) );
            $field['message'] = str_replace('form-settup',$slug,$field['message']);
        }

        return $field;
    }

    function saveForm( $post_id ) {
        
        if (get_post_type($post_id) != 'eeforms') {
            return;
        }

        $form = new FormHandling();
        $form->createFormTable($post_id);
    }


}

add_action('acf/save_post', array('setupACF','saveForm') );

add_filter('acf/load_field/key=field_602fbbb88d9d2', array('setupACF','updateMessage'));
add_filter('acf/settings/load_json', array('setupACF','setJSONLoadPoint'));