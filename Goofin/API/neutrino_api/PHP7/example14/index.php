<?php

<h2>Get CNAME and CNAME IP Address from a List of Domains</h2>

<p>Here is a basic example to scan a list of domains from domains-list.txt file via PHP7-CLI.</p>

<p>The script then gets the CNAME record and calculates the CNAME IP address.</p>

<p>Make sure the file domains-list.txt contains one domain per line.</p>

<p>It will be created a CSV file data.csv with following details:</p>

<p>domain,cname,cname_ip,date</p>

<p>You can run the script like this:</p>

<p>php -f domain-cname-ip.php</p>

?>