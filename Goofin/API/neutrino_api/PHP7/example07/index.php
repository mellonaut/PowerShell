<h2>Create CSV file with IP Reputation Data</h2>

<p>Here is a basic example to scan a list of IP addresses from ips-list.txt file.</p>

<p>It will be created a CSV file data.csv with IP reputation data.</p>

<p>The file ips-list.txt must contain one IP address per line.</p>

<hr style="margin: 20px 0;" />

<?php

require_once dirname(__DIR__, 1).'/config.php';
require_once dirname(__DIR__, 1).'/helper.php';
	
if(!$_apivoid['key']) die('<p><font color="red">You need to add your API key on config.php file!</font></p>');

$file = realpath(dirname(__FILE__))."/ips-list.txt";

if(!file_exists($file))
{
	echo '<p><font color="red">Cannot find ips-list.txt file!</font></p>';
}
else
{
	$ips_count = 0;
	
	$time_start = microtime(true);
	
	$save_as = realpath(dirname(__FILE__))."/data.csv";
	
	$handle = fopen($file, "r");
	
	if(!$handle) die('<p><font color="red">Failed to open ips-list.txt file!</font></p>');
	
	if(!file_exists($save_as)) file_put_contents($save_as, "ip,hostname,isp,detections,detectedby,proxy,tor\n", FILE_APPEND | LOCK_EX);
	
	while(!feof($handle))
	{
		$ip = trim(fgets($handle));
		
		if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) continue;
		
		$ips_count++;
			
		$ip_reputation = curl_get_json('https://endpoint.apivoid.com/iprep/v1/pay-as-you-go/?key='.$_apivoid['key'].'&ip='.$ip);
		
		if(is_array($ip_reputation) && !isset($ip_reputation['error']))
		{
			$temp = "";
			
			$temp .= $ip_reputation['data']['report']['ip'].",";
			
			$temp .= $ip_reputation['data']['report']['information']['reverse_dns'].",";
			
			$temp .= str_replace(array(";",","), "", $ip_reputation['data']['report']['information']['isp']).",";
			
			$temp .= $ip_reputation['data']['report']['blacklists']['detections'].",";
			
			$temp .= ($ip_reputation['data']['report']['blacklists']['detections']) ? str_replace(", ", "|", get_engines_list_detected_by($ip_reputation['data']['report']['blacklists']['engines']))."," : "-,";
			
			$temp .= var_export($ip_reputation['data']['report']['anonymity']['is_proxy'], true).",";
			
			$temp .= var_export($ip_reputation['data']['report']['anonymity']['is_tor'], true);
			
			file_put_contents($save_as, $temp."\n", FILE_APPEND | LOCK_EX);
		}
	}
	
	fclose($handle);
	
	echo '<p>Scanned '.number_format(intval($ips_count)).' IP addresses in total</p>';
	
	echo '<p>Check the generated file <b>'.htmlspecialchars($save_as).'</b></p>';
	
	$time_end = microtime(true);

	$time_taken = number_format($time_end - $time_start, 2);
	
	echo "<p>Time taken ".htmlspecialchars($time_taken)." sec(s)</p>";
}

?>
