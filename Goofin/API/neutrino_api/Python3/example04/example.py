#!/usr/bin/python3

import apivoid

data = apivoid.make_query("iprep", {"key":"YOUR_APIVOID_KEY_HERE","ip":"45.4.3.33"})

if(data):
   if(data.get('error')):
      print("Error: "+data['error'])
   else:
      print("IP: "+data['data']['report']['ip'])
      print("Hostname: "+data['data']['report']['information']['reverse_dns'])
      print("---")
      print("Detections Count: "+str(data['data']['report']['blacklists']['detections']))
      print("Detected By: "+apivoid.get_detection_engines(data['data']['report']['blacklists']['engines']))
      print("---")
      print("Country: "+data['data']['report']['information']['country_code']+" ("+data['data']['report']['information']['country_name']+")")
      print("Continent: "+data['data']['report']['information']['continent_code']+" ("+data['data']['report']['information']['continent_name']+")")
      print("Region: "+data['data']['report']['information']['region_name'])
      print("City: "+data['data']['report']['information']['city_name'])
      print("Latitude: "+str(data['data']['report']['information']['latitude']))
      print("Longitude: "+str(data['data']['report']['information']['longitude']))
      print("ISP: "+data['data']['report']['information']['isp'])
      print("---")
      print("Is Proxy: "+str(data['data']['report']['anonymity']['is_proxy']))
      print("Is Web Proxy: "+str(data['data']['report']['anonymity']['is_webproxy']))
      print("Is VPN: "+str(data['data']['report']['anonymity']['is_vpn']))
      print("Is Hosting: "+str(data['data']['report']['anonymity']['is_hosting']))
      print("Is Tor: "+str(data['data']['report']['anonymity']['is_tor']))
else:
   print("Error: Request failed")
   

