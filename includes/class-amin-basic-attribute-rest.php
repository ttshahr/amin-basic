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
require_once( 'class-amin-basic-utils.php' );

class Amin_Basic_Product_Attributes_Rest extends WC_REST_Product_Attributes_Controller {
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
		$this->rest_base = 'attributes';
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

		// add_log("register routes Product_Attributes_Rest");
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
			add_log("create permissions check Product_Attributes_Rest ====> error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot create the product attribute resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("create permissions check Product_Attributes_Rest");
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
			add_log("edit permissions check Product_Attributes_Rest ====> error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot edit the product attribute resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("edit permissions check Product_Attributes_Rest");
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
			add_log("delete permissions check Product_Attributes_Rest ====> error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot delete the product attribute resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("delete permissions check Product_Attributes_Rest");
		return true;
	}

	/**
	 * Create product attribute
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {

		$data = [];
		if ( ! isset( $request['code'] ) || ! isset( $request['name'] ) || ! isset( $request['display_name'] ) || ! isset( $request['recordID'] ) ) {
			add_log("Amin_Basic_Product_Attributes_Rest: create_item => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}

        global $wpdb;
	    $attribute_id = $wpdb->get_col("SELECT attribute_id
	    FROM {$wpdb->prefix}abp_attributes
	    WHERE abpCode = {$request['code']} OR abpRecordId = {$request['recordID']}");
		$id = reset($attribute_id);
		if ( $id && $id > 0 && $id != null){
			add_log("Amin_Basic_Product_Attributes_Rest: create_item => error 2");
			return new WP_Error( Amin_Basic_Response_Code::Attribute_Id_Exist, __( 'A product attribute with the code or record id provided already exists.' ), array( 'status' => 400 ) );
		}
        $res = $this->save_product_attribute_from_name( $request['name'], $request['display_name'] );

		if ( ! is_wp_error( $res ) ) {
            $wpdb->insert( "{$wpdb->prefix}abp_attributes", array( 'attribute_id' => $res, 'abpCode' => $request['code'], 'abpRecordId' => $request['recordID'] ) );
            
			$data['code'] = Amin_Basic_Response_Code::Attribute_Created;
			$data['message'] = __( 'Product attribute has been successfully added.', '' );
			$data['data'] = array('status' => 200);
		} else if ( is_wp_error( $res ) ) {
            return $res;
		} else {
			add_log("Amin_Basic_Product_Attributes_Rest: create_item => error 3");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
        }

		add_log("Amin_Basic_Product_Attributes_Rest: create_item => succeed");
		return rest_ensure_response( $data );
	}

	/**
	 * Edit product attribute
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function edit_item( $request ) {

		$data = [];
		if ( ! isset( $request['recordID'] ) ) {
			add_log("Amin_Basic_Product_Attributes_Rest: edit_item => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}

		global $wpdb;

		$attribute_id_to_edit = $this->get_attribute_id_from_meta( $request['recordID'], 0 );

		if ( $attribute_id_to_edit ) {
			$attribute_data = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = {$attribute_id_to_edit}" );

			$args = array(
				'attribute_id'      => $attribute_id_to_edit,
				'attribute_name'    => $attribute_data->attribute_name,
				'attribute_label'   => $attribute_data->attribute_label,
				'attribute_type'    => 'select',
				'attribute_orderby' => 'menu_order',
				'attribute_public'  => 0
			);

			$args_to_edit = [];

			if ( isset( $request['name'] ) ) {
				$slug = sanitize_title( $request['name'] );
			
				if ( strlen( $slug ) >= 28 ) {
					add_log("Amin_Basic_Product_Attributes_Rest: edit_item => error 2");
					return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Invalid product attribute name too long.' ), array( 'status' => 400 ) );
				} elseif ( wc_check_if_attribute_name_is_reserved( $slug ) ) {
					add_log("Amin_Basic_Product_Attributes_Rest: edit_item => error 3");
					return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Invalid product attribute name reserved.' ), array( 'status' => 400 ) );
				}
			
				$attribute_id = $this->get_attribute_id_from_name( $slug );
			
				if( ! empty( $attribute_id ) ){
					add_log("Amin_Basic_Product_Attributes_Rest: edit_item => error 4");
					return new WP_Error( Amin_Basic_Response_Code::Attribute_Name_Exist, __( 'Invalid product attribute name already exists.' ), array( 'status' => 400 ) );
				}
				$args_to_edit['attribute_name'] = $slug;
				$args['attribute_name'] = $slug;
			}

			if ( isset( $request['display_name'] ) ) {
				$args_to_edit['attribute_label'] = $request['display_name'];
				$args['attribute_label'] = $request['display_name'];
			}

			if ( count( $args_to_edit ) > 0 ) {
				$res = $wpdb->update( "{$wpdb->prefix}woocommerce_attribute_taxonomies", $args_to_edit, array( 'attribute_id' => $attribute_id_to_edit ) );
				set_transient( 'wc_attribute_taxonomies', false );

				$attributes = wc_get_attribute_taxonomies();
				$attributes[] = (object) $args;
				set_transient( 'wc_attribute_taxonomies', $attributes );
			}

			if ( isset( $request['code'] ) ) {
				$res = $wpdb->update( "{$wpdb->prefix}abp_attributes", array ( 'abpCode' => $request['code'] ), array ( 'attribute_id' => $attribute_id_to_edit ));
			}
		} else {
			add_log("Amin_Basic_Product_Attributes_Rest: edit_item => error 5");
			return new WP_Error( Amin_Basic_Response_Code::Attribute_Id_Not_Found, __( 'The record id provided doesn\'t exist.' ), array( 'status' => 400 ) );
		}

		if ( $res !== false ) {
			$data['code'] = Amin_Basic_Response_Code::Attribute_Updated;
			$data['message'] = __( 'Product attribute has been successfully updated.', '' );
			$data['data'] = array( 'status' => 200 );
		} else {
			add_log("Amin_Basic_Product_Attributes_Rest: edit_item => error 6");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

		add_log("Amin_Basic_Product_Attributes_Rest: edit_item => succeed");
		return rest_ensure_response( $data );
	}

	/**
	 * Delete product attribute
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function delete_item( $request ) {
		if ( ! isset( $request['recordID'] ) ) {
			add_log("Amin_Basic_Product_Attributes_Rest: delete_item => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}

	    $attribute_id = $this->get_attribute_id_from_meta( $request['recordID'], 0 );
		if ( ! $attribute_id ){
			add_log("Amin_Basic_Product_Attributes_Rest: delete_item => error 2");
			return new WP_Error( Amin_Basic_Response_Code::Attribute_Id_Not_Found, __( 'The record id provided doesn\'t exist.' ), array( 'status' => 400 ) );
		}
		$res = wc_delete_attribute( $attribute_id );

		if ( $res ) {
			global $wpdb;
        	$res = $wpdb->delete( "{$wpdb->prefix}abp_attributes", array( 'attribute_id' => $attribute_id ), array( '%d' ) );
		}

		if ( $res ) {
			$data['code'] = Amin_Basic_Response_Code::Attribute_Deleted;
			$data['message'] = __( 'Product attribute has been successfully deleted.', '' );
			$data['data'] = array( 'status' => 200 );
		} else {
			add_log("Amin_Basic_Product_Attributes_Rest: delete_item => error 3");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

		add_log("Amin_Basic_Product_Attributes_Rest: delete_item => succeed");
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
	 * Utility method for create a product attribute from name and label (display name)
	 *
	 * @return int
	 */
    function save_product_attribute_from_name( $name, $label, $set = true ){
        global $wpdb;

        $slug = sanitize_title( $name );
    
        if ( strlen( $slug ) >= 28 ) {
			add_log("Amin_Basic_Product_Attributes_Rest: save_product_attribute_from_name => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Invalid product attribute name too long.' ), array( 'status' => 400 ) );
        } elseif ( wc_check_if_attribute_name_is_reserved( $slug ) ) {
			add_log("Amin_Basic_Product_Attributes_Rest: save_product_attribute_from_name => error 2");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Invalid product attribute name reserved.' ), array( 'status' => 400 ) );
        }
    
        $attribute_id = $this->get_attribute_id_from_name( $slug );
    
        if( ! empty($attribute_id) ){
			add_log("Amin_Basic_Product_Attributes_Rest: save_product_attribute_from_name => error 3");
            return new WP_Error( Amin_Basic_Response_Code::Attribute_Name_Exist, __( 'Invalid product attribute name already exists.' ), array( 'status' => 400 ) );
        }
        
        $args = array(
            'attribute_name'    => $slug,
            'attribute_label'   => $label,
            'attribute_type'    => 'select',
            'attribute_orderby' => 'menu_order',
            'attribute_public'  => 0,
        );
    
        $res = $wpdb->insert(  "{$wpdb->prefix}woocommerce_attribute_taxonomies", $args );
        set_transient( 'wc_attribute_taxonomies', false );
    
        $attributes = wc_get_attribute_taxonomies();
        $args['attribute_id'] = $this->get_attribute_id_from_name( $name );
        $attributes[] = (object) $args;
        set_transient( 'wc_attribute_taxonomies', $attributes );

		add_log("Amin_Basic_Product_Attributes_Rest: save_product_attribute_from_name => ...");
		return $args['attribute_id'];
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
   		
		add_log("Amin_Basic_Product_Attributes_Rest: get_attribute_id_from_name => ...");
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
		add_log("Amin_Basic_Product_Attributes_Rest: get_attribute_id_from_meta => ...");
		if ( $id && $id > 0 && $id != null)
	    	return $id;
		else 
			return false;
	}
}