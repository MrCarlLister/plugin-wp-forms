<?php

/**
 * Register image and text block
 *
 * @return void
 */

class formGutten {

    public $block_name = 'ee-forms';

    function register()
    {
        $fG = new formGutten;
        add_filter('allowed_block_types', function($arr){ 
            $arr[]='acf/ee-forms';

            return $arr;

        }, 30);

        if (!function_exists('acf_register_block_type'))
            return;


        acf_register_block_type(array(
            'name'                => $fG->block_name,
            'title'                => __('Form', get_bloginfo('name')),
            'description'        => __('Display a form from the list'),
            'category'            => 'cta',
            'icon'                => 'images-alt',
            'keywords'            => array('formGutten', 'all'),
            // 'post_types'		=> array('posts','pages'),
            'mode'                => 'preview',
            // 'align'				=> '',
            // 'align_text'		=> 'left',
            // 'align_content'		=> 'top',
            // 'render_template'   => $render_template,
            'render_callback'   => ['formGutten','props'],
            // 'enqueue_style'		=> ee_mph__acf_find_the_template_path($block_name,'css'),
            // 'enqueue_script'		=> ee_mph__acf_find_the_template_path($block_name,'js'),
            // 'enqueue_assets'	=> function(){
            // 	wp_enqueue_style( 'block-testimonial', ee_mph__acf_find_the_template_path($block_name,'css') );
            // 	wp_enqueue_script( 'block-testimonial', ee_mph__acf_find_the_template_path($block_name,'js'), array('jquery'), '', true );
            //   },
            // 'supports'			=> array(
            // 	// disable alignment toolbar
            // 	'align' => false,

            // 	// customize alignment toolbar
            // 	'align' => array( 'left', 'right', 'full' ),

            // 	// Show text alignment toolbar.
            // 	'align_text' => true,

            // 	// Show content alignment toolbar.
            // 	'align_content' => true,

            // 	// disable preview/edit toggle
            // 	'mode' => false,

            // 	'multiple' => false

            // ),
            // 'example'  => array(
            // 	'attributes' => array(
            // 		'mode' => 'preview',
            // 		'data' => array(
            // 		  'testimonial'   => "Your testimonial text here",
            // 		  'author'        => "John Smith"
            // 		)
            // 	)
            // )

        ));
    }


    function props(){

        $ID = get_field('formid');

        (new formGutten)->render($ID);
    }

    function render($ID){

        echo do_shortcode('[eeform id="'.$ID.'"]');
    }
}


add_action('acf/init', ['formGutten','register']);
