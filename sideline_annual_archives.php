<?php
/*
Plugin Name: Sideline Annual Archives Widget
Plugin URI: http://www.sideline.ca/
Description: Adds a widget that contains all of the settings of the default Archives widget but also allows an option to only show the year.  This can be handy if the normal Archives widget is too long for the page.
Author: Michael McClenaghan
Version: 1.0
Author URI: http://www.sideline.ca/
*/

function widget_sideline_annual_archives($args, $number = 1) {
  extract($args);
	$options = get_option("widget_sideline_annual_archives");
  $showyear = $options[$number]['OnlyShowYear'] ? '1' : '0';
	$c = $options[$number]['count'] ? '1' : '0';
	$title = empty($options[$number]['title']) ? __('Archives') : $options[$number]['title'];
?>
	<?php echo $before_widget; ?>
		<?php echo $before_title . $title . $after_title; ?>
		<ul>
		<?php 
		if ($showyear) 
			wp_get_archives("type=yearly&show_post_count=$c");	
		else
			wp_get_archives("type=monthly&show_post_count=$c");
		?>
		</ul>
	<?php echo $after_widget; ?>
<?php
}

function sideline_annual_archives_control($number) {
  $options = $newoptions = get_option("widget_sideline_annual_archives");
  if ($_POST["sideline_annual_archives-Submit-$number"]) {
		$newoptions[$number]['OnlyShowYear'] = isset($_POST["sideline_annual_archives-OnlyShowYear-$number"]);
		$newoptions[$number]['Count'] = isset($_POST["sideline_annual_archives-Count-$number"]);
		$newoptions[$number]['Title'] = strip_tags(stripslashes($_POST["sideline_annual_archives-Title-$number"]));
	}
	if ($options != $newoptions) {
		$options = $newoptions;
    update_option("widget_sideline_annual_archives", $options);
  }
  $showyear = $options[$number]['OnlyShowYear'] ? 'checked="checked"' : '';
	$title = htmlspecialchars($options[$number]['Title'], ENT_QUOTES);
	$count = $options[$number]['count'] ? 'checked="checked"' : '';
?>
	<p>
		<label for="sideline_annual_archives-Title-<?php echo "$number"; ?>"><?php _e('Title:'); ?></label>
		<input type="text" id="sideline_annual_archives-Title-<?php echo "$number"; ?>" name="sideline_annual_archives-Title-<?php echo "$number"; ?>" value="<?php echo $title; ?>" />
	</p>
	<p>
		<label for="sideline_annual_archives-Count-<?php echo "$number"; ?>">Show post counts</label>
		<input class="checkbox" type="checkbox" <?php echo $count; ?> id="sideline_annual_archives-Count-<?php echo "$number"; ?>" name="sideline_annual_archives-Count-<?php echo "$number"; ?>" />
	</p>
	<p>	
		<label for="sideline_annual_archives-OnlyShowYear-<?php echo "$number"; ?>">Only show year </label>
		<input class="checkbox" type="checkbox" <?php echo $showyear; ?> id="sideline_annual_archives-OnlyShowYear-<?php echo "$number"; ?>" name="sideline_annual_archives-OnlyShowYear-<?php echo "$number"; ?>" />
	</p>	
  <input type="hidden" id="sideline_annual_archives-Submit-<?php echo "$number"; ?>" name="sideline_annual_archives-Submit-<?php echo "$number"; ?>" value="1" />
<?php	
}

function sideline_annual_archives_register() {
	$options = get_option('widget_sideline_annual_archives');
	$number = $options['number'];
	if ( $number < 1 ) $number = 1;
	if ( $number > 9 ) $number = 9;
	for ($i = 1; $i <= 9; $i++) {
		$name = array('Annual Archives %s', 'widgets', $i);
		register_sidebar_widget($name, $i <= $number ? 'widget_sideline_annual_archives' : /* unregister */ '', $i);
		register_widget_control($name, $i <= $number ? 'sideline_annual_archives_control' : /* unregister */ '', 410, 200, $i);
	}
}

add_action("plugins_loaded", "sideline_annual_archives_register");

?>
