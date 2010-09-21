<?php
/*
Plugin Name: Haiku - minimalist audio player
Plugin URI: http://daltonrooney.com/wordpress/haiku
Description: Add a text-link HTML5 audio player using shortcode
Author: Dalton Rooney
Version: 0.1.3
Author URI: http://daltonrooney.com/wordpress
*/ 

$haiku_player_version = "0.1.3";
// add our default options if they're not already there:
if (get_option('haiku_player_version')  != $haiku_player_version) {
    update_option('haiku_player_version', $haiku_player_version);}
add_option("haiku_player_show_support", 'false'); 
   
// now let's grab the options table data
$haiku_player_version = get_option('haiku_player_version');
$haiku_player_show_support = get_option('haiku_player_show_support');

add_shortcode('haiku', 'haiku_player_shortcode');
// define the shortcode function
function haiku_player_shortcode($atts) {
	STATIC $i = 1;
	extract(shortcode_atts(array(
		'url'	=> '',
		'title'	=> ''
	), $atts));
	// stuff that loads when the shortcode is called goes here
	$haiku_player_shortcode .= '
	<div id="haiku-player'.$i.'" class="haiku-player"></div>
		 <div id="player-container'.$i.'" class="player-container"> 
			<ul id="player-buttons'.$i.'" class="player-buttons"> 
				<li class="play"><a href="'.$url.'">play</a></li> 
				<li class="pause"><a href="javascript: void(0);">pause</a></li> 
				<li class="stop"><a href="javascript: void(0);">stop</a></li>
				<li class="title">'.esc_attr($title).'</li>
			</ul> 
	</div>';
	$i++; //increment static variable for unique player IDs
	return $haiku_player_shortcode;
} //ends the haiku_player_shortcode function

// scripts to go in the header and/or footer
if( !is_admin()){
   wp_deregister_script('jquery'); 
   wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"), false, '1.4.2', false); 
   wp_enqueue_script('jquery');
}  // load jQuery.
   wp_register_script('jplayer', ("/wp-content/plugins/haiku-minimalist-audio-player/js/jquery.jplayer.min.js"), false, '1.2', true); 
   wp_enqueue_script('jplayer');
   wp_register_script('haiku-player', ("/wp-content/plugins/haiku-minimalist-audio-player/js/haiku-player.js"), false, '0.1.1', true); 
   wp_enqueue_script('haiku-player');

function haiku_player_head() {
	echo '
<!-- loaded by Haiku audio player plugin-->
<link rel="stylesheet" type="text/css" href="' .  get_bloginfo('wpurl') . '/wp-content/plugins/haiku-minimalist-audio-player/haiku-player.css" />
<script type="text/javascript">
var jplayerswf = "'. get_bloginfo('wpurl') . '/wp-content/plugins/haiku-minimalist-audio-player/js/";
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
	global $haiku_player_version, $haiku_player_show_support; ?>
	<div class="wrap" style="width:500px">
		<h2>Haiku Player</h2>
		<p>Use the shortcode <code>[haiku url="http://example.com/file.mp3" title="Title of audio file"]</code> to play an audio file. Be sure to use the full URL to the audio file. Title is optional.</p>
		
		<h2>Support this plugin</h2>
		
		<div<?php if ($haiku_player_show_support=="true"){echo ' style="display:none"';}?>>
			<p>Donations for this software are welcome:</p> 
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post"> 
			<input type="hidden" name="cmd" value="_s-xclick"> 
			<input type="hidden" name="hosted_button_id" value="2ANTEK4HG6XCW"> 
			<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"> 
			<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"><br /> 
			</form> 	
			<p>Additionally, I have a recommendation for a web host if you&#8217;re interested. I&#8217;ve been using <a href="http://daltn.com/x/a2">A2 Hosting</a> for years, and they provide fantastic service and support. If you sign up through the link below, I get a referral fee, which helps me maintain this software. Their one-click WordPress install will have you up and running in just a couple of minutes.</p> 
			<p><a  href="http://daltn.com/x/a2"><img style="margin:10px 0;" src="http://daltonrooney.com/portfolio/wp-content/uploads/2010/01/green_234x60.jpeg" alt="" title="green_234x60" width="234" height="60" class="alignnone size-full wp-image-148" /></a></p> 
		</div><!--//support div-->
		<form method="post" action="options.php">
			<?php wp_nonce_field('update-options'); ?>
			<input type="checkbox" name="haiku_player_show_support" value="true"<?php if ($haiku_player_show_support=="true"){echo ' checked="checked"';}?>> I have donated to the plugin, don't show this ad.<br />
			<input type="hidden" name="page_options" value="haiku_player_show_support" />
			<input type="hidden" name="action" value="update" />	
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save') ?>" />
			</p>
		</form>
		<p>You're using Haiku Player v. <?php echo $haiku_player_version;?> by <a href="http://daltonrooney.com/wordpress">Dalton Rooney</a>.<p>Based on jPlayer, by <a href="http://www.happyworm.com/jquery/jplayer/">Happyworm</a>.</p>
	</div><!--//wrap div-->
<?php } ?>