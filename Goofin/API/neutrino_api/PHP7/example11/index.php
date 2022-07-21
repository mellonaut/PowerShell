<h2>Scan a list of URLs to spot potentially risky URLs</h2>

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
	
	if(!file_exists($save_as)) file_put_contents($save_as, "host,url_encoded,status_code,risk_score\n", FILE_APPEND | LOCK_EX);
	
	echo '<table>';
	echo '<thead><tr><th width="100">Risk Score</th><th width="250">Host</th><th width="160">IP Address</th><th width="230">ISP</th><th>URL</th></tr></thead>';
	echo '<tbody>';
	
	while(!feof($handle))
	{
		$url = trim(fgets($handle));
		
		if(!filter_var($url, FILTER_VALIDATE_URL)) continue;
		
		$urls_count++;
			
		$json = curl_get_json('https://endpoint.apivoid.com/urlrep/v1/pay-as-you-go/?key='.$_apivoid['key'].'&url='.urlencode($url));
		
		if(is_array($json) && !isset($json['error']))
		{
			$temp = "";
			
			$temp .= preg_replace("/^www\./", "", parse_url($url, PHP_URL_HOST)).",";
			
			$temp .= urlencode($url).",";

			$temp .= intval($json['data']['report']['response_headers']['code']).",";
			
			$temp .= intval($json['data']['report']['risk_score']['result']);

			file_put_contents($save_as, $temp."\n", FILE_APPEND | LOCK_EX);
			
			echo '<tr><td>'.( intval($json['data']['report']['risk_score']['result']) >= 70 ? '<font color="red">'.intval($json['data']['report']['risk_score']['result']).'</font>' : intval($json['data']['report']['risk_score']['result']) ).'</td><td>'.htmlspecialchars(preg_replace("/^www\./", "", parse_url($url, PHP_URL_HOST))).'</td><td>'.htmlspecialchars($json['data']['report']['server_details']['ip']).'</td><td>'.htmlspecialchars($json['data']['report']['server_details']['isp']).'</td><td>'.htmlspecialchars(mb_strimwidth($url, 0, 65, "...")).'</td></tr>';
		}
	}
	
	echo '</tbody>';
	echo '</table>';
	
	fclose($handle);
	
	echo '<p>Scanned '.number_format(intval($urls_count)).' URLs in total</p>';
	
	echo '<p>Check the generated file <b>'.htmlspecialchars($save_as).'</b></p>';
	
	$time_end = microtime(true);

	$time_taken = number_format($time_end - $time_start, 2);
	
	echo "<p>Time taken ".htmlspecialchars($time_taken)." sec(s)</p>";
}

?>
