<?php

global $wpdb;

global $jal_db_version;



include '../../../wp-config.php';

$service = $_GET['service'];



$table_name = $wpdb->prefix . "reviews_counter";



if(isset($_GET['act']))

{

	if($_GET['act'] == '0')

	{

		mysql_query("UPDATE $table_name SET active='0' WHERE url_key!='google_maps");

	}

	else

	{

		mysql_query("UPDATE $table_name SET active='1' ");

	}

}

else

{

	mysql_query("UPDATE $table_name SET active='1'");

}