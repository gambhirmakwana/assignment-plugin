<?php
/*
Plugin Name: Simple Alert
Plugin URI: 
Description: Simple alert plugin
Version: 1.0
*/
function smallenvelop_login_message( $message ) {
    if ( empty($message) ){
        return "<p><strong>Welcome to SmallEnvelop. Please login to continue</strong></p>";
    } else {
        return $message;
    }
}

add_filter( 'login_message', 'smallenvelop_login_message' );

function plugin_activation() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'simple_alert';
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	id mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
	post_ids varchar(300),
	title varchar(50) NOT NULL,
	PRIMARY KEY  (id)
);";
$wpdb->query($sql);
}
function plugin_deactivation(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'simple_alert';
	$sql = "DROP TABLE IF EXISTS $table_name";
	$wpdb->query($sql);
}
register_activation_hook( __FILE__, 'plugin_activation' );
register_deactivation_hook( __FILE__, 'plugin_deactivation' );

add_action('admin_menu', 'add_submenu_setting');
function add_submenu_setting() {
	add_options_page( '', 'Simple Alert', 'manage_options', 'my-unique-identifier', 'simple_alert');
}
function simple_alert() {

$arr = array("post" => array(0,4));
	?>
    <!-- end of the loop -->
		<div class="wrap" id="wp-media-grid" data-search="">
			<h1 class="wp-heading-inline">Simple Alert Setting</h1>
		</div>
		<form action="#" method="post">
			<div class="main-simle">
				<div class="form-controll">
					<input type="text" name="title" value="" class="form-inputs">
				</div>

				<div class="form-controll">
					<input type="checkbox" name="post_title" value="posts" class="form-inputs posts"> - Post
					<div class="as-post">
						<?php
						$wpb_all_query = new WP_Query(array('post_type'=>'post', 'post_status'=>'publish', 'posts_per_page'=>-1)); ?>
						<?php if($wpb_all_query->have_posts()) : ?>
							<!-- the loop -->
							<select multiple="multiple" name='posts[]'>
						    <?php while($wpb_all_query->have_posts()) : $wpb_all_query->the_post(); ?>
						        <option value="<?php echo get_the_ID(); ?>"><?php the_title(); ?></option>
						    <?php endwhile; ?>
						    </select>
						<?php endif; ?>
					</div>
				</div>
				<div class="form-controll">
					<input type="checkbox" name="page_title" value="page" class="form-inputs pages"> - Page
					<div class="as-page">
						<?php
						$wpb_all_query = new WP_Query(array('post_type'=>'page', 'post_status'=>'publish', 'posts_per_page'=>-1)); ?>
						<?php if($wpb_all_query->have_posts()) : ?>
							<!-- the loop -->
							<select multiple="multiple" name='posts[]'>
						    <?php while($wpb_all_query->have_posts()) : $wpb_all_query->the_post(); ?>
						        <option value="<?php echo get_the_ID(); ?>"><?php the_title(); ?></option>
						    <?php endwhile; ?>
						    </select>
						<?php endif; ?>
					</div>
				</div>
				<div class="form-controll">
					<input id="ajax_form" type="submit" name="btnSubmit" value="Submit">
				</div>
			</div>
		</form>
	<?php
}
function add_assets() {
    wp_register_style('simple-alert-css', plugins_url('css/style.css',__FILE__ ));
    wp_enqueue_style('simple-alert-css');

    wp_register_script('simple-alert-js', plugins_url('js/script.js',__FILE__ ));
    wp_enqueue_script('simple-alert-js');
}
add_action('admin_init','add_assets');
function remove_cssjs_ver( $src ) {
if( strpos( $src, '?ver=' ) )
$src = remove_query_arg( 'ver', $src );
return $src;
}
add_filter( 'style_loader_src', 'remove_cssjs_ver', 10, 2 );
add_filter( 'script_loader_src', 'remove_cssjs_ver', 10, 2 );

if(isset($_POST['btnSubmit']))
{
	global $wpdb;
	// echo $_POST['title'];die;
	$posts=$_POST['posts'];

	foreach($posts as $value) {
		$pst .=$value.",";
	}
	$wpdb->insert($wpdb->prefix.'simple_alert', array(
	    'post_ids' => $pst,'title'=>$_POST['title'] // ... and so on
	));

}

function add_to_header() {
    wp_enqueue_script('jquery'); 
} 
add_action( 'wp_enqueue_scripts', 'add_to_header' );


/**
 * Add shortcode to show form and script
 */

function add_word( ) {      

$script = "
<script>
jQuery('#ajax_form').bind('submit', function() {
    alert('clicked');
    var form = jQuery('#ajax_form');
    var data = form.serialize();
    data.action = 'add_word_to_form'
    jQuery.post('/wp-admin/admin-ajax.php', data, function(response) {
        alert(data);           
    });
    return false;
});
</script>";

$form ='
<form class="form" id="ajax_form">                            
    <input type="text" name="name" id="name"  placeholder="Word Goes Here" required="">
    <input type="submit" value="Submit" id="submitaddword">
</form>';

return $script.$form;


}
add_shortcode( 'addword', 'add_word' );


/** 
 * Set up AJAX call to add word
 */
add_action("wp_ajax_add_word_to_form", "add_word_to_form");
add_action("wp_ajax_nopriv_add_word_to_form", "add_word_to_form");

function add_word_to_form ($data)
{
    return "Hello".$data;
}

function your_function() {
    $crnt_id=get_the_ID();
	global $wpdb;
	$table_name = $wpdb->prefix . 'simple_alert';
	$sql = "SELECT * FROM $table_name";
	$result = $wpdb->get_results($sql);
	$cnt=0;
    foreach( $result as $results ) {
	    $pst_ids=$result[$cnt]->post_ids;
	    $txt=$result[$cnt]->title;
	    $cnt=count($pst_ids);
	    if($pst_ids!='')
	    {
	    	$epst_ids=explode(",",$pst_ids);
	    	$ecnt=count($epst_ids);

	    	for($a=0; $a<$ecnt;$a++)
			{
				if($epst_ids[$a]==$crnt_id)
				{
					echo "<script> alert('".$txt."'); </script>";
				}

			}
	    }
	    $cnt+=1;
    }
}
add_action( 'wp_footer', 'your_function' );