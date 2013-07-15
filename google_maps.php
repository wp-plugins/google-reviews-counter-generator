<?php 
set_time_limit (500);
global $wpdb;
global $jal_db_version;
include '../../../wp-config.php';
$options = get_option('plugin_options');
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body style="padding: 0px; margin: 0px; text-align: center; color: rgb(0, 153, 0); font-size: 15px; font-family: arial; font-weight:bold;">
<?php 
$_GET['url'] = $options['google_maps'];
$allowed = 1;
if($allowed == 1)
{
	$url = $_GET['url'];
	$str=file_get_contents( $_GET['url']);
	$reviews_count = 0;
	if(strstr($_GET['url'], 'maps.'))
	{
		if(($str1=strpos($str,'class="qja'))>0)
		{			
			$str2=substr($str, $str1, strlen($str)-$str1);
			$str3=strpos($str2,'>');
			$str4=substr($str2, $str3+1, strlen($str2)-($str3+1));
			$str5=strpos($str4,'</div>');
			$str6=substr($str4,0,$str5);
			$str10 = strip_tags($str6);
			$reviewtext=explode(' ', trim(trim($str10, '\n')));
			$reviews_count = $reviewtext[0];
		}
		else
		{
			$reviews_count = 0;
		}
		if(is_numeric(trim($reviews_count)))
		{
			echo $reviews_count;
		}
		else
		{
			echo '0';
		}
	}
	elseif(strstr($_GET['url'], 'plus.'))
	{
		if(($str1=strpos($str,'class="qja'))>0)
		{
			$str2=substr($str, $str1, strlen($str)-$str1);
			$str3=strpos($str2,'>');
			$str4=substr($str2, $str3+1, strlen($str2)-($str3+1));
			$str5=strpos($str4,'</span>');
			$str6=substr($str4,0,$str5);
			$str10 = strip_tags($str6);
			$reviewtext=explode(' ', trim(trim($str10, '\n')));
			$reviews_count = $reviewtext[0];
		}
		else if(($str1=strpos($str,'class="DLhpie"'))>0){
			$reviews_count = (substr_count($str,'class="DLhpie"'));
		}
		else
		{
			$result1 = (substr_count($str,"/reviews"));
			$word1 = '"More reviews (';
			$word2 = ')"';
			preg_match('/'.preg_quote($word1).'(.*?)'.preg_quote(trim($word2)).'/is', $str, $match);
			$result2 = $match[1];
			$reviews_count = (int)$result1 + (int)$result2 ;
		}		
		if(is_numeric(trim($reviews_count)))
		{
			echo $reviews_count;
		}
		else
		{
			echo '0';
		}
	}
}
else{ }
?>
</body>
</html>
