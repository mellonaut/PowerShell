<?php

/* 
 * Function that queries IP Reputation API to check if an IP address should be allowed or if 
 * it should be blocked, because it is detected by 2 or more blacklists,
 * or because it is detected as a public proxy, etc.
 *
*/

function apivoid_check_user_ip($ip)
{
    global $_apivoid;
	
	// Check if IP address is valid
    if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) return array("allow" => false, "message" => "IPv4 address is not valid!");
	
	// Check IP address with APIVoid IP Reputation API
    $json = curl_get_json('https://endpoint.apivoid.com/iprep/v1/pay-as-you-go/?key='.$_apivoid['key'].'&ip='.$ip);
	
	// Return allow => true if no output
	if(!is_array($json)) return array("allow" => true);
	
	// Return allow => true in case of an error
	if(isset($json['error'])) return array("allow" => true);
	
	// Check if IP address is detected by 2 or more blacklists 
	if($json['data']['report']['blacklists']['detections'] >= 2) return array("allow" => false, "message" => "Your IP address is detected by ".intval($json['data']['report']['blacklists']['detections'])." blacklists!");
	
	// Check if IP address is detected as a public proxy
    if($json['data']['report']['anonymity']['is_proxy']) return array("allow" => false, "message" => "Your IP address is detected as a proxy!");
	
	// Check if IP address is detected as a web proxy
    if($json['data']['report']['anonymity']['is_webproxy']) return array("allow" => false, "message" => "Your IP address is detected as a web proxy!");
	
	// Check if IP address is detected as a Tor node
    if($json['data']['report']['anonymity']['is_tor']) return array("allow" => false, "message" => "Your IP address is detected as a Tor node!");
	
	// Check if IP address is detected as a VPN
    if($json['data']['report']['anonymity']['is_vpn']) return array("allow" => false, "message" => "Your IP address is detected as a VPN!");
	
	// All fine, return allow => true
    return array("allow" => true);
}

/* 
 * Function that queries Email Verify API to check if an email address should be allowed or if 
 * it should be blocked, because it is detected as a disposable/temporary email,
 * or because it has an invalid or risky TLD, etc.
 *
*/

function apivoid_check_user_email($email)
{
    global $_apivoid;
	
	// Check if email address is valid
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return array("allow" => false, "message" => "Email address is not valid!");
	
	// Check email domain with APIVoid Email Verify API
    $json = curl_get_json('https://endpoint.apivoid.com/emailverify/v1/pay-as-you-go/?key='.$_apivoid['key'].'&email='.$email);
	
	// Return allow => true if no output
	if(!is_array($json)) return array("allow" => true);
	
	// Return allow => true in case of an error
	if(isset($json['error'])) return array("allow" => true);
	
	// Check if TLD of email domain is valid
	if(!$json['data']['valid_tld']) return array("allow" => false, "message" => "Email domain has an invalid TLD!");
	
	// Check if email domain has MX records configured
	if(!$json['data']['has_mx_records']) return array("allow" => false, "message" => "Email domain has no MX records!");
	
	// Check if email is disposable/temporary
    if($json['data']['disposable']) return array("allow" => false, "message" => "Disposable email is not allowed!");
	
	// Check if email username is suspicious
	if($json['data']['suspicious_username']) return array("allow" => false, "message" => "Email username is suspicious!");
	
	// Check if email domain is suspicious
	if($json['data']['suspicious_domain']) return array("allow" => false, "message" => "Email domain is suspicious!");
	
	// Check if TLD of email domain is risky
	if($json['data']['risky_tld']) return array("allow" => false, "message" => "Email domain has a risky TLD!");
	
	// All fine, return allow => true
    return array("allow" => true);
}

/* 
 * Function used to list only engines that detected an IP or a domain.
 * Used within IP Reputation API or Domain Reputation API.
 * You can select a custom separator and if engines should be clickable
 * that redirects users to their respective reference link.
 *
*/

function get_engines_list_detected_by($engines, $separator = ", ", $include_links = false)
{
	$result = "";

	// Get the list of engines
	foreach($engines as $engine)
	{
		// Check if the engine detected the scanned object
		if($engine['detected'])
		{
			// Should we output engines list with link to reference?
		    if($include_links)
			{
				// Engine name is clickable and points to its reference link
			    $result .= '<a href="'.$engine['reference'].'" target="_blank">'.$engine['engine'].'</a>'.$separator;
			}
			else
			{
				// Engine name is not clickable
			    $result .= $engine['engine'].$separator;
			}
		}
	}
	
	// Return the result, trimming the $separator on the right
	return rtrim($result, $separator);
}

/* 
 * Function that uses cURL to download an URL 
 * and returns JSON data decoded as array. If there is an error
 * it returns an empty string ""
 *
*/

function curl_get_json($url)
{
	// Check if URL is valid
	if(!filter_var($url, FILTER_VALIDATE_URL)) return "";
	
	// Use cURL to query APIVoid
	$curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_TIMEOUT, 60);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0.1");
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$output = curl_exec($curl);
	$info = curl_getinfo($curl);
	curl_close($curl);
	
	// Check HTTP code
	if($info['http_code'] != 200) return "";
 
    // Check if the downloaded data is empty
	if(!$output) return "";
	
	// Decode JSON data to array
    $json = json_decode($output, true);
	
	// Check if decoded JSON data is valid
	if(!is_array($json) || json_last_error() !== 0) return "";
	
	// Return JSON data
	return $json;
}

?>
