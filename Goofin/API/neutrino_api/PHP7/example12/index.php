<h2>Scan a list of URLs to get URL status information</h2>

<p>Here is a basic example to scan a list of URLs from urls-list.txt file.</p>

<p>Make sure the file urls-list.txt contains one URL per line.</p>

<p>It will be created a CSV file data.csv with details.</p>

<style>table{margin:35px 0;font-family:"Trebuchet MS",Arial,Helvetica,sans-serif;border-collapse:collapse;width:100%}table td,table th{border:1px solid #ddd;padding:8px}table tr:nth-child(even){background-color:#f2f2f2}table th{text-align:left}</style>

<?php

require_once dirname(__DIR__, 1).'/config.php';
require_once dirname(__DIR__, 1).'/helper.php';
	
if(!$_apivoid['key']) die('<p><font color="red">You need to add your API key on config.php file!</font></p>');

$file = realpath(dirname(__FILE__))."/urls-list.txt";

if(!file_exists($file))
{
	echo '<p><font color="red">Cannot find urls-list.txt file!</font></p>';
}
else
{
	$urls_count = 0;
	
	$time_start = microtime(true);
	
	$save_as = realpath(dirname(__FILE__))."/data.csv";
	
	$handle = fopen($file, "r");
	
	if(!$handle) die('<p><font color="red">Failed to open urls-list.txt file!</font></p>');
	
	if(!file_exists($save_as)) file_put_contents($save_as, "host,url_encoded,server_ip,http_status_code,suspended_page,sinkholed_domain,url_taken_down,url_status\n", FILE_APPEND | LOCK_EX);
	
	while(!feof($handle))
	{
		$url = trim(fgets($handle));
		
		if(!filter_var($url, FILTER_VALIDATE_URL)) continue;
		
		$urls_count++;
			
		$json = curl_get_json('https://endpoint.apivoid.com/urlstatus/v1/pay-as-you-go/?key='.$_apivoid['key'].'&url='.urlencode($url));
		
		if(is_array($json) && !isset($json['error']))
		{
			$temp = "";
			
			$temp .= preg_replace("/^www\./", "", parse_url($url, PHP_URL_HOST)).",";
			
			$temp .= urlencode($url).",";

			$temp .= $json['data']['report']['server_details']['ip'].",";
			
			$temp .= intval($json['data']['report']['analysis']['http_status_code']).",";

			$temp .= var_export($json['data']['report']['analysis']['suspended_page'], true).",";
			
			$temp .= var_export($json['data']['report']['analysis']['sinkholed_domain'], true).",";
			
			$temp .= var_export($json['data']['report']['analysis']['url_taken_down'], true).",";
			
			$temp .= $json['data']['report']['analysis']['url_status'];
			
			file_put_contents($save_as, $temp."\n", FILE_APPEND | LOCK_EX);
			
		}
	}
	
	fclose($handle);
	
	echo '<p>Scanned '.number_format(intval($urls_count)).' URLs in total</p>';
	
	echo '<p>Check the generated file <b>'.htmlspecialchars($save_as).'</b></p>';
	
	$time_end = microtime(true);

	$time_taken = number_format($time_end - $time_start, 2);
	
	echo "<p>Time taken ".htmlspecialchars($time_taken)." sec(s)</p>";
}

?>
