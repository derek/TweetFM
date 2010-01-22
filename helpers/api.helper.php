<?

	class API
	{
		static public function post($class, $method, $params)
		{
			$url = API_URL . "/". $class . "/". $method;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url );       
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_POST, 1 );
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
			
			if (!empty($_SESSION['user_id']))	
				curl_setopt($ch, CURLOPT_USERPWD, $_SESSION['user_id']."_".$_SESSION['username'].":" . MD5($_SESSION['user_id']."_".$_SESSION['username'] . "secret"));
				
			$response = curl_exec( $ch );
			//echo curl_error($ch);
			curl_close($ch);
			
			$r = json_decode($response, true);

			if (!is_array($r))
			{	
				return "API response parsing error: params = " . print_r($params, true) . "\n\n" . $response;
			}
			else
			{
				return $r;
				
			}
				
				
		}
		
		static public function get($class, $method, $params = array())
		{
			$url = API_URL . "/". $class . "/" . $method . "/?" . http_build_query($params);

			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, $url );       
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			
			if (!empty($_SESSION['user_id']))	
				curl_setopt($ch, CURLOPT_USERPWD, $_SESSION['user_id']."_".$_SESSION['username'].":" . MD5($_SESSION['user_id']."_".$_SESSION['username'] . "secret"));
				
			$response = curl_exec( $ch );
			//echo curl_error($ch);
			curl_close($ch);
			$arr = json_decode($response, true);
			
			if (is_array($arr))
				return $arr;
			else
				return $response;
		}
	}
	
	
?>