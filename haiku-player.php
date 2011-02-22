<?php
/*
Plugin Name: Haiku - minimalist audio player
Plugin URI: http://madebyraygun.com/lab/haiku
Description: A simple HTML5-based audio player that inserts a text link or graphical player for audio playback.
Author: Dalton Rooney
Version: 0.4.0
Author URI: http://madebyraygun.com
*/ 

$haiku_player_version = "0.4.0";

// add our default options if they're not already there:
if (get_option('haiku_player_version')  != $haiku_player_version) {
    update_option('haiku_player_version', $haiku_player_version);}
add_option("haiku_player_show_support", 'true'); 
add_option("haiku_player_show_graphical", 'false'); 
add_option("haiku_player_analytics", 'false'); 
add_option("haiku_player_default_location", ''); 
add_option("haiku_player_replace_audio_player", ''); 
add_option("haiku_player_replace_mp3_links", ''); 
   
// now let's grab the options table data
$haiku_player_version = get_option('haiku_player_version');
$haiku_player_show_support = get_option('haiku_player_show_support');
$haiku_player_show_graphical = get_option('haiku_player_show_graphical');
$haiku_player_analytics = get_option('haiku_player_analytics');
$haiku_player_default_location = get_option('haiku_player_default_location');
$haiku_player_replace_audio_player = get_option('haiku_player_replace_audio_player');
$haiku_player_replace_mp3_links = get_option('haiku_player_replace_mp3_links');

//set up defaults if these fields are empty
if (empty($haiku_player_show_graphical)) {$haiku_player_show_graphical = "false";}
if (empty($haiku_player_analytics)) {$haiku_player_analytics = "false";}
if (empty($haiku_player_replace_audio_player)) {$haiku_player_replace_audio_player = "false";}

function replace_audio($content) { //finds the old audio player shortcode and rewrites it
  $content = preg_replace('/\[audio:/','[haiku url=',$content,1);
  return $content;
}

if (!empty($haiku_player_replace_audio_player)) { //only run the audio tag replacement filter if the user selected it
	add_filter('the_content', replace_audio);
}

function replace_mp3_links($content) {
  $pattern = "/<a ([^=]+=['\"][^\"']+['\"] )*href=['\"](([^\"']+\.mp3))['\"]( [^=]+=['\"][^\"']+['\"])*>([^<]+)<\/a>/i"; //props to WordPress Audio Player for the regex
  $replacement = '[haiku url=$2 defaultpath=disabled]';
  $content = preg_replace($pattern, $replacement, $content,1);
  return $content;
}

if (!empty($haiku_player_replace_mp3_links)) { //only run the MP3 link replacement filter if the user selected it
	add_filter('the_content', replace_mp3_links);
}

add_shortcode('haiku', 'haiku_player_shortcode');
// define the shortcode function

function haiku_player_shortcode($atts) {
	global $haiku_player_show_graphical, $haiku_player_default_location, $haiku_player_analytics;
	STATIC $i = 1;
	extract(shortcode_atts(array(
		'url'	=> '',
		'title'	=> '',
		'defaultpath' => '',
		'graphical' => $haiku_player_show_graphical
	), $atts));
	// stuff that loads when the shortcode is called goes here
	
	if ($graphical == "false") {	//decide whether to show the text or graphical player
	$haiku_player_shortcode .= '
	<div id="haiku-text-player'.$i.'" class="haiku-text-player"></div>
		 <div id="text-player-container'.$i.'" class="text-player-container"> 
			<ul id="player-buttons'.$i.'" class="player-buttons"> 
				<li class="play"';
				if ($haiku_player_analytics == "true") { $haiku_player_shortcode .=  ' onClick="_gaq.push([\'_trackEvent\', \'Audio\', \'Play\', \''.$title.'\']);"';}
				$haiku_player_shortcode .= '><a title="Listen to '.$title.'" class="play" href="';
				
				if (!empty($haiku_player_default_location) && $defaultpath !="disabled") {
					$haiku_player_shortcode .= site_url() . $haiku_player_default_location . "/";
				}
				
				$haiku_player_shortcode .= $url;
				
				$haiku_player_shortcode .= '">play</a></li> 
				<li class="stop"><a href="javascript: void(0);">stop</a></li>';
				
				if(!empty($title)) { $haiku_player_shortcode .= '<li class="title">'.esc_attr($title).'</li>'; }
				
			$haiku_player_shortcode .= '</ul> 
	</div>';
	} elseif ($graphical == "true") {
	$haiku_player_shortcode .= '
	
	<div id="haiku-player'.$i.'" class="haiku-player"></div>
	
		<div id="player-container'.$i.'" class="player-container"><div id="haiku-button'.$i.'" class="haiku-button"><a title="Listen to '.$title.'" class="play" href="';
				
				if (!empty($haiku_player_default_location) && $defaultpath !="disabled") {
					$haiku_player_shortcode .= site_url() . $haiku_player_default_location . "/";
				}
				
				$haiku_player_shortcode .= $url;
				
				$haiku_player_shortcode .= '"';
		if ($haiku_player_analytics == "true") 
			{$haiku_player_shortcode .=  ' onClick="_gaq.push([\'_trackEvent\', \'Audio\', \'Play\', \''.$title.'\']);"';}
		$haiku_player_shortcode .= '><img alt="Listen to '.$title.'" class="listen" src="';
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
   wp_register_script('haiku-player', plugins_url( '/js/haiku-player.js', __FILE__ ), false, $haiku_player_version, true); 
   wp_enqueue_script('haiku-player');
 wp_register_script('jquery-ui-custom', plugins_url( '/js/jquery-ui-custom.min.js', __FILE__ ), false, '1.8.7', true); 
   wp_enqueue_script('jquery-ui-custom');
}


function haiku_player_head() {
global $haiku_player_version;
	echo '
<!-- loaded by Haiku audio player plugin-->
<link rel="stylesheet" type="text/css" href="' .  plugins_url( 'haiku-player.css', __FILE__ ) . '?ver='.$haiku_player_version.'" />
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
	global $haiku_player_version, $haiku_player_show_support, $haiku_player_default_location,  $haiku_player_show_graphical, $haiku_player_replace_audio_player, $haiku_player_replace_mp3_links; ?>
	<div class="wrap" style="width:800px">
	

<form method="post" action="options.php">

<?php wp_nonce_field('update-options'); ?>

	<div class="updated fade"><p style="line-height: 1.4em;">Thanks for downloading Haiku! If you like it, please be sure to give us a positive rating in the <a href="http://wordpress.org/extend/plugins/haiku-minimalist-audio-player/">WordPress repository</a>, it will help other people find the plugin and means a lot to us.<br /></div>
	
<h2>Haiku Player Settings</h2>

<table class="form-table">

<tr valign="top">
	<th scope="row">Default file location (optional)</th><br />
	<td><?php echo site_url();?><input type="text" name="haiku_player_default_location" value="<?php if (!empty($haiku_player_default_location)) {echo $haiku_player_default_location; }?>"/>
	</td>
</tr>

<tr valign="top">
	<th scope="row">Enable Google Analytics</th>
	<td><input type="checkbox" name="haiku_player_analytics" value="true" <?php if ($haiku_player_analytics=="true") {echo' checked="checked"'; }?>/>
	</td>
</tr>

<tr valign="top">
	<th scope="row">Use graphical player</th>
	<td><input type="checkbox" name="haiku_player_show_graphical" value="true" <?php if ($haiku_player_show_graphical=="true") {echo' checked="checked"'; }?>/>
	</td>
</tr>

<tr valign="top">
	<th scope="row">Replace all mp3 links</th>
	<td><input type="checkbox" name="haiku_player_replace_mp3_links" value="true" <?php if ($haiku_player_replace_mp3_links=="true") {echo' checked="checked"'; }?>/>
	</td>
</tr>

<tr valign="top">
	<th scope="row">Replace WP Audio Player [audio: file.mp3] syntax</th>
	<td><input type="checkbox" name="haiku_player_replace_audio_player" value="true" <?php if ($haiku_player_replace_audio_player=="true") {echo' checked="checked"'; }?>/>
	</td>
</tr>
</table>

<input type="hidden" name="page_options" value="haiku_player_show_graphical, haiku_player_analytics, haiku_player_default_location,haiku_player_replace_audio_player, haiku_player_replace_mp3_links" />
<input type="hidden" name="action" value="update" />	
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>

			<h2>Support this plugin</h2>

<div<?php if ($haiku_player_show_support=="true"){echo ' style="display:none"';}?>>

<p>Donations for this software are welcome:</p> 

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="UKN9872VTPJPW">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<br /></form> 

<p>One more thing: we love <a href="http://rygn.us/gugqDg">A2 Hosting</a>! We've been using them for years, and they provide the best web host service and support in the industry. If you sign up through the link below, we get a referral fee, which helps us maintain this software. Their one-click WordPress install will have you up and running in just a couple of minutes.</p> 
<p><a  href="http://rygn.us/gugqDg"><img style="margin:10px 0;" src="http://daltonrooney.com/portfolio/wp-content/uploads/2010/01/green_234x60.jpeg" alt="" title="green_234x60" width="234" height="60" class="alignnone size-full wp-image-148" /></a></p> 
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



		<h2>Reference</h2>
		<p>Use the shortcode <code>[haiku url="http://example.com/file.mp3" title="Title of audio file"]</code> to play an audio file. Use the full URL of the audio file unless you've set a default file location. The title field is recommended for search engine and accessibility purposes and required if you are using Google Analytics.</p>
		
		<p>The default file location field allows you to specify a folder in your site for your MP3 files. If, for example, all of your audio files are in http://yoursite.com/audio, set the default folder to /audio, and use the shortcode like this: [haiku url="file.mp3"]. This is also helpful if you're replacing an existing WordPress Audio Player installation. You can overwrite this setting on a per-player basis with the attribute defaultpath=disabled. For example, if you wanted to link to a file in a different folder you would use the shortcode [haiku url="http://yoursite/audiofolder2/file.mp3" defaultpath=disabled].</p>
		
		<p>The Google Analytics setting enables a script which tracks play events in your Google Analytics account using the title field. You must already have Google Analytics tracking installed on your site, using the asynchronous version of the script in the head of your HTML document.</p>
		
		<p>The player includes the ability to automatically turn all MP3 links into an audio player instance. Simply check the "Replace all mp3 links" box. You may experience problems if you have other plugins that also override MP3 links, like Shadowbox.</p>
		
		<p>The player is now drop-in compatible with WordPress Audio Player. If you're replacing a WordPress Audio Player install, check the "replace WP Audio Player" box to automatically replace all instances of the WP Audio Player shortcode. (WP Audio Player must be disabled or removed for this to work). The Haiku shortcode is still the recommended format as it allows the "Title" field for Google Analytics support.</p>
				
		<p>The player includes a graphical mode, which is turned off by default. Enable graphical mode by changing the default setting in the settings panel or by adding the attribute <code>graphical="true"</code> to your shortcode. Can be overridden on a per-player basis.</p>
		
		<p>The graphical player looks like this:</p>
		<img src ="<?php echo plugins_url( 'resources/player-example.png', __FILE__ )?>" alt="player example" height="50" width="178"/>
	
		<p>Please note that the graphical player is at an early stage of development and should be tested before you deploy it to a large audience. It is likely that the design of the player will change in future versions. It's just HTML & CSS, so feel free to experiment with your own version.</p>

	<a href="http://madebyraygun.com"><img style="margin-top:30px;" src="<?php echo plugins_url( 'resources/logo.png', __FILE__ );?>" width="225" height="70" alt="Made by Raygun" /></a>
	<p>You're using Haiku Player v. <?php echo $haiku_player_version;?> by <a href="http://madebyraygun.com">Raygun</a>. Check out our <a href="http://madebyraygun.com/lab/">other plugins</a>, and if you have any problems, stop by our <a href="http://madebyraygun.com/support/forum/">support forum</a>!</p>
	
	<p>Based on jPlayer, by <a href="http://www.happyworm.com/jquery/jplayer/">Happyworm</a>.</p>
	</div><!--//wrap div-->
<?php } ?>