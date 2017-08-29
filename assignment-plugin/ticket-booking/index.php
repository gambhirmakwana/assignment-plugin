<?php
/*
Plugin Name: Ticket Booking
Plugin URI: 
Description: Simple Bus Ticket Book Demo
Version: 1.0
*/
function plugin_activation_ticket() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'ticket_booking';
	$column_name = "CREATE TABLE IF NOT EXISTS $table_name ( id mediumint(9) unsigned NOT NULL AUTO_INCREMENT,user_id mediumint(10),email varchar(300),mobile_no varchar(15),subject varchar(250),message text,";
	for($i = 1; $i <= 100; $i++){
		$column_name .= "ticket_".$i.' int(10) default 0,';
	}
	$column_name .= "create_ate date,update_at date,PRIMARY KEY  (id));";
	$wpdb->query($column_name);
}
function plugin_deactivation_ticket(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'ticket_booking';
	$sql = "drop table if exists $table_name";
	$a = $wpdb->query($sql);
}
register_activation_hook( __FILE__, 'plugin_activation_ticket' );
register_deactivation_hook( __FILE__, 'plugin_deactivation_ticket' );

add_action('admin_menu', 'add_submenu_setting_ticket');
function add_submenu_setting_ticket() {
	add_options_page( '', 'Ticket Booking Add On', 'manage_options', 'my-unique-identifier1', 'simple_ticket_booking');
}
function simple_ticket_booking() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'ticket_booking';
	$sql = "SELECT * FROM $table_name";
	$results = $wpdb->get_results($sql);		
	
?>
	<div class="wrap" id="wp-media-grid" data-search="">
		<h1 class="wp-heading-inline">Ticket Booking</h1>
	</div>
	<div>
		<p>[ticket_book_cf7]</p>
	</div>
	<div class="main-ticket">
		
	</div>
<?php
}
function add_assets_ticket() {
    wp_register_style('ticket-addons-css', plugins_url('css/ticket_booking_addon.css',__FILE__ ));
    wp_enqueue_style('ticket-addons-css');

    wp_register_script('ticket-addons-js', plugins_url('js/ticket_booking_addon.js',__FILE__ ));
    wp_enqueue_script('ticket-addons-js');
}
function ticket_bookind_form(){
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
		<?php
			$curre_user_id = get_current_user_id();
			global $wpdb;
			$table_name = $wpdb->prefix . 'ticket_booking';
			$sql = "SELECT * FROM $table_name WHERE user_id = $curre_user_id";
			$results = $wpdb->get_results($sql);
			$column_name = "";
			for($i = 1; $i <= 100; $i++){
				$column_name = "ticket_$i";
				// echo $column_name;die;
				if($results[0]->$column_name == 1){
					echo '<input disabled type="checkbox" name="ticket_'.$i.'" value="1" checked>SeatNo:'.$i.'';	
				}else{
					echo '<input type="checkbox" name="ticket_'.$i.'" value="1">SeatNo:'.$i.'';	
				}
				// echo '<input type="checkbox" name="ticket_'.$i.'" value="1">SeatNo:'.$i.'';
			}
		?>
<?php
	if(isset($_POST['submit'])){
		$email = $_POST['email'];
		$curre_user_id = get_current_user_id();
		global $wpdb;
		$table_name = $wpdb->prefix . 'ticket_booking';
		$ticket_book_no = "";
		for($i = 1; $i <= 100; $i++){
			$name = "ticket_".$i;
			$val = isset($_POST[$name]) ? $_POST[$name] : 0;
			$ticket_book_no .= " ".$name." = ".$val.", ";
		}
		$seatData = substr(trim($ticket_book_no), 0,-1);
		$curre_user_id = get_current_user_id();
		$sql = "SELECT * FROM $table_name WHERE user_id = $curre_user_id";
		$results = $wpdb->get_results($sql);
		// print_r($results);die;
		if($results[0]->user_id == $curre_user_id){
			$sql = "UPDATE $table_name SET user_id = ".$curre_user_id.", email = '".$email."', ".$seatData." WHERE user_id = ".$curre_user_id."";
			$wpdb->query($sql);
		}else{
			$sql = "INSERT INTO  $table_name SET user_id = ".$curre_user_id.", email = '".$email."', ".$seatData."";
			$wpdb->query($sql);
		}
	}
}
wpcf7_add_shortcode("ticket_book_cf7","ticket_bookind_form");
// add_sortcode("ticket_bookind_form","abs");
add_action('admin_init','add_assets_ticket');
function remove_cssjs_ver_ticket( $src ) {
if( strpos( $src, '?ver=' ) )
$src = remove_query_arg( 'ver', $src );
return $src;
}
add_filter( 'style_loader_src', 'remove_cssjs_ver_ticket', 10, 2 );
add_filter( 'script_loader_src', 'remove_cssjs_ver_ticket', 10, 2 );