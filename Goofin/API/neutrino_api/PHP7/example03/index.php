<h2>Block Bad User Registrations</h2>

<p>Check email and user IP address with APIVoid before creating an user account.</p>

<p>Useful to block users that use disposable emails, bad email domains, blacklisted IPs, proxy IPs, etc.</p>

<p>This example uses <strong>apivoid_check_user_email/ip</strong> functions from helper.php</p>

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
	
	// Check email with APIVoid Email Verify API
	$emailcheck = apivoid_check_user_email($email);
	
	// Check if email should be allowed
	if(!$emailcheck['allow']) die('<p><font color="red">'.$emailcheck['message'].'</font></p>');
	
	// Check user IP with APIVoid IP Reputation API
	$ipcheck = apivoid_check_user_ip($ip);
	
	// Check if user IP address should be allowed
	if(!$ipcheck['allow']) die('<p><font color="red">'.$ipcheck['message'].'</font></p>');
	
	// All fine, save user to database
	echo '<p><font color="green">Your account has been created!</font></p>';
}

?>
