<h1><?php echo get_admin_page_title(); ?></h1>
<?php

/**
 *
 * @link       https://AliJannesary.ir
 * @since      1.0.0
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/admin/partials
 */

global $wpdb, $table_prefix;

$notice = false;

$gateways = WC()->payment_gateways->get_available_payment_gateways();

$orderStatuses = wc_get_order_statuses();

$newOrderStatusesTable = $table_prefix . 'amin_basic_new_order_statuses';

if( isset( $_POST['submit'] ) && 
	$_POST['gateway'] != '' && 
	$_POST['order_status'] != '' ) {
	
	$wpdb->insert(
		$newOrderStatusesTable,
		array( 'gateway' => $_POST['gateway'], 'order_status' => $_POST['order_status'] ),
		array( '%s', '%s' )
	);

	$notice = [
		'type' => 'success',
		'message' => 'ذخیره با موفقیت انجام شد',
	];

}

if( isset( $_GET['delete'] ) ) {
	$wpdb->delete(
		$newOrderStatusesTable,
		array( 'id' => $_GET['delete'] ),
		array( '%d' )
	);

	$notice = [
		'type' => 'success',
		'message' => 'حذف با موفقیت انجام شد',
	];
}

$newOrderStatuses = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$newOrderStatusesTable} where 1=%s" , 1) );

//print_r($gateways);

$newOrderStatusRows = "";

foreach( $newOrderStatuses as $newOrderStatus ) {
	
	$deleteLink = add_query_arg( 'delete', $newOrderStatus->id );
	
	$newOrderStatusRows .= "<tr><td>{$gateways[$newOrderStatus->gateway]->get_title()}</td><td>{$orderStatuses[$newOrderStatus->order_status]}</td><td><a href=\"{$deleteLink}\">حذف</a></td></tr>";
	
	unset($gateways[$newOrderStatus->gateway]);
}

$gatewaysOptions = "";

foreach( $gateways as $gateway ) {

	if( $gateway->enabled == 'yes' ) {
		$gatewaysOptions .= '<option value="' . esc_attr( $gateway->id ) . '">' . wp_kses_post( $gateway->get_title() ) . '</option>';
    }
}

$orderStatusOptions = "";

foreach( $orderStatuses as $statusSlug => $statusLabel ) {
	$orderStatusOptions .= '<option value="' . $statusSlug . '">' . $statusLabel . '</option>';
}

echo '<form action="" method="POST">';

if( $notice ){
    echo '<div class="notice notice-'.$notice['type'].'"> <p> '.$notice['message'].'</p></div>';
}

echo '<label for="gateway">شیوه پرداخت: </label>';
echo '<select name="gateway">' . $gatewaysOptions . '</select>';

echo '<label for="order_status">وضعیت سفارش: </label>';
echo '<select name="order_status">' . $orderStatusOptions . '</select>';
echo '<button type="submit" name="submit" class="button button-primary" >ذخیره</button>';

echo '
<table class="amin-basic-table">
	<thead>
		<th>روش پرداخت</th>
		<th>وضعیت سفارش جدید</th>
		<th>عملیات</th>
	</thead>
	<tbody>'
	. 
	$newOrderStatusRows
	.
	'</tbody>
</table>
';
?>