// Define console.log() to nothing so it doesn't throw errors in non-Firebug browsers
if (typeof console == "undefined" || typeof console.log == "undefined") var console = { log: function() {} }; 

var API = {

	post : function(api_class, api_method, params, callback){
		
		params["class"] = api_class;
		params["method"] = api_method;
		params["format"] = "json";
		
        var url = BASE_URL + "/action/proxy?url="+ API_URL;

		$.ajax({
			"type"		: "POST",
			"url"		: url	,
			"data"		: params,
			"cache"		: false,
			"dataType"	: "json",
			"success"	: 	function(response)
			{
				if(callback)
					callback(response.data)
			},
			"error"	: 	function(xhr){
				var response = $.evalJSON(xhr.responseText);
				var error_msg = "(" + xhr.status + ") Error " + response.error_code + " : " + response.message
				if(callback)
					callback(response, error_msg)
			}
		});               
	},

	get : function(){
		alert('a');
	}

}