#!/usr/bin/python3

# To run the script just use this:
# python3 /path/to/script.py instagram.com

import requests
import json
import sys
import os

apivoid_key = "YOUR_APIVOID_KEY_HERE";

try:
   host = sys.argv[1];
except:
   print("Usage: " + os.path.basename(__file__) + " <domain>")
   sys.exit(1)

def apivoid_domainrep(key, host):
   try:
      r = requests.get(url='https://endpoint.apivoid.com/domainbl/v1/pay-as-you-go/?key='+key+'&host='+host)
      return json.loads(r.content.decode())
   except:
      return ""

data = apivoid_domainrep(apivoid_key, host)

def get_detection_engines(engines):
   list = "";
   for key, value in engines.items():
      if(bool(value['detected']) == 1):
         list+=str(value['engine'])+", "
   return list.rstrip(", ")

if(data):
   if(data.get('error')):
      print("Error: "+data['error'])
   else:
      print("Host: "+str(data['data']['report']['host']))
      print("IP Address: "+str(data['data']['report']['server']['ip']))
      print("Reverse DNS: "+str(data['data']['report']['server']['reverse_dns']))
      print("---")
      print("Detections Count: "+str(data['data']['report']['blacklists']['detections']))
      print("Detected By: "+get_detection_engines(data['data']['report']['blacklists']['engines']))
      print("---")
      print("Country: "+str(data['data']['report']['server']['country_code'])+" ("+str(data['data']['report']['server']['country_name'])+")")
      print("Continent: "+str(data['data']['report']['server']['continent_code'])+" ("+str(data['data']['report']['server']['continent_name'])+")")
      print("Region: "+str(data['data']['report']['server']['region_name']))
      print("City: "+str(data['data']['report']['server']['city_name']))
      print("Latitude: "+str(data['data']['report']['server']['latitude']))
      print("Longitude: "+str(data['data']['report']['server']['longitude']))
      print("ISP: "+str(data['data']['report']['server']['isp']))
      print("---")
      print("Is Free Hosting: "+str(data['data']['report']['category']['is_free_hosting']))
      print("Is URL Shortener: "+str(data['data']['report']['category']['is_url_shortener']))
      print("Is Free Dynamic DNS: "+str(data['data']['report']['category']['is_free_dynamic_dns']))
else:
   print("Error: Request failed")
   
   
