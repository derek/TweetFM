<?
	class Controller_home
	{
		static public function main($username = null, $subpage = null, $sub_id = null)
		{
			if ($username)
			{
				if (!empty($subpage))
				{
					self::listen($sub_id);
				}
				else
				{
					self::userpublic($username);
				}
			}
			elseif (isset($_SESSION['twitter']['user_id']) && $_SESSION['twitter']['user_id'] > 0)
			{
				self::friend_timeline();
			}
			else
			{
				self::homepage();
			}
		}
		
		static private function homepage()
		{	
			VIEW::render(TEMPLATE::get("pages/home", $page));
		}
		
		static private function userpublic($username)
		{
			$timeline = API::get("user", "get_timeline", array("user" => $username));

			$data = array(
				"timeline" => $timeline,
				"username" => $username
			);

			VIEW::render(TEMPLATE::get("pages/userhome", $data));
		}
				
		static private function friend_timeline()
		{
			VIEW::render(TEMPLATE::get("pages/friend_timeline", $data));
		}
		
		static private function listen($listen_id)
		{
			$listen = API::get("listen", "get", array("listen_id" => $listen_id));
			
			$data = array(
				"listen" => $listen
			);
			
			VIEW::render(TEMPLATE::get("pages/listen", $data));
		}
	}
?>