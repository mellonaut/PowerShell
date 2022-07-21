<h2>Block Bad User Registrations</h2>

<p>Check email and user IP address with APIVoid before creating an user account.</p>

<p>Useful to block users that use disposable emails, bad email domains, blacklisted IPs, proxy IPs, etc.</p>

<p>This example uses customized code to block specific behaviors.</p>

<hr style="margin: 20px 0;" />

<form action="" method="post">
  Email Address:
  <br />
  <input type="text" name="email" placeholder="test@email.com" required>
  <br />
  <br />
  Password:
  <br />
  <input type="password" name="password" required>
  <br />
  <br />
  <input type="submit" name="submit" value="Register">
</form> 

<?php

if(isset($_POST['submit']))
{
	require_once dirname(__DIR__, 1).'/config.php';
    require_once dirname(__DIR__, 1).'/helper.php';
	
	if(!$_apivoid['key']) die('<p><font color="red">You need to add your API key on config.php file!</font></p>');
	
	$email = trim($_POST['email']);
	
	$password = trim($_POST['password']);
	
	// Get user IP address
	$ip = trim($_SERVER['REMOTE_ADDR']);
	
	// Make sure email is valid
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) die('<p><font color="red">Your email address is not valid!</font></p>');
	
	// Check email domain with APIVoid Email Verify API
	$json = curl_get_json('https://endpoint.apivoid.com/emailverify/v1/pay-as-you-go/?key='.$_apivoid['key'].'&email='.$email);
	
	// Make sure the API output is valid
	if(is_array($json))
	{
		// Make sure the API didn't return an error
		if(!isset($json['error']))
		{
			// Check if email is disposable/temporary
			if($json['data']['disposable']) die('<p><font color="red">Disposable email is not allowed!</font></p>');
	
			// Check if email username is suspicious
			if($json['data']['suspicious_username']) die('<p><font color="red">Email username is suspicious!</font></p>');
	
			// Check if TLD of email domain is valid
			if(!$json['data']['valid_tld']) die('<p><font color="red">Email domain has an invalid TLD!</font></p>');
	
			// Check if TLD of email domain is risky
			if($json['data']['risky_tld']) die('<p><font color="red">Email domain has a risky TLD!</font></p>');
		
			// Check if email domain is suspicious
			if($json['data']['suspicious_domain']) die('<p><font color="red">Email domain is suspicious!</font></p>');
		}
	}
	
	// Check user IP address with APIVoid IP Reputation API
	$json = curl_get_json('https://endpoint.apivoid.com/iprep/v1/pay-as-you-go/?key='.$_apivoid['key'].'&ip='.$ip);
	
	// Make sure the API output is valid
	if(is_array($json))
	{
		// Make sure the API didn't return an error
		if(!isset($json['error']))
		{
			// Check if user IP address is detected by 2 or more blacklists 
			if(intval($json['data']['report']['blacklists']['detections']) >= 2) die('<p><font color="red">Your IP is blacklisted by '.intval($json['data']['report']['blacklists']['detections']).' blacklists!</font></p>');
	
			// Check if user IP address is detected as a public proxy
			if($json['data']['report']['anonymity']['is_proxy']) die('<p><font color="red">Your IP address is detected as a proxy!</font></p>');
			
			// Check if user IP address is detected as Tor node
			if($json['data']['report']['anonymity']['is_tor']) die('<p><font color="red">Your IP address is detected as Tor node!</font></p>');
		}
	}
	
	// All fine, save user to database
	echo '<p><font color="green">Your account has been created!</font></p>';
}

?>
