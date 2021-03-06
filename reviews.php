<?php

/**

 * @package reviews

 */

/*

Plugin Name: Google Reviews Counter

Plugin URI: http://meganicheuniversity.com/wordpress-plugins

Description: Plugin to count reviews.

Version: 3.0

Author: MegaNiche

Author URI: http://www.meganicheuniversity.com

License: Later

*/

global $jal_db_version;
$jal_db_version = "1.0";
global $payment_domain;



$payment_domain = 'meganicheuniversity.com';

function jal_install() 
{
	global $wpdb;
	global $jal_db_version;
	global $payment_domain;
   	$table_name = $wpdb->prefix . "reviews_counter";
   
   	//if( $wpdb->get_var('SHOW TABLE LIKE '. $table_name) != $table_name)
    {



		$rs = mysql_query("DROP TABLE $table_name ");
		$sql = "CREATE TABLE $table_name (
	  		id mediumint(9) NOT NULL AUTO_INCREMENT,
	  		url_key VARCHAR(100) DEFAULT '' NOT NULL,
	  		url VARCHAR(256) DEFAULT '' NOT NULL,
	  		active tinyint DEFAULT 0 NOT NULL,
	  		PRIMARY KEY (id)
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	 	$rs = mysql_query("TRUNCATE TABLE $table_name ");
		$fields = array(
				'google_maps' => 'Google Maps Link',
				'yelp' => 'Yelp Link',
				'yahoo_local' => 'Yahoo Local Link',
				'yellow_pages' => 'Yellow Pages Link',
				'merchant_circle' => 'Merchant Circle Link',
				'local_com' => 'Local.com Link',
				'kudzu' => 'Kudzu Link',
			);
		foreach($fields as $key =>$txt)
		{
			$rs = mysql_query("SELECT id FROM $table_name WHERE url_key = '$key'");
			if(mysql_num_rows($rs) <=0)
			{
				if($key == 'google_maps' || $key == 'merchant_circle' || $key == 'kudzu' || $key == 'local_com')
				{
					dbDelta("INSERT INTO $table_name (url_key, active) VALUES ('$key', '1')");
				}
				else
				{
					dbDelta("INSERT INTO $table_name (url_key, active) VALUES ('$key', '0')");
				}
			}
		}



		global $payment_domain;



		file_get_contents('http://'.$payment_domain.'/reviews_counter/plugin_install.php?domain='.str_replace('www.', '', $_SERVER['HTTP_HOST']).'&site_url='.get_option('siteurl'));
		add_option("jal_db_version", $jal_db_version);
   }
}

register_activation_hook(__FILE__,'jal_install');


add_action('admin_menu', 'plugin_admin_add_page');

function plugin_admin_add_page() 
{
	add_menu_page('Review counter page', 'Google Reviews Counter Settings', 'manage_options', 'plugin', 'plugin_menu_page');
}


function plugin_menu_page() 
{
	global $payment_domain;
	global $wpdb;
	$id = $ar['id'];
	$table_name = $wpdb->prefix . "reviews_counter";
	?>
	
	<div>
	<h2>Review counter setting page</h2>
	Options relating to the Custom Plugin.<br/>
	Once done updating, Activate plugin in Widget Area.<br/>
	<form action="options.php" method="post" id="service_form">
		<?php settings_fields('plugin_options'); ?>
		<?php do_settings_sections('plugin'); ?>
		<br />
		<?php
		$conf = mysql_query("SELECT * FROM $table_name WHERE url_key<>'google_maps' LIMIT 1");
		$conf = mysql_fetch_object($conf);
		if(!($conf->active == '1'))
	 	{
			echo '<a href="http://'.$payment_domain.'/reviews_counter/pay.php?domain='.str_replace('http://', '', str_replace('www.', '', $_SERVER['HTTP_HOST'])).'&site_url='.str_replace('http://', '', str_replace('www.', '', get_option('siteurl'))).'" target="_blank" style="border: 1px solid rgb(187, 187, 187); padding: 4px; border-radius: 4px 4px 4px 4px; text-decoration: none;">Upgrade</a>&nbsp;&nbsp;Want access to all? Upgrade for $20'; 
		}

		$fields = array(
			'google_maps' => 'Google Maps Link',
			'yelp' => 'Yelp Link',
			'yahoo_local' => 'Yahoo Local Link',
			'yellow_pages' => 'Yellow Pages Link',
			'merchant_circle' => 'Merchant Circle Link',
			'local_com' => 'Local.com Link',
			'kudzu' => 'Kudzu Link',
		);
		$fields_check = array(
			'google_maps' => 'google',
			'yelp' => 'yelp',
			'yahoo_local' => 'yahoo',
			'yellow_pages' => 'yellowpages',
			'merchant_circle' => 'merchantcircle',
			'local_com' => 'local.com',
			'kudzu' => 'kudzu',
		);
		?>
		<br />
	</form>
	<script type="text/javascript">
	function checkLink(id, texttocheck)
	{
		var keys = new Array('<?php echo implode("','", array_keys($fields)) ?>');
		var vals = new Array('<?php echo implode("','", $fields) ?>');
		var check_vals = new Array('<?php echo implode("','", $fields_check) ?>');
		var error = 0;
		for(var i=0; i<keys.length; i++)
		{
			if(document.getElementById(keys[i]) != null)
			{
				var linkVal = document.getElementById(keys[i]).value;
				
				if(linkVal == '')
				{
				}
				else if((((linkVal.indexOf('http://') == '-1') && (linkVal.indexOf('https://') == '-1'))) || linkVal.indexOf(check_vals[i]) == '-1')
				{
					document.getElementById(keys[i]).style.border = '1px solid #F00';
					var error = 1;
				}
			}
		}
		if(error == 1)
		{
			alert('Please enter correct link in highlighted fields');
		}
		else
		{
			document.getElementById('service_form').submit();
		}
	}
	</script>
	</div>
	<?php
	
}

// add the admin settings
add_action('admin_init', 'plugin_admin_init');

function plugin_admin_init()
{
	$fields = array(
			'google_maps' => 'Google Maps Link',
			'yelp' => 'Yelp Link',
			'yahoo_local' => 'Yahoo Local Link',
			'yellow_pages' => 'Yellow Pages Link',
			'merchant_circle' => 'Merchant Circle Link',
			'local_com' => 'Local.com Link',
			'kudzu' => 'Kudzu Link',
		);
	foreach($fields as $key =>$txt)
	{
		register_setting( 'plugin_options', 'plugin_options', 'plugin_options_validate' );
		add_settings_section('plugin_main', 'Main Settings', 'plugin_section_text', 'plugin');
		add_settings_field($key, $txt, 'plugin_setting_string', 'plugin', 'plugin_main', array('id' => $key));
	}
}

function plugin_section_text() 
{
	echo '<p>Main description of this section here.</p>';
	
	if($_GET['settings-updated'] == 'true')
	{
		echo '<p style="color:RED">Settings successfully saved.</p>';
	}
}

function plugin_setting_string($ar) 
{
	global $wpdb;
	$id = $ar['id'];
	$table_name = $wpdb->prefix . "reviews_counter";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$conf = mysql_query("SELECT * FROM $table_name WHERE url_key='".$id."'");
	$conf = mysql_fetch_object($conf);
	$options = get_option('plugin_options');
	if($conf->active == '1')
	{
		echo '<input id="'.$id.'" name="plugin_options['.$id.']" size="40" type="text" value="'.(isset($options[$id])?$options[$id]:'').'" /><input name="Submit" type="button" value="Update" onclick="checkLink(\''.$id.'\', \''.$id.'\')" />';
	}
	else
	{
		echo '<span style="color:#999">Upgrade for this services.</span>';	
	}
}

// validate our options
function plugin_options_validate($input) 
{
	foreach($input as $key => $val)
	{
		$newinput[$key] = trim($input[$key]);
	}
	return $newinput;
}


class GoogleReviewsCounterWidget extends WP_Widget
{
  	function GoogleReviewsCounterWidget()
  	{
    	$widget_ops = array('classname' => 'GoogleReviewsCounterWidget', 'description' => 'Plugin to count reviews' );
    	$this->WP_Widget('GoogleReviewsCounterWidget', 'Google Reviews Counter', $widget_ops);
  	}
 
  	function form($instance)
  	{
    	$instance = wp_parse_args( (array) $instance, array( 'size' => '190' ) );
    	$size = $instance['size'];
		?>
  			<p>
            	<label for="<?php echo $this->get_field_id('size'); ?>">Select widget width: : 
                    <br />
                    <br />
                    <input id="<?php echo $this->get_field_id('size'); ?>_250" name="<?php echo $this->get_field_name('size'); ?>" type="radio" value="250" <?php echo ((attribute_escape($size) == '250')?'checked':''); ?> /> 250px
                    <br />
                    <input id="<?php echo $this->get_field_id('size'); ?>_190" name="<?php echo $this->get_field_name('size'); ?>" type="radio" value="190" <?php echo ((attribute_escape($size) == '190')?'checked':''); ?>  /> 190px
                    <br />
                    <input id="<?php echo $this->get_field_id('size'); ?>_130" name="<?php echo $this->get_field_name('size'); ?>" type="radio" value="130" <?php echo ((attribute_escape($size) == '130')?'checked':''); ?>  /> 130px
				</label>
			</p>
		<?php
  	}
 
  	function update($new_instance, $old_instance)
  	{
    	$instance = $old_instance;
    	$instance['size'] = $new_instance['size'];
    	return $instance;
  	}
 
  	function widget($args, $instance)
  	{
		global $payment_domain;
		global $wpdb;
		$table_name = $wpdb->prefix . "reviews_counter";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
		$widoption = get_option('plugin_options');
		$size = empty($instance['size']) ? ' ' : apply_filters('widget_size', $instance['size']);
		$fields = array(
				'google_maps' => 'Google Maps',
				'yelp' => 'Yelp',
				'yahoo_local' => 'Yahoo Local',
				'yellow_pages' => 'Yellow Pages',
				'merchant_circle' => 'Merchant Circle',
				'local_com' => 'Local.com',
				'kudzu' => 'Kudzu',
			);			
		
		echo '
		<link href="'.plugins_url().'/google-reviews-counter-generator/css/reviews.css" type="text/css" rel="stylesheet" />
		<div class="reviews_box size_'.$size.'">
			<div class="reviews_header">
				<h1>Online Reviews Counter</h1>
			</div>
			<div class="reviews_content">';
			
			if(is_array($widoption))
			foreach( $widoption as $key=>$value) 
			{
				$conf = mysql_query("SELECT * FROM $table_name WHERE url_key='".$key."'");
				$conf = mysql_fetch_object($conf);
				$options = get_option('plugin_options');
				if($conf->active == '0')
				{
					continue;
				}
				if(!isset($widoption[$key]))
				{
					continue;
				}
				elseif($widoption[$key] == '')
				{
					continue;
				}
	
				echo '
				<div class="content_review_row">
					<a href="'.$value.'" target="_blank"><img src="'.plugins_url().'/google-reviews-counter-generator/logos/'.$key.(($size=='130')?'_s':'_m').'.jpg" /></a>
					<p><div class="rev_cnt"><span class="rev_txt"><a  href="'.$value.'" target="_blank" id="reviews_count_'.$key.'">Reviews</a></span><span class="rev_num">';
						echo '<iframe src="http://'.$payment_domain.'/reviews_counter/'.$key.'.php?url='.str_replace('http', '-_-_-', str_replace('#', '---', str_replace('?', '___', str_replace('&', '__', $value)))).'&domain='.str_replace('www.', '', $_SERVER['HTTP_HOST']).'" height="17px" width="'.(($size=='250')?'37':'23').'px" scrolling="no" style="border:0px;"></iframe>';
					echo '</span></div></p>
				</div>';
			}
			echo '
<div style="float: left;padding: 5px;width: 100%;"><a target="_blank" style="font-size: 12px;margin-right: 15px;float: right;text-decoration:none;" href="http://meganicheuniversity.com/wordpress-plugins">Google Reviews</a></div>
			</div>
		</div>';
  	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("GoogleReviewsCounterWidget");') );?>
