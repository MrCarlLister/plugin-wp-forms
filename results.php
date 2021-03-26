<?php

/*
Plugin Name: Contact form table
Plugin URI: https://www.sitepoint.com/using-wp_list_table-to-create-wordpress-admin-tables/
Description: Plugin that adds contact form submissions to dashboard
Version: 1.0
Author: Collins Agbonghama / Carl Lister
Author URI:  https://w3guy.com / https://mrcarllister.co.uk
*/

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

function ee___redirect_fixer() {
	ob_start();
} // soi_output_buffer
add_action('init', 'ee___redirect_fixer');

class enquiries_List extends WP_List_Table {


	/** Class constructor */
	public function __construct() {
		
		parent::__construct( [
			'singular' => __( 'Request', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Requests', 'sp' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

	}
	
	// public $tablename = $_GET['tablename'];
	

	/**
	 * Retrieve enquiries data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_enquiries( $per_page = 20, $page_number = 1 ) {

		global $wpdb;
		// dd( $_REQUEST );
		// $f = new FormHandling;
		// dd($f->getAllForms());

		// foreach($f->getAllForms() as $table)
		// {
			// $tablename = str_replace( '-', '_', sanitize_title_with_dashes( get_the_title($_REQUEST['page']) ) );
		// }

		$tablename = self::get_tablename();
		// echo $tablename;

		// $sql = "SELECT * FROM {$tablename}";
		$sql = "SELECT * FROM {$tablename}";


		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		// dd($result);
		return $result;
	}


	/**
	 * Delete a enquiry record.
	 *
	 * @param int $id enquiry ID
	 */
	public static function delete_enquiry( $id ) {
		global $wpdb;
		$tablename = self::get_tablename();
		// dd($tablename);
		$wpdb->delete(
			"{$tablename}",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}


	public static function get_tablename(){
		if(isset($_GET['page'])){
			global $wpdb;
			return $wpdb->prefix.'eeforms_'.str_replace( '-', '_', sanitize_title_with_dashes( get_the_title($_GET['page']) ) );
		}
		

		return false;
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;
		$tablename = self::get_tablename();
		$sql = "SELECT COUNT(*) FROM {$tablename}";

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no enquiry data is available */
	public function no_items() {
		_e( 'No enquiries avaliable.', 'sp' );
	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_id( $item ) {

		$delete_nonce = wp_create_nonce( 'sp_delete_enquiry' );

		$title = '<strong>' . $item['id'] . '</strong>';

		$actions = [
			'delete' => sprintf( '<a href="?post_type=%s&page=%s&action=%s&enquiry=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['post_type']), esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		global $wpdb;
		$tablename = $this->get_tablename();

		$existing_columns = $wpdb->get_col("DESC {$tablename}", 0);
		
		
		$columns = array();
		foreach($existing_columns as $item)
		{

			$columns[$item] = $item;
		}
		$columns = array_merge(array('cb'=>'<input type="checkbox" />'),$columns);

		// $columns = [
		// 	'cb'      => '<input type="checkbox" />',
		// 	'id'    => __( 'ID', 'sp' ),
		// 	'label'    => __( 'Form label', 'sp' ),
		// 	'name'    => __( 'Name', 'sp' ),
		// 	'telephone' => __( 'Telephone', 'sp' ),
		// 	'email'    => __( 'Email', 'sp' ),
		// 	'message'    => __( 'Message', 'sp' )
		// ];
		// dd($columns);
		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'id'    => __( 'ID', 'sp' ),
			'label'    => __( 'For label', 'sp' ),
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'enquiries_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_enquiries( $per_page, $current_page );
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		
		if ( 'delete' === $this->current_action() ) {
			// echo 'a';
			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'sp_delete_enquiry' ) ) {
				die( 'Go get a life script kiddies' );
				
			}
			else {
				self::delete_enquiry( absint( $_GET['enquiry'] ) );

		                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		                // add_query_arg() return the current url
		                // wp_redirect( '/wp-admin/admin.php?page='.$_REQUEST['page']);
						wp_redirect( '/wp-admin/edit.php?post_type='.$_REQUEST['post_type'].'&page='.$_REQUEST['page'] );
						// wp_redirect( '/wp-admin/admin.php?page='.$_REQUEST['page'].'&tablename=wp_eeforms_editorial'  );

				exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_enquiry( $id );

			}

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		        // add_query_arg() return the current url
				wp_redirect( '/wp-admin/edit.php?post_type='.$_REQUEST['post_type'].'&page='.$_REQUEST['page']  );
			exit;
		}
	}

}


class SP_Plugin {

	// class instance
	static $instance;

	// enquiry WP_List_Table object
	public $enquiries_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_menu' ],999999 );
	}


	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function plugin_menu() {
		$f = new FormHandling;
		$forms = $f->getAllForms();

		// dd($forms);

		foreach($forms as $form)
		{

			

					$hook = add_submenu_page(
						'edit.php?post_type=eeforms',
						$form->post_title . ' form submissions',
						'> '.$form->post_title,
						'manage_options',
						$form->ID,
						[ $this, 'plugin_settings_page' ]
					);

					

					add_action( "load-$hook", [ $this, 'screen_option' ],99999 );

		}

	}


	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		
		?>
		<style>
		
		.wp-list-table .column-id { width:7%; }
		.wp-list-table .column-label { width:15%; }
		.wp-list-table .column-name { width:15%; }
		.wp-list-table .column-telephone { width:10%; }
		.wp-list-table .column-email { width:15%; }
		.wp-list-table .column-message { width:38%; }
		#poststuff #post-body.columns-2
		{
			margin-right:0;
		}
		
		</style>
		<div class="wrap">
			<h2>Form submissions</h2>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								$this->enquiries_obj->prepare_items();
								$this->enquiries_obj->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	<?php
	
	}

	public function plugin_details_page() {
		
		?>
		<style>
		
		.wp-list-table .column-id { width:7%; }
		.wp-list-table .column-label { width:15%; }
		.wp-list-table .column-name { width:15%; }
		.wp-list-table .column-telephone { width:10%; }
		.wp-list-table .column-email { width:15%; }
		.wp-list-table .column-message { width:38%; }
		#poststuff #post-body.columns-2
		{
			margin-right:0;
		}
		
		</style>
		<div class="wrap">
			<h2>Details</h2>

			
		</div>
	<?php
	
	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'enquiries',
			'default' => 20,
			'option'  => 'enquiries_per_page'
		];

		add_screen_option( $option, $args );

		$this->enquiries_obj = new enquiries_List();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}


add_action( 'plugins_loaded', function () {
	SP_Plugin::get_instance();
} );
