<?php
/*
Plugin Name: Post Type Plugin
Description: A highly documented plugin that demonstrates how to create custom post type using plugin.
Version:     1.0
Author:      Shailaja Bhagat
Author URI:  https://github.com/shailaja-bhagat/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) || die;

class Post_Type_Plugin {
	public function __construct() {
		add_action( 'init', [ $this, 'create_post_type_plugin' ], 0 );
	}

	// Register Custom Post Type
	public function create_post_type_plugin() {

		$labels = array(
			'name'                  => _x( 'Admin Settings', 'Post Type General Name', 'feelya' ),
			'singular_name'         => _x( 'Feelya admin setting', 'Post Type Singular Name', 'feelya' ),
			'menu_name'             => __( 'Admin Settings', 'feelya' ),
			'name_admin_bar'        => __( 'Feelya admin settings', 'feelya' ),
			'archives'              => __( 'Feelya admin setting Archives', 'feelya' ),
			'attributes'            => __( 'Feelya admin setting Attributes', 'feelya' ),
			'parent_item_colon'     => __( 'Parent Item:', 'feelya' ),
			'all_items'             => __( 'Notifications', 'feelya' ),
			'add_new_item'          => __( 'Add New admin setting', 'feelya' ),
			'add_new'               => __( 'Add New', 'feelya' ),
			'new_item'              => __( 'New admin setting', 'feelya' ),
			'edit_item'             => __( 'Edit admin setting', 'feelya' ),
			'update_item'           => __( 'Update admin setting', 'feelya' ),
			'view_item'             => __( 'View admin setting', 'feelya' ),
			'view_items'            => __( 'View admin settings', 'feelya' ),
			'search_items'          => __( 'Search admin setting', 'feelya' ),
			'not_found'             => __( 'Not found', 'feelya' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'feelya' ),
			'featured_image'        => __( 'Featured Image', 'feelya' ),
			'set_featured_image'    => __( 'Set featured image', 'feelya' ),
			'remove_featured_image' => __( 'Remove featured image', 'feelya' ),
			'use_featured_image'    => __( 'Use as featured image', 'feelya' ),
			'insert_into_item'      => __( 'Insert into admin setting', 'feelya' ),
			'uploaded_to_this_item' => __( 'Uploaded to this admin setting', 'feelya' ),
			'items_list'            => __( 'admin settings list', 'feelya' ),
			'items_list_navigation' => __( 'admin settings list navigation', 'feelya' ),
			'filter_items_list'     => __( 'Filter admin settings list', 'feelya' ),
		);
		$support = array( 'title', 'editor','author', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions', 'post-formats', );
		$args   = array(
			'label'               => __( 'admin_setting', 'feelya' ),
			'description'         => __( 'Therapy admin setting information', 'feelya' ),
			'labels'              => $labels,
			'supports'            => $support,
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 4,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
         
			// This is where we add taxonomies to our CPT
			'taxonomies'          => array( 'category' ),
		);
		register_post_type( 'cpt_plugin', $args );
	}
}

$post_type_plugin = new Post_Type_Plugin();



add_action('admin_menu', 'settings');

function settings()
{
	add_submenu_page( 'edit.php?post_type=cpt_plugin', 'settings', 'Sender Email Addresses', 'manage_options', 'setting-rules', 'setting_rules_main');
}

add_action('feelya_admin_init', 'setting_rules');

function setting_rules() {
    register_setting('fasbr_group', 'fasbr_option');
}

function setting_rules_main() { ?>
<div class="container">
	<div class="breadcrumb">
		<a href="<?php echo site_url().'/wp-admin/edit.php?post_type=cpt_plugin'; ?>"><span>Admin Settings</span></a>
		<i class="material-icons">>></i>
	</div>
	<div class="breadcrumb">
		<span>Sender Email Addresses</span>
	</div>
</div>
	<!-- <h1>Booking Rules</h1> -->
	<?php
			global $wpdb;
			if(isset($_POST['submit']) && !empty($_POST)){
				
					$tableName = $wpdb->prefix . 'feelya_settings';
					$query = $wpdb->update( $tableName, [
						'client_email' => $_POST['client_email'],
						'therapist_email'   => $_POST['therapist_email'],
						'additional_recipients_mail' => $_POST['additional_recipients_mail']
					],  array( 'settings_id' => 1 ), [
						'%s',
						'%s',
						'%s',
						'%s',
						'%s'
					] );
					
			}
			$query = "SELECT * FROM wp_feelya_settings";
			$res=$wpdb->get_results($query);
			//print_r($res);
			
		?>
	<form method="post" action="">

		<!-- Contact Form Details BLOCK -->
		<div class="google-captcha section-block">
			<h1>Sender Email Addresses</h1>
			<div class="section-block-inner bookingstyle">
				<table>
					<tr>
					<th>Email notifications to Clients will be sent from </th>
						<td>
						<div class="modifystyle">
							<select name="client_email" class="dropdown-menu">
								<option value="support@feelya.com" <?php if($res[0]->client_email=='support@feelya.com'){ echo 'selected'; }?>>support@feelya.com</option>
								<option value="talk@feelya.com" <?php if($res[0]->client_email=='talk@feelya.com'){ echo 'selected'; }?>>talk@feelya.com</option>
								<option value="subodh@arkenea.com" <?php if($res[0]->client_email=='subodh@arkenea.com'){ echo 'selected'; } ?>>subodh@arkenea.com</option>
								<option value="ravinaw@arkenea.com" <?php if($res[0]->client_email=='ravinaw@arkenea.com'){ echo 'selected';} ?>>ravinaw@arkenea.com</option>
								<option value="shailaja@arkenea.com" <?php if($res[0]->client_email=='shailaja@arkenea.com'){ echo 'selected'; } ?>>shailaja@arkenea.com</option>
							</select>
						</div>
						</td>
					</tr>
					<tr>
					<th>Email notifications to Therapists will be sent from </th>
						<td>
						<div class="modifystyle">
							<select name="therapist_email" class="dropdown-menu">
								<option value="support@feelya.com" <?php if($res[0]->therapist_email=='support@feelya.com'){ echo 'selected'; }?>>support@feelya.com</option>
								<option value="talk@feelya.com" <?php if($res[0]->therapist_email=='talk@feelya.com'){ echo 'selected'; }?>>talk@feelya.com</option>
								<option value="subodh@arkenea.com" <?php if($res[0]->therapist_email=='subodh@arkenea.com'){ echo 'selected'; }?>>subodh@arkenea.com</option>
								<option value="ravinaw@arkenea.com" <?php if($res[0]->therapist_email=='ravinaw@arkenea.com'){ echo 'selected'; }?>>ravinaw@arkenea.com</option>
								<option value="shailaja@arkenea.com" <?php if($res[0]->therapist_email=='shailaja@arkenea.com'){ echo 'selected'; }?>>shailaja@arkenea.com</option>
							</select>
						</div>
						</td>
					</tr>
					<tr>
					<th>Email notifications to Additional recipients will be sent from </th>
						<td>
						<div class="modifystyle">
							<select name="additional_recipients_mail" class="dropdown-menu">
								<option value="support@feelya.com" <?php if($res[0]->additional_recipients_mail=='support@feelya.com'){ echo 'selected'; }?>>support@feelya.com</option>
								<option value="talk@feelya.com" <?php if($res[0]->additional_recipients_mail=='talk@feelya.com'){ echo 'selected'; }?>>talk@feelya.com</option>
								<option value="subodh@arkenea.com" <?php if($res[0]->additional_recipients_mail=='subodh@arkenea.com'){ echo 'selected'; }?>>subodh@arkenea.com</option>
								<option value="ravinaw@arkenea.com" <?php if($res[0]->additional_recipients_mail=='ravinaw@arkenea.com'){ echo 'selected'; }?>>ravinaw@arkenea.com</option>
								<option value="shailaja@arkenea.com" <?php if($res[0]->additional_recipients_mail=='shailaja@arkenea.com'){ echo 'selected'; }?>>shailaja@arkenea.com</option>
							</select>
						</div>
						</td>
					</tr>
					<tr>
						<td>
							<input type="submit" class="button-primary save-button" name="submit" value="Save Changes">
						</td>
						<th></th>
					</tr>
				</table>
			</div>
		</div>

	</form>
	<style>
		a:link {
			text-decoration: none;
			}
		.container {
			height: 64px;
			display: flex;
			align-items: center;
		}
				div.breadcrumb {
			display: flex;
			align-items: center;
		}

		span {
			font-family: Roboto;
			font-weight: 300;
			font-size: 20px;
		}

		i {
			margin: 0 8px;
		}

		jimbo > i:last-child {
			display: none;
		}
		.info_span{
			font-size: 14px;margin-top: -5px;display: block;
		}
		.dropdown-menu {
 		 min-width: 60px !important;
		  margin-left: 10px;
		}
		.section-block input[type="text"], .section-block textarea, .section-block select {
			min-width: 500px;
			margin: 0 0 10px;
		}
		.section-block textarea {
			min-height: 100px;
		}
		.section-block table {
			border-collapse: collapse;
			width: 100%;
		}
		.section-block tr {
			border-bottom: 0px solid transparent;
		}
		.section-block td, .section-block th {
			padding-right: 30px;
		}
		.section-block td h2 {
			font-weight: 700;
			font-weight: 700;
			font-size: 16px;
			line-height: 20px;
			border-bottom: 1px solid;
			padding-bottom: 5px;
		}
		.section-block th {
			text-align: left;
			/*min-width: 130px;*/
			max-width: 100%;
			vertical-align: top;
			padding-top: 5px;
		}
		.section-block-inner{
			padding: 20px;
		}
		.section-block {
			
			
			width: 95%;
			margin: 30px 0;
		
		}
		.section-block h1 {
			margin: 0 0 0;
			line-height: 50px;
			padding-left: 20px;
			text-transform: uppercase;
			font-size: 20px;
		}
		.section-block h3 {
			margin-top: 0;
		}
		.switch-field {
			display: flex;
			overflow: hidden;
		}

		.switch-field input {
			position: absolute !important;
			clip: rect(0, 0, 0, 0);
			height: 1px;
			width: 1px;
			border: 0;
			overflow: hidden;
		}

		.switch-field label {
			background-color: #e4e4e4;
			color: rgba(0, 0, 0, 0.6);
			font-size: 14px;
			line-height: 1;
			text-align: center;
			padding: 8px 16px;
			margin-right: -1px;
			border: 1px solid rgba(0, 0, 0, 0.2);
			box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
			transition: all 0.1s ease-in-out;
		}

		.switch-field label:hover {
			cursor: pointer;
		}

		.switch-field input:checked + label {
			background-color: #a9a9a9;
			box-shadow: none;
		}

		.switch-field label:first-of-type {
			border-radius: 4px 0 0 4px;
		}

		.switch-field label:last-of-type {
			border-radius: 0 4px 4px 0;
		}

		/* This is just for CodePen. */

		.form {
			max-width: 600px;
			font-family: "Lucida Grande", Tahoma, Verdana, sans-serif;
			font-weight: normal;
			line-height: 1.625;
			margin: 8px auto;
			padding: 16px;
		}

		h2 {
			font-size: 18px;
			margin-bottom: 8px;
		}

		.section-block-inner.bookingstyle table tbody tr th{
			width: 50%;
		}
		.section-block-inner.bookingstyle table tbody tr td .modifystyle{
			display: flex;
    		align-items: center;
			margin-bottom: 25px;
		}
		.section-block-inner.bookingstyle table tbody tr td .modifystyle select{
			margin: 0 15px 0 0;
		}
		.section-block-inner.bookingstyle table tbody tr td .save-button{
			margin-top: 15px;
		}

	</style>
<?php 


}


// Reminders Sub menu

add_action('admin_menu', 'reminders');

function reminders()
{
	add_submenu_page( 'edit.php?post_type=cpt_plugin', 'Reminders', 'Reminders', 'manage_options', 'reminder', 'reminders_main');
}

add_action('feelya_admin_init', 'register_reminders');

function register_reminders() {
    register_setting('fasr_group', 'fasr_option');
}

function reminders_main() { ?>

<div class="container">
	<div class="breadcrumb">
		<a href="<?php echo site_url().'/wp-admin/edit.php?post_type=cpt_plugin'; ?>"><span>Admin Settings</span></a>
		<i class="material-icons">>></i>
	</div>
	<div class="breadcrumb">
		<span>Reminders</span>
	</div>
</div>
	<!-- <h1>Booking Rules</h1> -->
	<?php
		global $wpdb;
		if(isset($_POST['submit']) && !empty($_POST)){
			
				$tableName = $wpdb->prefix . 'feelya_reminders';
				$query = $wpdb->update( $tableName, [
					'respond_req_days' => $_POST['respond_req_days'],
					'respond_req_on_off'   => $_POST['respond_req_on_off'],
					'upcoming_appt_days' => $_POST['upcoming_appt_days'],
					'upcoming_appt_on_off'     => $_POST['upcoming_appt_on_off']
				],  array( 'reminder_id' => 1 ), [
					'%s',
					'%s',
					'%s',
					'%s',
					'%s'
				] );
				
		}
		$query = "SELECT * FROM wp_feelya_reminders";
		$res=$wpdb->get_results($query);
		
	?>
	<form method="post" action="">

		<!-- Contact Form Details BLOCK -->
		<div class="google-captcha section-block">
			<h1>Reminders</h1>
			<div class="section-block-inner bookingstyle">
				<table>
					
					<tr>
						<th>Number of days, after request is made in which reminder to respond will be send</th>
							<td>
							<div class="modifystyle">
								<select name="respond_req_days" class="dropdown-menu">
										<option value="2" <?php if($res[0]->respond_req_days=='2'){ echo 'selected'; } ?>>2</option>
										<option value="4" <?php if($res[0]->respond_req_days=='4'){ echo 'selected'; } ?>>4</option>
										<option value="8" <?php if($res[0]->respond_req_days=='8'){ echo 'selected'; } ?>>8</option>
								</select>
								<div class="switch-field" >
									<input type="radio" id="radio-one" name="respond_req_on_off" value="on" <?php if($res[0]->respond_req_on_off=='on'){?> checked="checked" <?php } ?>/>
									<label for="radio-one">ON</label>
									<input type="radio" id="radio-two" name="respond_req_on_off" value="off" <?php if($res[0]->respond_req_on_off=='off'){ ?> checked="checked" <?php } ?>/>
									<label for="radio-two">OFF</label>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<th>Number of days before appointment, in which to send reminder </th>
							<td>
							<div class="modifystyle">
								<select name="upcoming_appt_days" class="dropdown-menu">
										<option value="2" <?php if($res[0]->upcoming_appt_days=='2'){ echo 'selected'; } ?>>2</option>
										<option value="4" <?php if($res[0]->upcoming_appt_days=='4'){ echo 'selected'; } ?>>4</option>
										<option value="6" <?php if($res[0]->upcoming_appt_days=='6'){ echo 'selected'; } ?>>6</option>
										<option value="8" <?php if($res[0]->upcoming_appt_days=='8'){ echo 'selected'; } ?>>8</option>
								</select>
								<div class="switch-field" class="dropdown-menu">
									<input type="radio" id="radio" name="upcoming_appt_on_off" value="on" <?php if($res[0]->upcoming_appt_on_off=='on'){ ?> checked="checked" <?php } ?> />
									<label for="radio">ON</label>
									<input type="radio" id="radio-t" name="upcoming_appt_on_off" value="off" <?php if($res[0]->upcoming_appt_on_off=='off'){ ?> checked="checked" <?php } ?> />
									<label for="radio-t">OFF</label>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<input type="submit" class="button-primary save-button" name="submit" value="Save Changes">
						</td>
						<th></th>
					</tr>
				</table>
			</div>
		</div>

	</form>

		

	
	<style>
		a:link {
			text-decoration: none;
			}
		.container {
			height: 64px;
			display: flex;
			align-items: center;
		}

		div.breadcrumb {
			display: flex;
			align-items: center;
		}

		span {
			font-family: Roboto;
			font-weight: 300;
			font-size: 20px;
		}

		i {
			margin: 0 8px;
		}

		jimbo > i:last-child {
			display: none;
		}
		.info_span{
			font-size: 14px;margin-top: -5px;display: block;
		}
		.dropdown-menu {
 		 min-width: 60px !important;
		  margin-left: 10px;
		}
		.section-block input[type="text"], .section-block textarea, .section-block select {
			min-width: 500px;
			margin: 0 0 10px;
		}
		.section-block textarea {
			min-height: 100px;
		}
		.section-block table {
			border-collapse: collapse;
			width: 100%;
		}
		.section-block tr {
			border-bottom: 0px solid transparent;
		}
		.section-block td, .section-block th {
			padding-right: 30px;
		}
		.section-block td h2 {
			font-weight: 700;
			font-weight: 700;
			font-size: 16px;
			line-height: 20px;
			border-bottom: 1px solid;
			padding-bottom: 5px;
		}
		.section-block th {
			text-align: left;
			/*min-width: 130px;*/
			max-width: 100%;
			vertical-align: top;
			padding-top: 5px;
		}
		.section-block-inner{
			padding: 20px;
		}
		.section-block {
			
			
			width: 95%;
			margin: 30px 0;
		
		}
		.section-block h1 {
			margin: 0 0 0;
			line-height: 50px;
			padding-left: 20px;
			text-transform: uppercase;
			font-size: 20px;
		}
		.section-block h3 {
			margin-top: 0;
		}
		.switch-field {
			display: flex;
			overflow: hidden;
		}

		.switch-field input {
			position: absolute !important;
			clip: rect(0, 0, 0, 0);
			height: 1px;
			width: 1px;
			border: 0;
			overflow: hidden;
		}

		.switch-field label {
			background-color: #e4e4e4;
			color: rgba(0, 0, 0, 0.6);
			font-size: 14px;
			line-height: 1;
			text-align: center;
			padding: 8px 16px;
			margin-right: -1px;
			border: 1px solid rgba(0, 0, 0, 0.2);
			box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
			transition: all 0.1s ease-in-out;
		}

		.switch-field label:hover {
			cursor: pointer;
		}

		.switch-field input:checked + label {
			background-color: #a9a9a9;
			box-shadow: none;
		}

		.switch-field label:first-of-type {
			border-radius: 4px 0 0 4px;
		}

		.switch-field label:last-of-type {
			border-radius: 0 4px 4px 0;
		}

		/* This is just for CodePen. */

		.form {
			max-width: 600px;
			font-family: "Lucida Grande", Tahoma, Verdana, sans-serif;
			font-weight: normal;
			line-height: 1.625;
			margin: 8px auto;
			padding: 16px;
		}

		h2 {
			font-size: 18px;
			margin-bottom: 8px;
		}

		.section-block-inner.bookingstyle table tbody tr th{
			width: 50%;
		}
		.section-block-inner.bookingstyle table tbody tr td .modifystyle{
			display: flex;
    		align-items: center;
			margin-bottom: 25px;
		}
		.section-block-inner.bookingstyle table tbody tr td .modifystyle select{
			margin: 0 15px 0 0;
		}
		.section-block-inner.bookingstyle table tbody tr td .save-button{
			margin-top: 15px;
		}
	</style>	
<?php 
}

// Booking Rules submenu

add_action('admin_menu', 'booking_rules');

function booking_rules()
{
	add_submenu_page( 'edit.php?post_type=cpt_plugin', 'Booking Rules', 'Booking Rules', 'manage_options', 'booking-rules', 'booking_rules_main');
}

add_action('feelya_admin_init', 'register_booking_rules');

function register_booking_rules() {
    register_setting('fasbr_group', 'fasbr_option');
}

function booking_rules_main() { ?>
<div class="container">
	<div class="breadcrumb">
		<a href="<?php echo site_url().'/wp-admin/edit.php?post_type=cpt_plugin'; ?>"><span>Admin Settings</span></a>
		<i class="material-icons">>></i>
	</div>
	<div class="breadcrumb">
		<span>Booking Rules</span>
	</div>
</div>
	<!-- <h1>Booking Rules</h1> -->
	<?php
			settings_fields('fasbr_group');
			$options = get_option('fasbr_option');
			global $wpdb;
			if(isset($_POST['submit']) && !empty($_POST)){
				
					$tableName = $wpdb->prefix . 'feelya_booking_rules';
					$query = $wpdb->update( $tableName, [
						'adv_notice_time' => $_POST['advnotice'],
						'min_appt_time'   => $_POST['min_cancel'],
						'min_time_modify' => $_POST['min_modify'],
						'appt_length'     => $_POST['appointment_length'],
						'cancellation_policy_on_off' => $_POST['cancelpolicy'],
						'modify_time_on_off'  => $_POST['modifytime']
					],  array( 'rule_id' => 1 ), [
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
					] );
					
			}
			$query = "SELECT * FROM wp_feelya_booking_rules";
			$res=$wpdb->get_results($query);
			
		?>
	<form method="post" action="">

		<!-- Contact Form Details BLOCK -->
		<div class="google-captcha section-block">
			<h1>Booking Rules</h1>
			<div class="section-block-inner bookingstyle">
				<table>
					<tr>
						<th>Default advance notice (minimum time required before a client can book)</th>
						<td>
						<div class="modifystyle">
							<select name="advnotice" class="dropdown-menu">
							        <option value="1" <?php if($res[0]->adv_notice_time=='1'){ echo 'selected'; } ?>>1</option>
									<option value="2" <?php if($res[0]->adv_notice_time=='2'){ echo 'selected'; } ?>>2</option>
							</select>
						</div>
						</td>
						
					</tr>
					<tr>
						<th>Default cancellation policy (min time required prior to appointment so client can cancel without penalty) 
							</th>
							<td>
							<div class="modifystyle">
								<select name="min_cancel" class="dropdown-menu">
										<option value="48" <?php if($res[0]->min_appt_time=='48'){ echo 'selected'; } ?>>48</option>
										<option value="72" <?php if($res[0]->min_appt_time=='72'){ echo 'selected'; } ?>>72</option>
										<option value="76" <?php if($res[0]->min_appt_time=='76'){ echo 'selected'; } ?>>76</option>
								</select>
								<div class="switch-field">
									<input type="radio" id="radio-one" name="cancelpolicy" value="on" <?php if($res[0]->cancellation_policy_on_off=='on'){?> checked="checked" <?php } ?>/>
									<label for="radio-one">ON</label>
									<input type="radio" id="radio-two" name="cancelpolicy" value="off" <?php if($res[0]->cancellation_policy_on_off=='off'){ ?> checked="checked" <?php } ?>/>
									<label for="radio-two">OFF</label>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<th>Default minimum time before modifying [hours/days]</th>
							<td>
							<div class="modifystyle">
								<select name="min_modify" class="dropdown-menu">
										<option value="6" <?php if($res[0]->min_time_modify=='6'){ echo 'selected'; } ?>>6</option>
										<option value="12" <?php if($res[0]->min_time_modify=='12'){ echo 'selected'; } ?>>12</option>
										<option value="24" <?php if($res[0]->min_time_modify=='24'){ echo 'selected'; } ?>>24</option>
										<option value="48" <?php if($res[0]->min_time_modify=='48'){ echo 'selected'; } ?>>48</option>
								</select>
								<div class="switch-field">
									<input type="radio" id="radio" name="modifytime" value="on" <?php if($res[0]->modify_time_on_off=='on'){ ?> checked="checked" <?php } ?> />
									<label for="radio">ON</label>
									<input type="radio" id="radio-t" name="modifytime" value="off" <?php if($res[0]->modify_time_on_off=='off'){ ?> checked="checked" <?php } ?> />
									<label for="radio-t">OFF</label>
								</div>

							</div>
						</td>
					</tr>
					<tr>
						<th>Default appointment length</th>
							<td>
							<div class="modifystyle">
								<select name="appointment_length" class="dropdown-menu">
										<option value="50" <?php if($res[0]->appt_length=='50'){ echo 'selected'; } ?>>50</option>
										<option value="55" <?php if($res[0]->appt_length=='55'){ echo 'selected'; } ?>>55</option>
										<option value="60" <?php if($res[0]->appt_length=='60'){ echo 'selected'; } ?>>60</option>
								</select>
							</div>
						</td>
					</tr>

					

					<tr>
						<td>
							<input type="submit" class="button-primary save-button" name="submit" value="Save Changes">
						</td>
						<th></th>
					</tr>
				</table>
			</div>
		</div>

	</form>
	<style>
		a:link {
			text-decoration: none;
			}
		.container {
			height: 64px;
			display: flex;
			align-items: center;
		}

		div.breadcrumb {
			display: flex;
			align-items: center;
		}

		span {
			font-family: Roboto;
			font-weight: 300;
			font-size: 20px;
		}

		i {
			margin: 0 8px;
		}

		jimbo > i:last-child {
			display: none;
		}
		.info_span{
			font-size: 14px;margin-top: -5px;display: block;
		}
		.dropdown-menu {
 		 min-width: 60px !important;
		  margin-left: 10px;
		}
		.section-block input[type="text"], .section-block textarea, .section-block select {
			min-width: 500px;
			margin: 0 0 10px;
		}
		.section-block textarea {
			min-height: 100px;
		}
		.section-block table {
			border-collapse: collapse;
			width: 100%;
		}
		.section-block tr {
			border-bottom: 0px solid transparent;
		}
		.section-block td, .section-block th {
			padding-right: 30px;
		}
		.section-block td h2 {
			font-weight: 700;
			font-weight: 700;
			font-size: 16px;
			line-height: 20px;
			border-bottom: 1px solid;
			padding-bottom: 5px;
		}
		.section-block th {
			text-align: left;
			/*min-width: 130px;*/
			max-width: 100%;
			vertical-align: top;
			padding-top: 5px;
		}
		.section-block-inner{
			padding: 20px;
		}
		.section-block {
			
			
			width: 95%;
			margin: 30px 0;
		
		}
		.section-block h1 {
			margin: 0 0 0;
			line-height: 50px;
			padding-left: 20px;
			text-transform: uppercase;
			font-size: 20px;
		}
		.section-block h3 {
			margin-top: 0;
		}
		.switch-field {
			display: flex;
			overflow: hidden;
		}

		.switch-field input {
			position: absolute !important;
			clip: rect(0, 0, 0, 0);
			height: 1px;
			width: 1px;
			border: 0;
			overflow: hidden;
		}

		.switch-field label {
			background-color: #e4e4e4;
			color: rgba(0, 0, 0, 0.6);
			font-size: 14px;
			line-height: 1;
			text-align: center;
			padding: 8px 16px;
			margin-right: -1px;
			border: 1px solid rgba(0, 0, 0, 0.2);
			box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
			transition: all 0.1s ease-in-out;
		}

		.switch-field label:hover {
			cursor: pointer;
		}

		.switch-field input:checked + label {
			background-color: #a9a9a9;
			box-shadow: none;
		}

		.switch-field label:first-of-type {
			border-radius: 4px 0 0 4px;
		}

		.switch-field label:last-of-type {
			border-radius: 0 4px 4px 0;
		}

		/* This is just for CodePen. */

		.form {
			max-width: 600px;
			font-family: "Lucida Grande", Tahoma, Verdana, sans-serif;
			font-weight: normal;
			line-height: 1.625;
			margin: 8px auto;
			padding: 16px;
		}

		h2 {
			font-size: 18px;
			margin-bottom: 8px;
		}

		.section-block-inner.bookingstyle table tbody tr th{
			width: 50%;
		}
		.section-block-inner.bookingstyle table tbody tr td .modifystyle{
			display: flex;
    		align-items: center;
			margin-bottom: 25px;
		}
		.section-block-inner.bookingstyle table tbody tr td .modifystyle select{
			margin: 0 15px 0 0;
		}
		.section-block-inner.bookingstyle table tbody tr td .save-button{
			margin-top: 15px;
		}

	</style>	
<?php 


}
