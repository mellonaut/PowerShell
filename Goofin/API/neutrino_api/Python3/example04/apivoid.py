#!/usr/bin/python3

# Simple APIVoid Python3 Module

# Useful to quickly make an APIVoid API query, i.e:
# make_query("iprep", {"key":"YOUR_APIVOID_KEY_HERE","ip":"45.4.3.33"})

import requests
import json

def make_query(endpoint, arguments):
   try:
      r = requests.get(url='https://endpoint.apivoid.com/'+endpoint+'/v1/pay-as-you-go/', params=arguments)
      return json.loads(r.content.decode())
   except:
      return ""

def get_detection_engines(engines):
   list = "";
   for key, value in engines.items():
      if(bool(value['detected']) == 1):
         list+=str(value['engine'])+", "
   return list.rstrip(", ")
   
   
   