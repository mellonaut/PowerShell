<?php

require_once dirname(__DIR__, 1).'/config.php';
require_once dirname(__DIR__, 1).'/helper.php';
	
if(!$_apivoid['key']) die("You need to add your API key on config.php file!\n");

$file = realpath(dirname(__FILE__))."/websites-list.txt";

if(!file_exists($file))
{
	echo "Cannot find websites-list.txt file!\n";
}
else
{
	$domains_count = 0;
	
	$time_start = microtime(true);
	
	$save_as = realpath(dirname(__FILE__))."/data.csv";
	
	$handle = fopen($file, "r");
	
	if(!$handle) die("Failed to open websites-list.txt file!\n");
	
	if(!file_exists($save_as)) file_put_contents($save_as, "domain,risk_score,detections,detected_by,ip,hostname,country_code,isp,scan_date\n", FILE_APPEND | LOCK_EX);
	
	while(!feof($handle))
	{
		$domain = trim(fgets($handle));
		
		if(!$domain || stripos($domain, ".") === false) continue;
		
		if(preg_match('/^(http:|https:)/is', $domain)) $domain = parse_url($domain, PHP_URL_HOST);
		
		$domain = preg_replace('/^www\./', '', $domain);
			
		$domains_count++;
			
		$domain_reputation = curl_get_json('https://endpoint.apivoid.com/domainbl/v1/pay-as-you-go/?key='.$_apivoid['key'].'&host='.$domain);

		echo "Scanning ".htmlspecialchars($domain)."\n";

		if(is_array($domain_reputation) && !isset($domain_reputation['error']))
		{
			$temp = "";
			
			$temp .= $domain_reputation['data']['report']['host'].",";

			$temp .= $domain_reputation['data']['report']['risk_score']['result'].",";

			$temp .= $domain_reputation['data']['report']['blacklists']['detections'].",";
			
			$temp .= ($domain_reputation['data']['report']['blacklists']['detections']) ? str_replace(", ", "|", get_engines_list_detected_by($domain_reputation['data']['report']['blacklists']['engines']))."," : "-,";
			
			$temp .= $domain_reputation['data']['report']['server']['ip'].",";
			
			$temp .= $domain_reputation['data']['report']['server']['reverse_dns'].",";

			$temp .= $domain_reputation['data']['report']['server']['country_code'].",";
			
			$temp .= str_replace(array(";",","), "", $domain_reputation['data']['report']['server']['isp']).",";

            $temp .= date("Y-m-d");
			
			file_put_contents($save_as, $temp."\n", FILE_APPEND | LOCK_EX);
		}
	}
	
	fclose($handle);

	echo "Scanned ".number_format(intval($domains_count))." websites in total\n";
	
	echo "Check the generated file ".htmlspecialchars(basename($save_as))."\n";
	
	$time_end = microtime(true);

	$time_taken = number_format($time_end - $time_start, 2);
	
	echo "Time taken ".htmlspecialchars($time_taken)." sec(s)\n";
}

?>
