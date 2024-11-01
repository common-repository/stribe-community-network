<?php
/*
Plugin Name: Stribe Community Network
Plugin URI: http://www.stribe.com
Description: Plug a Stribe.com community in your Wordpress blog in 30 seconds.  
Version: 0.3
Author: Stribe team
Author URI: http://www.stribe.com
License: MIT (Expat) 
*/

//error_reporting(E_ALL);

/*
  Taken from and for compatibility with Wordpress Mobile Edition 
*/
function stribe_is_mobile()
{
	$cfmobi_mobile_browsers = array(
                '2.0 MMP',
                '240x320',
                '400X240',
                'AvantGo',
                'BlackBerry',
                'Blazer',
                'Cellphone',
                'Danger',
                'DoCoMo',
                'Elaine/3.0',
                'EudoraWeb',
                'Googlebot-Mobile',
                'hiptop',
                'IEMobile',
                'KYOCERA/WX310K',
                'LG/U990',
                'MIDP-2.',
                'MMEF20',
                'MOT-V',
                'NetFront',
                'Newt',
                'Nintendo Wii',
                'Nitro', // Nintendo DS
                'Nokia',
                'Opera Mini',
                'Palm',
                'PlayStation Portable',
                'portalmmm',
                'Proxinet',
                'ProxiNet',
                'SHARP-TQ-GX10',
                'SHG-i900',
                'Small',
                'SonyEricsson',
                'Symbian OS',
                'SymbianOS',
                'TS21i-10',
                'UP.Browser',
                'UP.Link',
                'webOS', // Palm Pre, etc.
                'Windows CE',
                'WinWAP',
                'YahooSeeker/M1A1-R2D2',
        );
    
        $cfmobi_touch_browsers = array(
                'iPhone',
                'iPod',
                'Android',
                'BlackBerry9530',
                'LG-TU915 Obigo', // LG touch browser
                'LGE VX',
                'webOS', // Palm Pre, etc.
                'Nokia5800',
        );
	
        $mobile = null;
        if (!isset($_SERVER["HTTP_USER_AGENT"]) || (isset($_COOKIE['cf_mobile']) && $_COOKIE['cf_mobile'] == 'false')) {
                $mobile = false;
        }
        else if (isset($_COOKIE['cf_mobile']) && $_COOKIE['cf_mobile'] == 'true') {
                $mobile = true;
        }
        $browsers = array_merge($cfmobi_mobile_browsers, $cfmobi_touch_browsers);
        if (is_null($mobile) && count($browsers)) {
                foreach ($browsers as $browser) {
                        if (!empty($browser) && strpos($_SERVER["HTTP_USER_AGENT"], trim($browser)) !== false) {
                                $mobile = true;
                        }
                }
        }
        if (is_null($mobile)) {
                $mobile = false;
        }
	return $mobile;
}


function stribeDIV($height)
{
  echo "<div id=\"stribe_container\" style=\"height:".$height."px; width:100%; min-width:250px; max-width:500px\"></div>";
}

function stribe_get_options()
{
  $options = get_option("widget_stribe");
  if (!is_array( $options ))
  {
    $options = array(
      'title' => 'Community',
      'height'=> 600,
      'ckey'  => 'no key!'
      );
  }
  return $options;
}

function include_stribe_js($ckey)
{
  $stribe_js = "http://static.stribe.com/s.js?ckey=".$ckey;

  //wp_enqueue_script('stribeJS', $stribe_js);  // unfortunately it adds arguments to the ckey...
  echo "\n<script type=\"text/javascript\" language=\"javascript\" src=\"".$stribe_js."\"></script>\n";
} 

function stribe_head()
{
  if (!is_admin() && !stribe_is_mobile())
  {
    $options = stribe_get_options();
    $ckey = $options['ckey'];
    include_stribe_js($ckey);
  }
}

function widget_stribe($args) {
  extract($args);

  $options = stribe_get_options();
  $height = $options['height'];
  $ckey = $options['ckey'];
  $title = $options['title'];

  echo $before_widget;
  echo $before_title;
  echo $title;
  echo $after_title;
  stribeDIV($height);
  if (stribe_is_mobile())  // if we are on mobile, but there is a sidebar location where we can try to appear, let's go!
  {
    include_stribe_js($ckey);
  }
  echo $after_widget;
}
 
function stribe_init()
{
  register_sidebar_widget('Stribe Community Network', 'widget_stribe');
  register_widget_control('Stribe Community Network', 'stribe_control' );
}

function stribe_control()
{
  stribe_options("control");
}

function stribe_options($type)
{
  $options = stribe_get_options();

  if ($_POST['stribe-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['stribe-WidgetTitle']);
    $options['ckey'] = htmlspecialchars($_POST['stribe-ckey']);
    $options['height'] = htmlspecialchars($_POST['stribe-height']);
    update_option("widget_stribe", $options);
  }

  if ($type == "settings")
  {
    echo "<p></p>";
    echo "<p>Fill your Stribe API key below (aka \"ckey\" parameter). If you don't have one yet, you can create an account for free on <a href=\"http://www.stribe.com\" target=\"_blank\">http://www.stribe.com</a>.</p>";   
     echo "<form id=\"stribe_settings_form\" name=\"stribe_settings_form\" method=\"post\">";
  }
?>
  <p>
    <label for="stribe-ckey">Stribe API key for this blog:</label>
    <input type="text" id="stribe-ckey" name="stribe-ckey" value="<?php echo $options['ckey'];?>" size="7" />
  </p>

<?php
  if ($type == "settings")
  {
     echo "<hr>";
     echo "<p>The following options are used only if you drag &amp; drop the Stribe widget in your sidebar from the <em>Appearance -&gt; Widgets</em> menu.<br />";
     echo "Note that your sidebar should allow for a minimal width of 250 pixels. We recommend a 300 pixels sidebar for optimal integration.</p>";
  }
?>
  <p>
    <label for="stribe-WidgetTitle">Stribe widget title: </label>
    <input type="text" <?php if ($type=="control") { ?>class="widefat"<?php } ?> id="stribe-WidgetTitle" name="stribe-WidgetTitle" value="<?php echo $options['title'];?>" />
  </p>

  <p>
    <label for="stribe-height">Height of the Stribe widget: </label>
    <input type="text" id="stribe-height" name="stribe-height" value="<?php echo $options['height'];?>" size="4"/>
  </p>

    <input type="hidden" id="stribe-Submit" name="stribe-Submit" value="1" />
<?php
  if ($type == "settings")
  {
    echo "<p class=\"submit\"><input type=\"submit\" name=\"submit\" class=\"button-primary\" value=\"Save Settings\" /></p>";
    echo "</form>";
  }
}

function stribe_settings_add_page() {
	$mypage = add_options_page('Stribe Community Network', 'Stribe Community Network', 8, 'stribe', 'stribe_settings_page');
}

function stribe_settings_page() {
	echo "<div class='wrap'>
	<h2>Stribe Community Network settings</h2>";
	stribe_options("settings");
	echo "</div>";
}

function stribe_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=stribe">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'stribe_settings_link');

add_action("widgets_init", 'stribe_init');

add_action("wp_print_scripts", 'stribe_head');
add_action('admin_menu', 'stribe_settings_add_page');
?>
