<?php

/**
 * Add rest api endpoint for products category
 *
 * @link       https://AliJannesary.ir
 * @since      1.0.0
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/includes
 */

/**
 * Add rest api endpoint for products category.
 *
 * Maintain create, edit and delete methods for products category.
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/includes
 * @author     Ali Jannesary <https://AliJannesary.ir>
 */
class Amin_Basic_Product_Categories_Rest extends WC_REST_Product_Categories_Controller {
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
		$this->rest_base = 'categories';
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
		
		// add_log("Amin_Basic_Product_Categories_Rest: register_routes => ...");
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
			add_log("Amin_Basic_Product_Categories_Rest: create_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot create the products category resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Product_Categories_Rest: create_permissions_check => succeed");
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
			add_log("Amin_Basic_Product_Categories_Rest: edit_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot edit the products category resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Product_Categories_Rest: edit_permissions_check => succeed");
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
			add_log("Amin_Basic_Product_Categories_Rest: delete_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot delete the products category resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Product_Categories_Rest: delete_permissions_check => succeed");
		return true;
	}

	/**
	 * Create product category
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {

		$data = [];
		if ( ! isset( $request['code'] ) || ! isset( $request['parent_code'] ) || ! isset( $request['name'] ) || ! isset( $request['typeLevel'] ) || ! isset( $request['typeShow'] ) || ! isset( $request['recordID'] ) ) {
			add_log("Amin_Basic_Product_Categories_Rest: create_item => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}
		
		$abpCode = intval( $request['code'] );
		if ( $abpCode <= 0 || $abpCode == null ) {
			add_log("Amin_Basic_Product_Categories_Rest: create_item => error 2");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The code provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		$abpParentCode = intval( $request['parent_code'] );

		$abpTypeLevel = intval( $request['typeLevel'] );
		if ( ( $abpTypeLevel != 1 && $abpTypeLevel != 2 )  || $abpTypeLevel == null ) {
			add_log("Amin_Basic_Product_Categories_Rest: create_item => error 3");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The type level provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		$abpTypeShow = intval( $request['typeShow'] );
		if ( ( $abpTypeShow != 2 && $abpTypeShow != 1 )  || $abpTypeShow == null ) {
			add_log("Amin_Basic_Product_Categories_Rest: create_item => error 4");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The type show provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		$abpRecordId = $request['recordID'];
		if ( $abpRecordId == null ) {
			add_log("Amin_Basic_Product_Categories_Rest: create_item => error 5");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The record id provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}

		$args = array(
			'hide_empty' => false,
			'meta_query' => array(
				array(
				   'key'       => 'abpCode',
				   'value'     => $abpCode,
				   'compare'   => '='
				)
			),
			'taxonomy'  => 'product_cat',
		);
		$categories = get_terms( $args );

		if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
			add_log("Amin_Basic_Product_Categories_Rest: create_item => error 6");
			return new WP_Error( Amin_Basic_Response_Code::Category_Exist, __( 'A category with the code provided already exists.' ), array( 'status' => 400 ) );
		} else if ( is_wp_error( $categories ) ) {
			add_log("Amin_Basic_Product_Categories_Rest: create_item => error 7");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( $categories->get_error_message() ), array( 'status' => 400 ) );
		}

		// Insert term (= product category)
		$termObject = wp_insert_term( $request['name'], 'product_cat' );
        if ( is_wp_error($termObject) ) {
            if ( $termObject->get_error_code() == 'term_exists' ) {
				add_log("Amin_Basic_Product_Categories_Rest: create_item => error 8");
				return new WP_Error( Amin_Basic_Response_Code::Category_Exist, __( 'A category with the name provided already exists.' ), array( 'status' => 400 ) );
			} else {
				add_log("Amin_Basic_Product_Categories_Rest: create_item => error 9");
				return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( $termObject->get_error_message() ), array( 'status' => 400 ) );
			}
        }
		
        if ( $abpParentCode != null && $abpParentCode > 0 ) {
			$args = array(
				'hide_empty' => false,
				'meta_query' => array(
					array(
					   'key'       => 'abpCode',
					   'value'     => $abpParentCode,
					   'compare'   => '='
					)
				),
				'taxonomy'  => 'product_cat',
			);
			$parentCategory = get_terms( $args );

			if ( ! empty( $parentCategory ) && ! is_wp_error( $parentCategory ) ) {
            	$res = wp_update_term( $termObject['term_id'], 'product_cat', array ( 'parent' => $parentCategory[0]->term_id ) );
			} else if ( empty( $parentCategory ) ) {
				add_log("Amin_Basic_Product_Categories_Rest: create_item => error 10");
				return new WP_Error( Amin_Basic_Response_Code::Category_Id_Not_Found, __( 'The parent code provided doesn\'t exist.' ), array( 'status' => 400 ) );
			}
        }
		
        $res = update_term_meta( $termObject['term_id'], 'abpCode', $abpCode );
        $res = update_term_meta( $termObject['term_id'], 'abpParentCode', $abpParentCode );
        $res = update_term_meta( $termObject['term_id'], 'abpTypeLevel', $abpTypeLevel );
        $res = update_term_meta( $termObject['term_id'], 'abpTypeShow', $abpTypeShow );
        $res = update_term_meta( $termObject['term_id'], 'abpRecordId', $abpRecordId );

		if ( $res ) {
			$data['code'] = Amin_Basic_Response_Code::Category_Created;
			$data['message'] = __( 'Product category has been successfully added.', '' );
			$data['data'] = array('status' => 200);
		} else {
			add_log("Amin_Basic_Product_Categories_Rest: create_item => error 11");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

		add_log("Amin_Basic_Product_Categories_Rest: create_item => succeed");
		return rest_ensure_response( $data );
	}

	/**
	 * Edit product category
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function edit_item( $request ) {

		$data = [];
		if ( ! isset( $request['recordID'] ) ) {
			add_log("Amin_Basic_Product_Categories_Rest: edit_item => error 1 ===> RecordId Parameter Not Send");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}

		$abpRecordId = $request['recordID'];
		if ( $abpRecordId != null ) {
			$args = array(
				'hide_empty' => false,
				'meta_query' => array(
					array(
					   'key'       => 'abpRecordId',
					   'value'     => $abpRecordId,
					   'compare'   => '='
					)
				),
				'taxonomy'  => 'product_cat',
			);
			$categoryObject = get_terms( $args );

			if ( empty( $categoryObject ) || is_wp_error( $categoryObject ) ) {
				add_log("Amin_Basic_Product_Categories_Rest: edit_item => error 2 ===> RecordId Not Exist IN Product: " . $abpRecordId);
				return new WP_Error( Amin_Basic_Response_Code::Category_Id_Not_Found, __( 'The record id provided doesn\'t exist.' ), array( 'status' => 400 ) );
			}
		} else {
			add_log("Amin_Basic_Product_Categories_Rest: edit_item => error 3 ===> RecordId Parameter IS Null ");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The record id provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}
		
		if ( isset( $request['code'] ) ) {
			$abpCode = intval( $request['code'] );
			if ( $abpCode > 0  && $abpCode != null ) {
				$res = update_term_meta( $categoryObject[0]->term_id, 'abpCode', $abpCode );
			} else {
				add_log("Amin_Basic_Product_Categories_Rest: edit_item => error 4");
				return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The code provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
			}
		}

		if ( isset( $request['parent_code'] ) ) {
			$abpParentCode = intval( $request['parent_code'] );
			if ( $abpParentCode >= 0 ) {
				if( $abpParentCode == 0 ) {
					$parentCategoryId = 0;
				} else {
					$args = array(
						'hide_empty' => false,
						'meta_query' => array(
							array(
							   'key'       => 'abpCode',
							   'value'     => $abpParentCode,
							   'compare'   => '='
							)
						),
						'taxonomy'  => 'product_cat',
					);
					$parentCategory = get_terms( $args );

					if ( count( $parentCategory ) <= 0 || is_wp_error( $parentCategory ) ){
						add_log("Amin_Basic_Product_Categories_Rest: edit_item => error 5");
						return new WP_Error( Amin_Basic_Response_Code::Category_Id_Not_Found, __( 'The parent code provided doesn\'t exist.' ), array( 'status' => 400 ) );
					}
					$parentCategoryId = $parentCategory[0]->term_id;
				}
				$res = wp_update_term( $categoryObject[0]->term_id, 'product_cat', array ( 'parent' => $parentCategoryId ) );
				$res = update_term_meta( $categoryObject[0]->term_id, 'abpParentCode', $abpParentCode );
			} else {
				add_log("Amin_Basic_Product_Categories_Rest: edit_item => error 6");
				return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The parent code provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
			}
		}

		$notUpdateNameGroupKala = get_option('notUpdateNameGroupKala');
		if ($notUpdateNameGroupKala == 'on') {
			if ( isset( $request['name'] ) ) {
				if ( $request['name'] != '' && $request['name'] != null ) {
					$res = wp_update_term( $categoryObject[0]->term_id, 'product_cat', array ( 'name' => $request['name'] ) );
				} else {
					add_log("Amin_Basic_Product_Categories_Rest: edit_item => error 7");
					return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The name provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
				}
			}
		}

		if ( isset( $request['typeLevel'] ) ) {
			$abpTypeLevel = intval( $request['typeLevel'] );
			if ( $abpTypeLevel == 1 || $abpTypeLevel == 2 && $abpTypeLevel != null ) {
				$res = update_term_meta( $categoryObject[0]->term_id, 'abpTypeLevel', $abpTypeLevel );
			} else {
				add_log("Amin_Basic_Product_Categories_Rest: edit_item => error 8");
				return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The type level provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
			}
		}

		if ( isset( $request['typeShow'] ) ) {
			$abpTypeShow = intval( $request['typeShow'] );
			if ( $abpTypeShow == 2 || $abpTypeShow == 1 && $abpTypeShow != null ) {
				$res = update_term_meta( $categoryObject[0]->term_id, 'abpTypeShow', $abpTypeShow );
			} else {
				add_log("Amin_Basic_Product_Categories_Rest: edit_item => error 9");
				return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The type show provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
			}
		}

		if ( $res || ! is_wp_error( $res ) ) {
			$data['code'] = Amin_Basic_Response_Code::Category_Updated;
			$data['message'] = __( 'Product category has been successfully updated.', '' );
			$data['data'] = array('status' => 200);
		} else {
			add_log("Amin_Basic_Product_Categories_Rest: edit_item => error 10");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

		add_log("Amin_Basic_Product_Categories_Rest: edit_item => succeed");
		return rest_ensure_response( $data );
	}

	/**
	 * Delete product category
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function delete_item( $request ) {

		$data = [];
		if ( ! isset( $request['recordID'] ) ) {
			add_log("Amin_Basic_Product_Categories_Rest: delete_item => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}

		$abpRecordId = $request['recordID'];
		if ( $abpRecordId != null ) {
			$args = array(
				'hide_empty' => false,
				'meta_query' => array(
					array(
					   'key'       => 'abpRecordId',
					   'value'     => $abpRecordId,
					   'compare'   => '='
					)
				),
				'taxonomy'  => 'product_cat',
			);
			$categoryObject = get_terms( $args );

			if ( empty( $categoryObject ) || is_wp_error( $categoryObject ) ) {
				add_log("Amin_Basic_Product_Categories_Rest: delete_item => error 2");
				return new WP_Error( Amin_Basic_Response_Code::Category_Id_Not_Found, __( 'The record id provided doesn\'t exist.' ), array( 'status' => 400 ) );
			}
		} else {
			add_log("Amin_Basic_Product_Categories_Rest: delete_item => error 3");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The record id provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}
		
		$res = wp_delete_term( $categoryObject[0]->term_id, 'product_cat' );

		if ( $res ) {
			$data['code'] = Amin_Basic_Response_Code::Category_Deleted;
			$data['message'] = __( 'Product category has been successfully deleted.', '' );
			$data['data'] = array('status' => 200);
		} else {
			add_log("Amin_Basic_Product_Categories_Rest: delete_item => error 4");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}

		add_log("Amin_Basic_Product_Categories_Rest: delete_item => succeed");
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
}