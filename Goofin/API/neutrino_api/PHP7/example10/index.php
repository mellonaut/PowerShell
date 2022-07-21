<h2>Scan a list of domains to find out which domain is parked</h2>

<p>Here is a basic example to scan a list of domains from domains-list.txt file.</p>

<p>Make sure the file domains-list.txt contains one domain per line.</p>

<p>It will be created a CSV file data.csv with details.</p>

<hr style="margin: 20px 0;" />

<?php

require_once dirname(__DIR__, 1).'/config.php';
require_once dirname(__DIR__, 1).'/helper.php';
	
if(!$_apivoid['key']) die('<p><font color="red">You need to add your API key on config.php file!</font></p>');

$file = realpath(dirname(__FILE__))."/domains-list.txt";

if(!file_exists($file))
{
	echo '<p><font color="red">Cannot find domains-list.txt file!</font></p>';
}
else
{
	$domains_count = 0;
	
	$time_start = microtime(true);
	
	$save_as = realpath(dirname(__FILE__))."/data.csv";
	
	$handle = fopen($file, "r");
	
	if(!$handle) die('<p><font color="red">Failed to open domains-list.txt file!</font></p>');
	
	if(!file_exists($save_as)) file_put_contents($save_as, "domain,a_records_found,is_parked\n", FILE_APPEND | LOCK_EX);
	
	while(!feof($handle))
	{
		$domain = trim(fgets($handle));
		
		if(stripos($domain, ".") === false) continue;
		
		$domains_count++;
			
		$json = curl_get_json('https://endpoint.apivoid.com/parkeddomain/v1/pay-as-you-go/?key='.$_apivoid['key'].'&host='.$domain);
		
		if(is_array($json) && !isset($json['error']))
		{
			$temp = "";
			
			$temp .= $json['data']['host'].",";

			$temp .= var_export($json['data']['a_records_found'], true).",";

			$temp .= var_export($json['data']['parked_domain'], true);

			file_put_contents($save_as, $temp."\n", FILE_APPEND | LOCK_EX);
		}
	}
	
	fclose($handle);
	
	echo '<p>Scanned '.number_format(intval($domains_count)).' domains in total</p>';
	
	echo '<p>Check the generated file <b>'.htmlspecialchars($save_as).'</b></p>';
	
	$time_end = microtime(true);

	$time_taken = number_format($time_end - $time_start, 2);
	
	echo "<p>Time taken ".htmlspecialchars($time_taken)." sec(s)</p>";
}

?>
