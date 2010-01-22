<?

	class twitter
	{
		static public function call($method, $params = array())
		{
			$access_token = $_SESSION['access_token'];
			
			/* Create a TwitterOauth object with consumer/user tokens. */
			$connection = new TwitterOAuth(TWITTER_OAUTH_CONSUMER_KEY, TWITTER_OAUTH_CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

			/* If method is set change API call made. Test is called by default. */
			$content = $connection->get($method, $params);
			
			return $content;
			/* Some example calls */
			//$connection->get('users/show', array('screen_name' => 'abraham')));
			//$connection->post('statuses/update', array('status' => date(DATE_RFC822)));
			//$connection->post('statuses/destroy', array('id' => 5437877770));
			//$connection->post('friendships/create', array('id' => 9436992)));
			//$connection->post('friendships/destroy', array('id' => 9436992)));
		}
	}


?>