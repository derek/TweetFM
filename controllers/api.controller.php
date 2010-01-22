<?
	class Controller_api
	{
		static public function main($class = null, $method = null, $params = null)
		{
			$params = $_POST;
			
			$GLOBALS['user_id'] = self::_key_to_user($params['key']);
			
			if (empty($GLOBALS['user_id']))
			{
				$response = array(
					"_message" => "Invalid API key"
				);
			}
			else
			{
				$response = call_user_func("Controller_api::" . $class . "_" . $method, $params);
			}
			
			exit(json_encode($response));
		}
		
		static public function listen_create($params = array())
		{
			self::_require($params, array(
				"artist" => "Missing GET artist",
				"album" => "Missing GET album",
				"track" => "Missing GET track",
				"hash" => "Missing GET hash",
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

		static public function comment_create($params = array())
		{
			self::_require($params, array(
				"hash" => "Missing GET hash",
			));

			$track_id  		= $GLOBALS['db']->fetchOne("SELECT track_id FROM tracks WHERE hash = ?", array($params['hash']));
			$listen_id 		= $GLOBALS['db']->fetchOne("SELECT listen_id FROM listens WHERE track_id = ? AND user_id = ? ORDER BY listen_id DESC LIMIT 1", array($track_id, $GLOBALS['user_id']));
			$last_comment 	= $GLOBALS['db']->fetchOne("SELECT comment FROM comments WHERE listen_id = ? AND user_id = ? ORDER BY comment_id DESC LIMIT 1", array($listen_id, $GLOBALS['user_id']));
			
			if ($last_comment == $params['comment'])
			{
				return array(
					"_message" => "Already logged this comment for listen_id ({$listen_id})"
				);
			}
			else
			{
				$GLOBALS['db']->insert('comments', array(
					'user_id'  	=> $GLOBALS['user_id'],
					'listen_id' => $listen_id,
					'comment' 	=> $params['comment'],
				));

				$comment_id = $GLOBALS['db']->fetchOne("SELECT comment_id FROM comments WHERE user_id = ? ORDER BY comment_id DESC LIMIT 1", array($GLOBALS['user_id']));

				return array(
					"_message" => "comment_id ({$comment_id}) recorded for user_id ({$GLOBALS['user_id']}) on track_id ({$track_id}) with listen_id ({$listen_id})"
				);				
			}		
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
	}
?>