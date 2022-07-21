#!/usr/bin/python3

# To run the script just use this:
# python3 /path/to/script.py "https://www.twitter.com/"

import requests
import urllib.parse
import json
import sys
import os

apivoid_key = "YOUR_APIVOID_KEY_HERE";

try:
   url = sys.argv[1];
except:
   print("Usage: " + os.path.basename(__file__) + " <url>")
   sys.exit(1)

def apivoid_urlrep(key, url):
   try:
      r = requests.get(url='https://endpoint.apivoid.com/urlrep/v1/pay-as-you-go/?key='+key+'&url='+urllib.parse.quote(url))
      return json.loads(r.content.decode())
   except:
      return ""

data = apivoid_urlrep(apivoid_key, url)

if(data):
   if(data.get('error')):
      print("Error: "+data['error'])
   else:
      print("URL: "+str(url))
      print("Risk Score: "+str(data['data']['report']['risk_score']['result']))
      print("---")
      print("Is Suspended Page: "+str(data['data']['report']['security_checks']['is_suspended_page']))
      print("Is Suspicious URL Pattern: "+str(data['data']['report']['security_checks']['is_suspicious_url_pattern']))
      print("Is Most Abused TLD: "+str(data['data']['report']['security_checks']['is_most_abused_tld']))
      print("Is Phishing Heuristic: "+str(data['data']['report']['security_checks']['is_phishing_heuristic']))
      print("Is Suspicious Content: "+str(data['data']['report']['security_checks']['is_suspicious_content']))
      print("Is Domain Blacklisted: "+str(data['data']['report']['security_checks']['is_domain_blacklisted']))
      print("Is Suspicious Domain: "+str(data['data']['report']['security_checks']['is_suspicious_domain']))
      print("Is Sinkholed Domain: "+str(data['data']['report']['security_checks']['is_sinkholed_domain']))
      print("Is Defaced Heursitic: "+str(data['data']['report']['security_checks']['is_defaced_heuristic']))
      print("Is External Redirect: "+str(data['data']['report']['security_checks']['is_external_redirect']))
      print("Is China Country: "+str(data['data']['report']['security_checks']['is_china_country']))
      print("Is Robots Noindex: "+str(data['data']['report']['security_checks']['is_robots_noindex']))
      print("Is Masked File: "+str(data['data']['report']['security_checks']['is_masked_file']))
      print("Is Masked EXE File: "+str(data['data']['report']['security_checks']['is_masked_windows_exe_file']))
      print("Is Windows EXE File: "+str(data['data']['report']['security_checks']['is_windows_exe_file']))
      print("Is Credit Card Field: "+str(data['data']['report']['security_checks']['is_credit_card_field']))
      print("Is Password Field: "+str(data['data']['report']['security_checks']['is_password_field']))
      print("---")
      print("Server IP: "+str(data['data']['report']['server_details']['ip']))
      print("Server Hostname: "+str(data['data']['report']['server_details']['hostname']))
      print("Country: "+str(data['data']['report']['server_details']['country_code'])+" ("+str(data['data']['report']['server_details']['country_name'])+")")
      print("ISP: "+str(data['data']['report']['server_details']['isp']))
      print("---")
      print("Is Free Hosting: "+str(data['data']['report']['site_category']['is_free_hosting']))
      print("Is URL Shortener: "+str(data['data']['report']['site_category']['is_url_shortener']))
      print("Is Free Dynamic DNS: "+str(data['data']['report']['site_category']['is_free_dynamic_dns']))
else:
   print("Error: Request failed")
   
   
