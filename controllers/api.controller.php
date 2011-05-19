<?
	class Controller_api
	{
		static public function main($class = null, $method = null, $params = null)
		{
			$params = array_merge($_POST, $_GET);
			
			if (isset($params['key']))
			{
				$User = new User($params['key']);
				$GLOBALS['user_id'] = $User->user_id;
				
				if (empty($GLOBALS['user_id']))
				{
					$response = array(
						"_message" => "Invalid API key"
					);
				}
			}
			
			if (!method_exists("Controller_api", $class . "_" . $method))
			{	
				$response = array(
					"_message" => "(" . $class . "." . $method . ") is an invalid API method"
				);
			}
			else
			{
				$response = call_user_func("Controller_api::" . $class . "_" . $method, $params);
			}
			
			header("Content-type:application/json");
			exit(json_encode($response));
		}
		
		

		// *********
		// Comment

			static public function comment_create($params = array())
			{
				self::_require($params, array(
					"comment" 	=> "Missing GET comment",
				));
				
				$Track = Track::factory($params, true);
				if ($Track)
				{
					// TODO: Make this return a Comments object for the next 2 lines.
					if (!isset($params['listen_id']))
						$params['listen_id'] = false;
					$comment_id = $Track->add_comment($GLOBALS['user_id'], $params['comment'], $params['listen_id']);
					$listen_id = $GLOBALS['db']->fetchOne("SELECT listen_id FROM comments WHERE comment_id = ?", array($comment_id)); 
					
					if ($params['twitter'] == "true")
					{
						$status = $params['comment'];
						$user_info = $GLOBALS['db']->fetchRow("SELECT twitter_name, twitter_oauth_token, twitter_oauth_secret FROM users WHERE user_id = ?", array($GLOBALS['user_id'])); 
						
						$tokens = array(
							"oauth_token" => $user_info['twitter_oauth_token'],
							"oauth_token_secret" => $user_info['twitter_oauth_secret'],
						);
						
						if (strlen($status) > 90)
							$status = substr($status, 0, 90) . "...";
							
						$status .=  " http://tweet.fm/" . $user_info['twitter_name'] . "/listen/" . $listen_id;
						$Twitter = new Twitter($tokens); 
						$response = $Twitter->post("statuses/update", array('status' => $status));		
					}
					

					return array(
						"_message" => "Comment posted",
						"comment_id" => $comment_id,
						"listen_id" => $listen_id,
						"user" => $user_info['twitter_name'],
					);	
				}
				else
				{
					return array(
						"_message" => "Error creating track"
					);
				}
			}
		
		
		// *********
		// User
		
			static public function users_all($params)
			{
				$users = $GLOBALS['db']->fetchAll("SELECT twitter_name FROM users u WHERE lastfm_name IS NOT NULL ORDER BY twitter_id ASC");
				
				return array(
					"users" => $users
				);
			}
		
			static public function user_timeline($params)
			{
				$User = new User($params['user']);
				$user_info = $User->get_info();
				
				$url = "http://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user=" . $user_info['lastfm_name'] . "&api_key=6436ab894729af9414d64f4847791dca&format=json&limit=24";

				$data = json_decode(file_get_contents($url), true);

				foreach ($data['recenttracks']['track'] as $i => $t)
				{
					$track = array(
						'artist' => $t['artist']['#text'],
						'album'  => $t['album']['#text'],
						'track'  => $t['name'],
						'date'   => $t['date']['uts'],
					);
					
					$Track = Track::factory($track);
					
					if ($Track)
					{
						$track['comments'] = $Track->get_comments($track);
						//	$track['comments'] = array();
					}
					else
					{
						$track['comments'] = array();
					}
					
					$tracks[] = $track;
				}
			
				return array(
					"_message" => "(" . count($tracks) . ") tracks returned",
					"user" => $user_info,
					"tracks" => $tracks
				);
			}
			
			static public function user_comments($params)
			{
				$comments = $GLOBALS['db']->fetchAll("SELECT c.comment_id, c.comment, c.date_created, c.listen_id FROM comments c LEFT JOIN users u ON u.user_id = c.user_id WHERE u.twitter_name = ? ORDER BY comment_id DESC", array($params['user']));

				foreach ($comments as &$comment)
				{
					$Track = Track::factory($comment);
					$comment['track'] = $Track->get_info();
					//unset($comment['track_id']);
				}
				
				$User = new User($params['user']);
				$author = $User->get_info();
				
				return array(
					"author"	=> $author,
					"comments"  => $comments
				);			
			}
			
			static public function user_get_comment($params)
			{
				$comment = $GLOBALS['db']->fetchRow("SELECT c.comment_id, c.comment, c.date_created, c.listen_id, c.user_id FROM comments c WHERE comment_id = ?", array($params['comment_id']));

				$Listen = LISTEN::factory($comment);
				$comment['listen'] = $Listen->get_info();
				
				$Track = Track::factory($comment['listen']['track']);
				$comment['track'] = $Track->get_info();
				
				$User = new User($comment['user_id']);
				$comment['author'] = $User->get_info();
				
				unset($comment['track_id']);
				unset($comment['user_id']);
				
				return array(
					"comment" => $comment
				);
			}			
			
			static public function user_info($params)
			{
				$identifier = $params['user'] ? $params['user'] : $GLOBALS['user_id'];
				$User = new User($identifier);
				$info = $User->get_info();
				
				return array(
					"user" => $info
				);
			}	
					
			static public function user_update($params)
			{
				$User = new User($GLOBALS['user_id']);
				$User->update($params);
				$info = $User->get_info();
				
				return array(
					"user" => $info
				);
			}
			
			static public function user_login($params)
			{
				self::_require($params, array(
					"twitter_id" 			=> "Missing GET twitter_id",
					"twitter_name" 			=> "Missing GET twitter_name",
					"twitter_oauth_token"	=> "Missing GET twitter_oauth_token",
					"twitter_oauth_secret" 	=> "Missing GET twitter_oauth_secret",
				));
				
				$User = USER::factory($params['twitter_id']);
				
				$User->update(array(
					"twitter_name" 			=> $params['twitter_name'],
					"twitter_oauth_token"	=> $params['twitter_oauth_token'],
					"twitter_oauth_secret" 	=> $params['twitter_oauth_secret'],					
					"twitter_picture_url" 	=> $params['twitter_picture_url'],					
				));
				
				$info = $User->get_info();
				
				return $info;
			}
			
			static public function listen_get_info($params)
			{
				self::_require($params, array(
					"listen_id" 			=> "Missing GET listen_id",
				));
				
				$Listen = LISTEN::factory(array("user_id" => $GLOBALS['user_id'], "listen_id" => $params['listen_id']));
								
				$info = $Listen->get_info();
				
				return $info;
			}
		
		
		/** PRIVATE **/
		static private function _require($params, $fields)
		{
			foreach ($fields as $key => $error)
			{
				if (!array_key_exists($key, $params))
				{
					die(json_encode(array(
						"_message" => $error
					)));
				}
			}
		}
	}
	
	
	
	
	
	
	
	// Below are the API object models
	
	class User
	{
		static public function factory($twitter_id)
		{
			// See if the user has logged in here before
			$user_id = $GLOBALS['db']->fetchOne("SELECT u.user_id FROM users u WHERE twitter_id = ?", array($twitter_id));

			if (empty($user_id))
			{
				$key = MD5(microtime());
				$GLOBALS['db']->insert('users', array(
					'twitter_id'   	=> $twitter_id,
					'key'   		=> $key,
				));
			
				$user_id = $GLOBALS['db']->fetchOne("SELECT user_id FROM users WHERE key = ?", array($key));
			}
			
			$User = new User($user_id);
			
			return $User;
		}
		
		
		public function __construct($user_id)
		{
			
			if (is_string($user_id))
			{
				if (strlen($user_id) == 32)
				{
					$user_id = $GLOBALS['db']->fetchOne("SELECT user_id FROM users WHERE key = ?", array($user_id));
				}
				else
				{
					$user_id = $GLOBALS['db']->fetchOne("SELECT user_id FROM users WHERE twitter_name = ?", array($user_id));	
				}
			}
			
			$this->user_id = $user_id;
		}
		
		public function get_info()
		{
			return $GLOBALS['db']->fetchRow("SELECT user_id, key, twitter_name as username, twitter_picture_url as picture_url, lastfm_name, email FROM users WHERE user_id = ?", array($this->user_id));	
		}
		
		public function update($params)
		{
			$GLOBALS['db']->update('users', $params, array("user_id = ?" => $this->user_id));
			
			return true;
		}
	}

	class Listen
	{
		static public function factory($info, $create_if_not_exists = false)
		{
			if (isset($info['listen_id']))
			{
				$listen_id = $info['listen_id'];
			}
			else
			{
				$listen_id = false;
			}
			
			if (empty($listen_id) && $create_if_not_exists)
			{
				$GLOBALS['db']->insert('listens', array(
					'user_id'   	=> $info['user_id'],
					'track_id'   	=> $info['track_id'],
				));
		
				$listen_id = $GLOBALS['db']->fetchOne("SELECT listen_id FROM listens WHERE user_id = ? AND track_id = ? ORDER BY listen_id DESC LIMIT 1", array($info['user_id'], $info['track_id']));
			}
			
			if ($listen_id > 0)
			{
				$Listen = new Listen($listen_id);
				return $Listen;				
			}
			else
			{
				return false;
			}
		}
		
		public function __construct($listen_id) {
			$this->listen_id = $listen_id;
			
			$info = $this->get_info();
			
			$this->track_id = $info['track_id'];
		}
		
		public function get_info() {
			$info = $GLOBALS['db']->fetchRow("SELECT * FROM listens WHERE listen_id = ?", array($this->listen_id));
			$info['comments'] = $this->get_comments();
			
			$User = new User($info['user_id']);
			$info['user'] = $User->get_info();
			
			$Track = TRACK::factory(array("track_id" => $info['track_id']), false);
			$info['track'] = $Track->get_info();
			
			unset($info['track_id']);
			unset($info['user_id']);
			
			return $info;
		}
		
		public function get_comments()
		{
			$comments = $GLOBALS['db']->fetchAll("SELECT c.comment_id, c.comment, c.user_id, c.date_created FROM comments c WHERE listen_id = ? ORDER BY comment_id DESC", array($this->listen_id));

			foreach ($comments as &$comment)
			{
				$User = new User($comment['user_id']);
				$comment['author'] = $User->get_info();
				unset($comment['user_id']);
			}
			
			return $comments;
		}
		
	}
	
	class Track
	{
		static public function factory($info, $create_if_not_exists = false)
		{
			if (isset($info['track_id']))
			{
				$track_id = $info['track_id'];
			}
			else if (isset($info['listen_id']))
			{
				$track_id = $GLOBALS['db']->fetchOne("SELECT track_id FROM listens WHERE listen_id = ?", array($info['listen_id']));
				$info = $GLOBALS['db']->fetchRow("SELECT artist, album, track FROM tracks t LEFT JOIN listens l ON l.track_id = t.track_id WHERE l.listen_id = ?", array($info['listen_id']));
			}
			else
			{
				$track_id = $GLOBALS['db']->fetchOne("SELECT track_id FROM tracks WHERE artist = ? AND track = ?", array($info['artist'], $info['track']));
			}
			
			if (empty($track_id) && $create_if_not_exists)
			{
				$hashable_array = array(
					'artist'=> $info['artist'],
					'album'	=> $info['album'],
					'track'	=> $info['track'],
				);
				
				ksort($hashable_array);
				
				$hash = md5(json_encode($hashable_array));

				$GLOBALS['db']->insert('tracks', array(
					'hash'  => $hash,
					'artist'=> $info['artist'],
					'album'	=> $info['album'],
					'track'	=> $info['track'],
				));
				
				$track_id = $GLOBALS['db']->fetchOne("SELECT track_id FROM tracks WHERE hash = ?", array($hash));
			}
			
			if ($track_id > 0)
			{
				$Track = new Track($track_id);
				return $Track;				
			}
			else
			{
				return false;
			}
			
		}
		
		public function __construct($track_id)
		{
			$this->track_id = $track_id;
			
			return true;
		}
		
		public function get_comments()
		{
			$comments = $GLOBALS['db']->fetchAll("SELECT c.comment_id, c.comment, c.user_id, c.date_created FROM comments c LEFT JOIN listens l ON c.listen_id = l.listen_id WHERE l.track_id = ? ORDER BY comment_id DESC", array($this->track_id));

			foreach ($comments as &$comment)
			{
				$User = new User($comment['user_id']);
				$comment['author'] = $User->get_info();
				unset($comment['user_id']);
			}
			
			return $comments;
		}
		
		public function get_info()
		{
			$data = $GLOBALS['db']->fetchRow("SELECT t.track_id, t.artist, t.album, t.track FROM tracks t WHERE track_id = ?", array($this->track_id));

			return $data;
		}
		
		public function add_comment($user_id, $comment, $listen_id = false)
		{
			if ($listen_id)
			{
				$Listen = new Listen($listen_id);
			}
			else
			{
				$Listen = LISTEN::factory(array("user_id" => $user_id, "track_id" => $this->track_id), true);
			}
			
			$GLOBALS['db']->insert('comments', array(
				'listen_id' => $Listen->listen_id,
				'user_id'   => $user_id,
				'comment'   => $comment,
			));
		
			$comment_id = $GLOBALS['db']->fetchOne("SELECT comment_id FROM comments WHERE listen_id = ? AND comment = ?", array($Listen->listen_id, $comment));
			
			return $comment_id;		
		}
	}
?>