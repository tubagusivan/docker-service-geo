define({ "api": [  {    "type": "get",    "url": "/adm_0/center",    "title": "Center Latitude and Longitude",    "name": "GetCountryCenterLatLng",    "group": "Country",    "parameter": {      "fields": {        "Query string": [          {            "group": "Query string",            "type": "String",            "optional": false,            "field": "name",            "description": "<p>Name of the country</p>"          }        ]      }    },    "version": "0.0.0",    "filename": "./index.php",    "groupTitle": "Country",    "sampleRequest": [      {        "url": "https://geo-dot-qlue-go-engine.appspot.com/adm_0/center"      }    ]  },  {    "type": "get",    "url": "/adm_0/list",    "title": "Request List",    "name": "GetCountryList",    "group": "Country",    "version": "0.0.0",    "filename": "./index.php",    "groupTitle": "Country",    "sampleRequest": [      {        "url": "https://geo-dot-qlue-go-engine.appspot.com/adm_0/list"      }    ]  },  {    "success": {      "fields": {        "Success 200": [          {            "group": "Success 200",            "optional": false,            "field": "varname1",            "description": "<p>No type.</p>"          },          {            "group": "Success 200",            "type": "String",            "optional": false,            "field": "varname2",            "description": "<p>With type.</p>"          }        ]      }    },    "type": "",    "url": "",    "version": "0.0.0",    "filename": "./doc/main.js",    "group": "_Users_andre_Documents_GIT_service_geo_web_doc_main_js",    "groupTitle": "_Users_andre_Documents_GIT_service_geo_web_doc_main_js",    "name": ""  }] });
