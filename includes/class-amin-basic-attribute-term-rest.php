<?php

/**
 * Add rest api endpoint for product attribute
 *
 * @link       https://AliJannesary.ir
 * @since      1.0.0
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/includes
 */

/**
 * Add rest api endpoint for product attribute.
 *
 * Maintain create, edit and delete methods for product attribute.
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/includes
 * @author     Ali Jannesary <https://AliJannesary.ir>
 */
class Amin_Basic_Product_Attributes_Terms_Rest extends WC_REST_Terms_Controller {
	/**
	 * The namespace.
	 *
     * @since    1.0.0
	 * @access   protected
	 * @var      string
	 */
	protected $namespace;
	/**
	 * Rest base for the current object.
	 *
     * @since    1.0.0
	 * @access   protected
	 * @var      string
	 */
	protected $rest_base;

	/**
	 * Initialize the main variables.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->namespace = 'wc/api';
		$this->rest_base = 'attributes/terms';
	}

	/**
	 * Register the routes for the objects of the controller.
     * 
     * @since    1.0.0
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/create', array(

			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'create_permissions_check' )
			),
			'schema' => null

		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/edit', array(

			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'edit_item' ),
				'permission_callback' => array( $this, 'edit_permissions_check' )
			),
			'schema' => null

		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/delete', array(

			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'delete_permissions_check' )
			),
			'schema' => null

		) );
		
		// add_log("Amin_Basic_Product_Attributes_Terms_Rest: register_routes => ...");
	}

	/**
	 * Check permissions for the create.
	 *
     * @since    1.0.0
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   bool|WP_Error
	 */
	public function create_permissions_check( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			add_log("Amin_Basic_Product_Attributes_Terms_Rest: create_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot create the attribute term resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Product_Attributes_Terms_Rest: create_permissions_check => succeed");
		return true;
	}

	/**
	 * Check permissions for the edit.
	 *
     * @since    1.0.0
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   bool|WP_Error
	 */
	public function edit_permissions_check( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			add_log("Amin_Basic_Product_Attributes_Terms_Rest: edit_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot edit the attribute term resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Product_Attributes_Terms_Rest: edit_permissions_check => succeed");
		return true;
	}

	/**
	 * Check permissions for the delete.
	 *
     * @since    1.0.0
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   bool|WP_Error
	 */
	public function delete_permissions_check( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			add_log("Amin_Basic_Product_Attributes_Terms_Rest: delete_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot delete the attribute term resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Product_Attributes_Terms_Rest: delete_permissions_check => succeed");
		return true;
	}

	/**
	 * Create attribute term
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {
		
		if ( ! isset( $request['code'] ) || ! isset( $request['parent_code'] ) || ! isset( $request['name'] ) || ! isset( $request['display_name'] ) || ! isset( $request['recordID'] ) ) {
			add_log("Amin_Basic_Product_Attributes_Terms_Rest: create_item => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}

		$slug = sanitize_title( $request['name'] );

		$attribute_id = $this->get_attribute_id_from_meta( $request['parent_code'], 1 );

		if ( ! $attribute_id ){
			add_log("Amin_Basic_Product_Attributes_Terms_Rest: create_item => error 2");
			return new WP_Error( Amin_Basic_Response_Code::Attribute_Id_Not_Found, __( 'The parent code provided doesn\'t exist.' ), array( 'status' => 400 ) );
		}
		$attribute_name =  wc_attribute_taxonomy_name_by_id( (int)$attribute_id );

		$terms = get_terms( array(
			'taxonomy'   => $attribute_name,
			'hide_empty' => false,
			'meta_query' => array (
				'relation' => 'OR',
				array (
					'key'     => 'abpCode',
					'value'   => $request['code'],
					'compare' => '='
				),
				array (
					'key'     => 'abpRecordId',
					'value'   => $request['recordID'],
					'compare' => '='
				)
			)
		) );

		if ( count($terms) > 0 ){
			add_log("Amin_Basic_Product_Attributes_Terms_Rest: create_item => error 3");
			return new WP_Error( Amin_Basic_Response_Code::Attribute_Term_Id_Exist, __( 'An attribute term with the code or record id provided already exists.' ), array( 'status' => 400 ) );
		}
		if( ! term_exists( $slug, $attribute_name ) ){
			$res = wp_insert_term( $request['display_name'], $attribute_name, array ( 'slug' => $slug ) );
		} else {
			add_log("Amin_Basic_Product_Attributes_Terms_Rest: create_item => error 4");
			return new WP_Error( Amin_Basic_Response_Code::Attribute_Term_Name_Exist, __( 'An attribute term with the name provided already exists.' ), array( 'status' => 400 ) );
		}

		if ( ! is_wp_error( $res ) ) {
			update_term_meta( $res['term_id'], 'abpCode', $request['code'] );
			update_term_meta( $res['term_id'], 'abpRecordId', $request['recordID'] );

			$data['code'] = Amin_Basic_Response_Code::Attribute_Term_Created;
			$data['message'] = __( 'Attribute term has been successfully added.', '' );
			$data['data'] = array('status' => 200);
		} else {
			add_log("Amin_Basic_Product_Attributes_Terms_Rest: create_item => error 5");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
        }

		add_log("Amin_Basic_Product_Attributes_Terms_Rest: create_item => succeed");
		return rest_ensure_response( $data );
	}

	/**
	 * Edit attribute term
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function edit_item( $request ) {

		if ( ! isset( $request['recordID'] ) ) {
			add_log("Amin_Basic_Product_Attributes_Terms_Rest: edit_item => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}

		$terms = get_terms( array(
			'hide_empty' => false,
			'meta_query' => array (
				array (
					'key'     => 'abpRecordId',
					'value'   => $request['recordID'],
					'compare' => '='
				)
			)
		) );

		if ( count( $terms ) <= 0 ){
			add_log("Amin_Basic_Product_Attributes_Terms_Rest: edit_item => error 2");
			return new WP_Error( Amin_Basic_Response_Code::Attribute_Term_Id_Not_Found, __( 'The record id provided doesn\'t exist.' ), array( 'status' => 400 ) );
		}
		$term_id = $terms[0]->term_id;
		$attribute_name = $terms[0]->taxonomy;

		if ( isset( $request['name'] ) ) {
			$slug = sanitize_title( $request['name'] );

			if( ! term_exists( $slug, $attribute_name ) ){
				$res = wp_update_term( $term_id, $attribute_name, array ( 'slug' => $slug ) );
			} else {
				add_log("Amin_Basic_Product_Attributes_Terms_Rest: edit_item => error 3");
				return new WP_Error( Amin_Basic_Response_Code::Attribute_Term_Name_Exist, __( 'An attribute term with the name provided already exists.' ), array( 'status' => 400 ) );
			}
		}

		if ( isset( $request['display_name'] ) ) {
			$res = wp_update_term( $term_id, $attribute_name, array ( 'name' => $request['display_name'] ) );
		}

		if ( isset( $request['code'] ) ) {
			$res = update_term_meta( $term_id, 'abpCode', $request['code'] );
		}

		if ( $res ) {
			$data['code'] = Amin_Basic_Response_Code::Attribute_Term_Updated;
			$data['message'] = __( 'Attribute term has been successfully updated.', '' );
			$data['data'] = array( 'status' => 200 );
		} else {
			add_log("Amin_Basic_Product_Attributes_Terms_Rest: edit_item => error 3");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

		add_log("Amin_Basic_Product_Attributes_Terms_Rest: edit_item => succeed");
		return rest_ensure_response( $data );
	}

	/**
	 * Delete attribute term
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function delete_item( $request ) {
		if ( ! isset( $request['recordID'] ) ) {
			add_log("Amin_Basic_Product_Attributes_Terms_Rest: delete_item => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}

		$terms = get_terms( array(
			'hide_empty' => false,
			'meta_query' => array (
				array (
					'key'     => 'abpRecordId',
					'value'   => $request['recordID'],
					'compare' => '='
				)
			)
		) );

		if ( count( $terms ) <= 0 ){
			add_log("Amin_Basic_Product_Attributes_Terms_Rest: delete_item => error 2");
			return new WP_Error( Amin_Basic_Response_Code::Attribute_Term_Id_Not_Found, __( 'The record id provided doesn\'t exist.' ), array( 'status' => 400 ) );
		}
		$term_id = $terms[0]->term_id;
		$attribute_name = $terms[0]->taxonomy;

		$res = wp_delete_term( $term_id, $attribute_name );

		if ( $res ) {
			$data['code'] = Amin_Basic_Response_Code::Attribute_Term_Deleted;
			$data['message'] = __( 'Attribute term has been successfully deleted.', '' );
			$data['data'] = array( 'status' => 200 );
		} else {
			add_log("Amin_Basic_Product_Attributes_Terms_Rest: delete_item => error 3");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

		add_log("Amin_Basic_Product_Attributes_Terms_Rest: delete_item => succeed");
		return rest_ensure_response( $data );
	}

	/**
	 * Sets up the proper HTTP status code for authorization.
	 *
	 * @return int
	 */
	public function authorization_status_code() {

		$status = 401;

		if ( is_user_logged_in() ) {
			$status = 403;
		}

		return $status;
	}







    /**
	 * Utility method for get attribute id by name (slug)
	 *
	 * @return int|bool
	 */
    function get_attribute_id_from_name( $name ){
        global $wpdb;
        $attribute_id = $wpdb->get_col("SELECT attribute_id
        FROM {$wpdb->prefix}woocommerce_attribute_taxonomies
        WHERE attribute_name LIKE '$name'");
     		add_log("Amin_Basic_Product_Attributes_Terms_Rest: save_product_attribute_from_name => ...");
			 return reset($attribute_id);
    }

	/**
	 * Utility method for get attribute id by abpRecordId = 0 or abpCode = 1 meta
	 *
	 * @return int|bool
	 */
	public function get_attribute_id_from_meta( $value, $type ){
		$type = $type == 0 ? 'abpRecordId' : 'abpCode';
	    global $wpdb;
	    $attribute_id = $wpdb->get_col("SELECT attribute_id
	    FROM {$wpdb->prefix}abp_attributes
	    WHERE {$type} = {$value}");
		$id = reset($attribute_id);
		add_log("Amin_Basic_Product_Attributes_Terms_Rest: get_attribute_id_from_meta => ...");
		if ( $id && $id > 0 && $id != null)
	    	return $id;
		else 
			return false;
	}
}