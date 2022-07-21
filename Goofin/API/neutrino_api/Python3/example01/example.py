#!/usr/bin/python3

import requests
import json

def apivoid_query(endpoint, arguments):
   try:
      r = requests.get(url='https://endpoint.apivoid.com/'+endpoint+'/v1/pay-as-you-go/', params=arguments)
      return json.loads(r.content.decode())
   except:
      return ""
	  
data = apivoid_query("iprep", {"key":"YOUR_APIVOID_KEY_HERE","ip":"45.4.3.33"})

print(data)