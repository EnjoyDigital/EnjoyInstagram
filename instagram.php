<?php
/**
* Plugin Name: EnjoyInstagram
* Description: Pulls in the latest images from specified Instagram account
* Version: 1.0
* Author: Glenn Taylor (Enjoy Digital)
*/

class enjoyInstagram{

	function __construct(){
		add_action('admin_menu', array($this, 'ei_create_menu'));	// Create menu page
		add_shortcode('enjoy_instagram', array($this, 'ei_shortcode'));
	}

	/**
	 * Creates an option under settings for the plugin
	 * @return null
	 */
	function ei_create_menu(){
		add_options_page('Enjoy Instagram', 'Enjoy Instagram', 'manage_options', 'enjoy-instagram-menu', array($this, 'ei_plugin_options'));
	}

	/**
	 * Generate the plugin settings interface and handle options
	 * @return null
	 */
	function ei_plugin_options(){
		if(!empty($_POST)){
			if(get_option('ei_access_token') !== false){
				update_option('ei_access_token', $_POST['access-token']);
			}else{
				add_option('ei_access_token', $_POST['access-token']);
			}

			if(get_option('ei_user_id') !== false){
				update_option('ei_user_id', $_POST['user-id']);
			}else{
				add_option('ei_user_id', $_POST['user-id']);
			}
			?>
				<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Settings saved.</strong></p></div>
			<?php
		}

		$access_token = get_option('ei_access_token');
		$user_id = get_option('ei_user_id');
		?>
			<div class="wrap">
				<h2>Enjoy Instagram</h2>
				<form action="options-general.php?page=enjoy-instagram-menu" method="post">
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row">Access Token</th>
								<td><input type="text" class="regular-text" name="access-token" value="<?=$access_token?>"></td>
							</tr>
							<tr>
								<th scope="row">User ID</th>
								<td><input type="text" class="regular-text" name="user-id" value="<?=$user_id?>"></td>
							</tr>
						</tbody>
					</table>

					<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
				</form>
			</div>
		<?php
	}

	/**
	 * Grabs the items from Instagram and stores them
	 * @return null
	 */
	function ei_update_items(){
		$user_id = get_option('ei_user_id');
		$access_token = get_option('ei_access_token');

		$url = 'https://api.instagram.com/v1/users/' . $user_id . '/media/recent?access_token=' . $access_token;
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$json = json_decode(curl_exec($ch), true);
		curl_close($ch);

		$items = array_slice($json['data'], 0, 12);

		if(get_option('ei_instagram_items') !== false){
			update_option('ei_instagram_items', $items);
		}else{
			add_option('ei_instagram_items', $items);
		}
	}

	/**
	 * Creates the shortcode and returns the actual media items
	 * @return object
	 */
	function ei_shortcode(){

		$last_checked = get_option('ei_last_checked');

		if($last_checked < (time() - (30 * 60)) || $last_checked === false){

			$this->ei_update_items();

			if(get_option('ei_last_checked') !== false){
				update_option('ei_last_checked', time());
			}else{
				add_option('ei_last_checked', time());
			}
		}

		$items = get_option('ei_instagram_items');

		return $this->ei_format_items($items);

	}

	/**
	 * Returns formatted HTML with the images
	 * @param  object $items Array of the items from Instagram
	 * @return string
	 */
	function ei_format_items($items){
		foreach($items as $item){
			if($item['type'] != 'image'){
				continue;
			}

			$return .= '<div class="image"><a href="'.$item['link'].'" target="_blank"><img src="'.$item['images']['thumbnail']['url'].'" width="90" height="90"></a></div>';
		}

		return $return;
	}

}

$instagram = new enjoyInstagram();