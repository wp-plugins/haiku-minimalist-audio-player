<?php
/*
Plugin Name: Haiku - minimalist audio player
Plugin URI: http://daltonrooney.com/wordpress/haiku
Description: Add a text-link HTML5 audio player using shortcode
Author: Dalton Rooney
Version: 0.2.0
Author URI: http://daltonrooney.com/wordpress
*/ 

$haiku_player_version = "0.2.0";
// add our default options if they're not already there:
if (get_option('haiku_player_version')  != $haiku_player_version) {
    update_option('haiku_player_version', $haiku_player_version);}
add_option("haiku_player_show_support", 'true'); 
add_option("haiku_player_show_graphical", 'false'); 
   
// now let's grab the options table data
$haiku_player_version = get_option('haiku_player_version');
$haiku_player_show_support = get_option('haiku_player_show_support');
$haiku_player_show_graphical = get_option('haiku_player_show_graphical');

add_shortcode('haiku', 'haiku_player_shortcode');
// define the shortcode function

function haiku_player_shortcode($atts) {
	global $haiku_player_show_graphical;
	STATIC $i = 1;
	extract(shortcode_atts(array(
		'url'	=> '',
		'title'	=> '',
		'graphical' => $haiku_player_show_graphical
	), $atts));
	// stuff that loads when the shortcode is called goes here
	
	if ($graphical == "false") {	//decide whether to show the text or graphical player
	$haiku_player_shortcode .= '
	<div id="haiku-text-player'.$i.'" class="haiku-text-player"></div>
		 <div id="text-player-container'.$i.'" class="text-player-container"> 
			<ul id="player-buttons'.$i.'" class="player-buttons"> 
				<li class="play"><a href="'.$url.'">play</a></li> 
				<li class="stop"><a href="javascript: void(0);">stop</a></li>';
				
				if(!empty($title)) { $haiku_player_shortcode .= '<li class="title">'.esc_attr($title).'</li>'; }
				
			$haiku_player_shortcode .= '</ul> 
	</div>';
	} elseif ($graphical == "true") {
	$haiku_player_shortcode .= '
	
	<div id="haiku-player'.$i.'" class="haiku-player"></div>
	
		<div id="player-container'.$i.'" class="player-container"><div id="haiku-button'.$i.'" class="haiku-button"><a href="'.$url.'"><img class="listen" src="';
		
		$haiku_player_shortcode .=  plugins_url( 'resources/play.png', __FILE__ );
		
		$haiku_player_shortcode .= '"  /></a>
		
		<ul id="controls'.$i.'" class="controls"><li class="pause"><a href="javascript: void(0);"></a></li><li class="play"><a href="javascript: void(0);"></a></li><li class="stop"><a href="javascript: void(0);"></a></li><li id="sliderPlayback'.$i.'" class="sliderplayback"></li></ul></div>
	</div><!-- player_container-->
	
';}
		
	$i++; //increment static variable for unique player IDs
	return $haiku_player_shortcode;
} //ends the haiku_player_shortcode function

// scripts to go in the header and/or footer
if( !is_admin()){
   wp_enqueue_script('jquery');
   wp_register_script('jplayer', plugins_url( '/js/jquery.jplayer.min.js', __FILE__ ), false, '1.2', true); 
   wp_enqueue_script('jplayer');
   wp_register_script('haiku-player', plugins_url( '/js/haiku-player.js', __FILE__ ), false, '0.2.0', true); 
   wp_enqueue_script('haiku-player');
 wp_register_script('jquery-ui-custom', plugins_url( '/js/jquery-ui-custom.min.js', __FILE__ ), false, '1.8.7', true); 
   wp_enqueue_script('jquery-ui-custom');
}


function haiku_player_head() {
	echo '
<!-- loaded by Haiku audio player plugin-->
<link rel="stylesheet" type="text/css" href="' .  plugins_url( 'haiku-player.css', __FILE__ ) . '?ver=0.2.0" />
<script type="text/javascript">
var jplayerswf = "'. plugins_url( '/js/', __FILE__ ) . '";
</script>
<!-- end Haiku -->
';
} // ends haiku_player_head function
add_action('wp_head', 'haiku_player_head');

// create the admin menu
// hook in the action for the admin options page
add_action('admin_menu', 'add_haiku_player_option_page');

function add_haiku_player_option_page() {
	// hook in the options page function
	add_options_page('Haiku Player', 'Haiku Player', 6, __FILE__, 'haiku_player_options_page');
}
function haiku_player_options_page() { 	// Output the options page
	global $haiku_player_version, $haiku_player_show_support, $haiku_player_show_graphical; ?>
	<div class="wrap" style="width:500px">
	
			<h2>Support this plugin</h2>

<div<?php if ($haiku_player_show_support=="true"){echo ' style="display:none"';}?>>

<p>Donations for this software are welcome:</p> 

<form action="https://www.paypal.com/cgi-bin/webscr" method="post"> 
<input type="hidden" name="cmd" value="_s-xclick"> 
<input type="hidden" name="hosted_button_id" value="2ANTEK4HG6XCW"> 
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"> 
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"><br /> 
</form> 

<p>One more thing: we love <a href="http://daltn.com/x/a2">A2 Hosting</a>! We've been using them for years, and they provide the best web host service and support in the industry. If you sign up through the link below, we get a referral fee, which helps us maintain this software. Their one-click WordPress install will have you up and running in just a couple of minutes.</p> 
<p><a  href="http://daltn.com/x/a2"><img style="margin:10px 0;" src="http://daltonrooney.com/portfolio/wp-content/uploads/2010/01/green_234x60.jpeg" alt="" title="green_234x60" width="234" height="60" class="alignnone size-full wp-image-148" /></a></p> 
</div>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<input type="checkbox" name="haiku_player_show_support" value="true"<?php if ($haiku_player_show_support=="true"){echo ' checked="checked"';}?>> I have donated to the plugin, don't show ads.<br />
<input type="hidden" name="page_options" value="haiku_player_show_support" />
<input type="hidden" name="action" value="update" />	

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save') ?>" />
</p>

</form>

<form method="post" action="options.php">

<?php wp_nonce_field('update-options'); ?>

<h2>Haiku Player Settings</h2>

<table class="form-table">
<tr valign="top">
<th scope="row">Use graphical player (experimental)</th>
<td><select name="haiku_player_show_graphical" value="<?php echo get_option('haiku_player_show_graphical'); ?>" />
	<option value="true" <?php if($haiku_player_show_graphical == "true") echo " selected='selected'";?>>true</option>
	<option value="false" <?php if($haiku_player_show_graphical == "false") echo " selected='selected'";?>>false</option>
</select>
</td>
</tr>	
</table>

<input type="hidden" name="page_options" value="haiku_player_show_graphical" />
<input type="hidden" name="action" value="update" />	
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>


		<h2>Reference</h2>
		<p>Use the shortcode <code>[haiku url="http://example.com/file.mp3" title="Title of audio file"]</code> to play an audio file. Be sure to use the full URL to the audio file. The title field is optional.</p>
		
		<p><strong>New!</strong> The player now supports a graphical mode, which is turned off by default. Enable graphical mode by changing the default setting in the settings panel or by adding the attribute <code>graphical="true"</code> to your shortcode. Can be overridden on a per-player basis.</p>
		
		<p>The graphical player looks like this:</p>
		<img src ="<?php echo plugins_url( 'resources/player-example.png', __FILE__ )?>" alt="player example" height="50" width="178"/>
	
		<p>Please note that the graphical player is at an early stage of development and should be tested before you deploy it to a large audience. It is likely that the design of the player will change in future versions. It's just HTML & CSS, so feel free to experiment with your own version.</p>

		<p>You're using Haiku Player v. <?php echo $haiku_player_version;?> by <a href="http://madebyraygun.com">Raygun</a>.<p>Based on jPlayer, by <a href="http://www.happyworm.com/jquery/jplayer/">Happyworm</a>.</p>
	</div><!--//wrap div-->
<?php } ?>