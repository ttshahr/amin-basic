<?php

/**
 * Add rest api endpoint for customers
 *
 * @link       https://AliJannesary.ir
 * @since      1.0.0
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/includes
 */

/**
 * Add rest api endpoint for customers.
 *
 * Maintain GetNewUsers and setClubID methods for customers.
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/includes
 * @author     Ali Jannesary <https://AliJannesary.ir>
 */
class Amin_Basic_Customers_Rest extends WC_REST_Customers_Controller {
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
		$this->rest_base = 'customers';
	}

	/**
	 * Register the routes for the objects of the controller.
     * 
     * @since    1.0.0
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/GetNewUsers', array(

			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_new_users' ),
				'permission_callback' => array( $this, 'get_new_users_permissions_check' )
			),
			'schema' => null

		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/setClubID', array(

			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'set_club_id' ),
				'permission_callback' => array( $this, 'set_club_id_permissions_check' )
			),
			'schema' => null

		) );
		
		// add_log("Amin_Basic_Customers_Rest: register_routes => ...");
	}

	/**
	 * Check permissions for the GetNewUsers.
	 *
     * @since    1.0.0
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   bool|WP_Error
	 */
	public function get_new_users_permissions_check( $request ) {
		if ( ! current_user_can( 'read' ) ) {
			add_log("Amin_Basic_Customers_Rest: get_new_users_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot get the customer resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Customers_Rest: get_new_users_permissions_check => succeed");
		return true;
	}

	/**
	 * Check permissions for the setClubID.
	 *
     * @since    1.0.0
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   bool|WP_Error
	 */
	public function set_club_id_permissions_check( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			add_log("Amin_Basic_Customers_Rest: set_club_id_permissions_check => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'You cannot edit the customer resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		add_log("Amin_Basic_Customers_Rest: set_club_id_permissions_check => succeed");
		return true;
	}

	/**
	 * Get new users
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function get_new_users( $request ) {

		$result = [];
		$args = array(
			'role'    => 'Customer',
			'meta_key' => 'abpCustomerId',
			'meta_compare' => 'NOT EXISTS'
		);
		$newUsers = get_users( $args );
		foreach ( $newUsers as $newUser ) {
			$userMeta = get_user_meta( $newUser->ID );

			$userData = [];
			$userData['customerName']    = $newUser->display_name;
			$userData['cityName']        = $userMeta['billing_city'][0];
			$userData['townshipName']    = WC()->countries->states[$userMeta['billing_country'][0]][$userMeta['billing_state'][0]];
			$userData['codeMeli']        = $userMeta['abpCodeMeli'][0];
			$userData['postCode']        = $userMeta['billing_postcode'][0];
			$userData['address']         = $userMeta['billing_address_1'][0];
			$userData['email']           = $newUser->user_email;
			$userData['mobile']          = $userMeta['digits_phone'][0]; //apbMobile
			$userData['webSite']         = $newUser->user_url;
			$userData['phone']           = $userMeta['billing_phone'][0];
			$userData['birthDate']       = $userMeta['abpBirthDate'][0];
			$userData['marriageDate']    = $userMeta['abpMarriageDate'][0];
			$userData['spouseBirthDate'] = $userMeta['abpSpouseBirthDate'][0];
			$userData['bairn1BirthDate'] = $userMeta['abpBairn1BirthDate'][0];
			$userData['bairn2BirthDate'] = $userMeta['abpBairn2BirthDate'][0];
			$userData['bairn3BirthDate'] = $userMeta['abpBairn3BirthDate'][0];
			$userData['bairn4BirthDate'] = $userMeta['abpBairn4BirthDate'][0];
			$userData['bairn5BirthDate'] = $userMeta['abpBairn5BirthDate'][0];
			$userData['bairn6BirthDate'] = $userMeta['abpBairn6BirthDate'][0];
			$userData['recordID']        = $newUser->ID;
			
			$tempMobile = $userData['mobile'];
			
			if(mb_strlen( $tempMobile ) > 10){

				if(substr($tempMobile, 0, 4) == "+980"){
					$tempMobile = str_replace("+980", "0", $tempMobile);
				}
				else if(substr($tempMobile, 0, 4) == "+989"){
					$tempMobile = str_replace("+989", "09", $tempMobile);
				}				
				else if(substr($tempMobile, 0, 4) == "0989"){
					$tempMobile = str_replace("0989", "09", $tempMobile);
				}
				else if(substr($tempMobile, 0, 3) == "989"){
					$tempMobile = str_replace("989", "09", $tempMobile);
				}
				else if(substr($tempMobile, 0, 3) == "098"){
					$tempMobile = str_replace("098", "09", $tempMobile);
				}
			}

			if(mb_strlen( $tempMobile ) != 11)
				add_log("Amin_Basic_Customers_Rest: get_new_users => warning mobile: " . $userData['mobile'] . " customer Name: " . $userData['customerName']);

			if(mb_strlen( $userData['postCode'] ) != 10)
				add_log("Amin_Basic_Customers_Rest: get_new_users => warning postCode: " . $userData['postCode'] . " customer Name: " . $userData['customerName']);
			
			if(mb_strlen( $userData['email'] ) > 50)
				add_log("Amin_Basic_Customers_Rest: get_new_users => warning email: " . $userData['email'] . " customer Name: " . $userData['customerName']);

			array_push( $result, $userData );
		}

		if ( count( $result ) >= 0 ) {
			$data['data'] = array('status' => 200, 'result' => $result);
		} else {
			add_log("Amin_Basic_Customers_Rest: get_new_users => error");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Internal error.' ), array( 'status' => 400 ) );
		}
		
		add_log("Amin_Basic_Customers_Rest: get_new_users => succeed");
		return rest_ensure_response( $data );
	}

	/**
	 * Set club id
	 *
	 * @param    WP_REST_Request         $request get data from request.
	 * @return   mixed|WP_Error|WP_REST_Response
	 */
	public function set_club_id( $request ) {

		$data = [];
		if ( ! isset( $request['recordID'] ) || ! isset( $request['customerID'] ) ) {
			add_log("Amin_Basic_Customers_Rest: set_club_id => error 1");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Please provide all required parameters.' ), array( 'status' => 400 ) );
		}

		$abpRecordId = intval( $request['recordID'] );
		if ( $abpRecordId <= 0 || $abpRecordId == null ){
			add_log("Amin_Basic_Customers_Rest: set_club_id => error 2");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The record id provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}
		$abpCustomerId = intval( $request['customerID'] );
		if ( $abpCustomerId <= 0 || $abpCustomerId == null ){
			add_log("Amin_Basic_Customers_Rest: set_club_id => error 3");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'The customer id provided doesn\'t in correct format.' ), array( 'status' => 400 ) );
		}
		$args = array(
			'role'    => 'Customer',
			'meta_key' => 'abpCustomerID',
			'meta_value' => $abpCustomerId,
			'meta_compare' => '='
		);
		$userExist = get_users( $args );
		if ( count( $userExist ) > 0 ) {
			add_log("Amin_Basic_Customers_Rest: set_club_id => error 4 --> CustomerID: $abpCustomerId  RecordId: $abpRecordId");
			return new WP_Error( Amin_Basic_Response_Code::Customer_Id_Exist, __( 'The customer id provided already exist.' ), array( 'status' => 400 ) );
		}

		$userObject = get_user_by( 'ID', $request['recordID'] );

		if ( empty( $userObject ) || is_wp_error( $userObject ) ) {
			add_log("Amin_Basic_Customers_Rest: set_club_id => error 5");
			return new WP_Error( Amin_Basic_Response_Code::Customer_Id_Not_Found, __( 'The record id provided doesn\'t exist.' ), array( 'status' => 400 ) );
		}

		$res = update_user_meta( $userObject->ID, 'abpCustomerID', $request['customerID'] );

		if ( $res ) {
			$data['code'] = Amin_Basic_Response_Code::Customer_Updated;
			$data['message'] = __( 'Customer club id has been successfully set.', '' );
			$data['data'] = array('status' => 200);
		} else {
			add_log("Amin_Basic_Customers_Rest: set_club_id => error 6");
			return new WP_Error( Amin_Basic_Response_Code::Other_Errors, __( 'Customer club id already set.' ), array( 'status' => 400 ) );
		}

		add_log("Amin_Basic_Customers_Rest: set_club_id => succeed");
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