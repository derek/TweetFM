<?
	class Controller_api
	{
		static public function main($class = null, $method = null, $params = null)
		{
			$params = array_merge($_POST, $_GET);
			
			if (isset($params['key']))
			{
				$GLOBALS['user_id'] = self::_key_to_user($params['key']);
				
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
		
		
		// **********
		// Listen
		
			static public function listen_create($params = array())
			{
				self::_require($params, array(
					"artist" => "Missing GET artist",
					"album"  => "Missing GET album",
					"track"  => "Missing GET track",
					"hash"   => "Missing GET hash",
				));
			
				$track_id 	 = $GLOBALS['db']->fetchOne("SELECT track_id FROM tracks WHERE hash = ?", array($params['hash']));
				$last_listen = $GLOBALS['db']->fetchOne("SELECT track_id FROM listens WHERE user_id = ? ORDER BY listen_id DESC LIMIT 1", array($GLOBALS['user_id']));
			
				if ($track_id > 0 && $track_id == $last_listen)
				{
					return array(
						"_message" => "Already logged this listen for track_id ({$track_id})"
					);
				}
				else
				{
					if ($track_id < 1)
					{
						$GLOBALS['db']->insert('tracks', array(
							'hash'   => $params['hash'],
							'artist' => $params['artist'],
							'album'  => $params['album'],
							'track'  => $params['track'],
						));

						$track_id = $GLOBALS['db']->fetchOne("SELECT track_id FROM tracks WHERE hash = ?", array($params['hash']));
					}

					$GLOBALS['db']->insert('listens', array(
						'user_id'   => $GLOBALS['user_id'],
						'track_id' => $track_id,
					));

					$listen_id = $GLOBALS['db']->fetchOne("SELECT listen_id FROM listens WHERE user_id = ? ORDER BY listen_id DESC LIMIT 1", array($GLOBALS['user_id']));

					return array(
						"_message" => "user_id ({$GLOBALS['user_id']}) listened to track_id ({$track_id}) as listen_id ({$listen_id})"
					);				
				}
			}

			static public function listen_get($params = array())
			{
				self::_require($params, array(
					"listen_id" => "Missing GET listen_id",
				));
		
				$comments =  $GLOBALS['db']->fetchAll("SELECT c.comment_id, c.comment, c.user_id, c.listen_id, c.date_created FROM comments c WHERE listen_id = ? ORDER BY comment_id ASC", array($params['listen_id']));
				foreach ($comments as &$comment)
				{
					$comment['author'] = USER::get_info($comment['user_id']);
					unset($comment['user_id']);
				}
				$track 		= $GLOBALS['db']->fetchRow("SELECT t.artist, t.album, t.track FROM tracks t LEFT JOIN listens l ON t.track_id = l.track_id WHERE l.listen_id = ?", array($params['listen_id']));

				$track_id 	= $GLOBALS['db']->fetchOne("SELECT track_id FROM listens WHERE listen_id = ?", array($params['listen_id']));
				$track['comments'] = $GLOBALS['db']->fetchAll("SELECT c.comment_id, c.comment, c.user_id, c.listen_id, c.date_created FROM comments c WHERE listen_id IN (SELECT listen_id FROM listens WHERE track_id = ?) ORDER BY comment_id ASC", array($track_id));

				foreach ($track['comments'] as &$comment)
				{
					$comment['author'] = USER::get_info($comment['user_id']);
					unset($comment['user_id']);
				}
			
				return array(
					"comments" 	=> $comments,
					"track"		=> $track
				);
			}
		
			static public function listen_like($params = array())
			{
				self::_require($params, array(
					"listen_id" => "Missing GET listen_id",
				));

				$GLOBALS['db']->insert('likes', array(
					'user_id'  	=> $GLOBALS['user_id'],
					'listen_id' => $params['listen_id'],
				));
			
				return array(
					"_message" => "Liked"
				);
			}

		
		// *********
		// Comment

			static public function comment_create($params = array())
			{
				self::_require($params, array(
					"listen_id" => "Missing GET listen_id",
				));

				$track_id  		= $GLOBALS['db']->fetchOne("SELECT track_id FROM listens WHERE listen_id = ?", array($params['listen_id']));
				//$listen_id 		= $GLOBALS['db']->fetchOne("SELECT listen_id FROM listens WHERE track_id = ? AND user_id = ? ORDER BY listen_id DESC LIMIT 1", array($track_id, $GLOBALS['user_id']));
				$last_comment 	= $GLOBALS['db']->fetchOne("SELECT comment FROM comments WHERE listen_id = ? AND user_id = ? ORDER BY comment_id DESC LIMIT 1", array($params['listen_id'], $GLOBALS['user_id']));
			
				if ($last_comment == $params['comment'])
				{
					return array(
						"_message" => "Already logged this comment for listen_id ({$params['listen_id']})"
					);
				}
				else
				{
					$GLOBALS['db']->insert('comments', array(
						'user_id'  	=> $GLOBALS['user_id'],
						'listen_id' => $params['listen_id'],
						'comment' 	=> $params['comment'],
					));

					$comment_id = $GLOBALS['db']->fetchOne("SELECT comment_id FROM comments WHERE user_id = ? ORDER BY comment_id DESC LIMIT 1", array($GLOBALS['user_id']));

					return array(
						"_message" => "comment_id ({$comment_id}) recorded for user_id ({$GLOBALS['user_id']}) on track_id ({$track_id}) with listen_id ({$params['listen_id']})"
					);				
				}		
			}
		
		
		// *********
		// User
		
			static public function user_get_timeline($params)
			{
				$listens = $GLOBALS['db']->fetchAll("SELECT t.artist, t.album, t.track, t.hash, l.listen_id, l.date_created FROM tracks t LEFT JOIN listens l ON l.track_id = t.track_id WHERE user_id = ? ORDER BY l.listen_id DESC", array(Controller_api::_user_name_to_id($params['user'])));
			
				foreach ($listens as &$listen)
				{
					$comments 		 = $GLOBALS['db']->fetchAll("SELECT c.comment_id, c.comment, c.user_id, c.listen_id, c.date_created FROM comments c WHERE listen_id = ? ORDER BY comment_id ASC", array($listen['listen_id']));
					$listen['liked'] = $GLOBALS['db']->fetchOne("SELECT TRUE FROM likes WHERE listen_id = ? AND user_id = ?", array($listen['listen_id'], $GLOBALS['user_id']));
					
					foreach ($comments as &$comment)
					{
						$comment['author'] = USER::get_info($comment['user_id']);
						unset($comment['user_id']);
					}
					$listen['comments'] = $comments;
				}
			
				$listener = USER::get_info(self::_user_name_to_id($params['user']));
			
				return array(
					"_message" => "(" . count($listens) . ") tracks returned",
					"listener" => $listener,
					"listens" => $listens
				);
			}
			
			static public function user_login($params)
			{
				self::_require($params, array(
					"user_id" => "Missing GET user_id",
				));
				
				$info = $GLOBALS['db']->fetchRow("SELECT u.user_id, u.key, u.username FROM users u WHERE twitter_id = ?", array($params['user_id']));
				
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
		
		static private function _key_to_user($key)
		{
			return $GLOBALS['db']->fetchOne("SELECT user_id FROM users WHERE key = ?", array($key));
		}
		
		static private function _user_name_to_id($username)
		{
			return $GLOBALS['db']->fetchOne("SELECT user_id FROM users WHERE username = ?", array($username));
		}
	}
	
	class User
	{
		static public function get_info($user_id)
		{
			return $GLOBALS['db']->fetchRow("SELECT user_id, username FROM users WHERE user_id = ?", array($user_id));	
		}
	}
?>