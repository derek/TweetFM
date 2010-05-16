<?
	class Controller_home
	{
		static public function main($username = null, $subpage = null, $sub_id = null)
		{
			if ($username)
			{	
				switch($subpage)
				{
					case "comment":
						self::comment($username, $sub_id);
						break;
						
					case "listens":
						self::listens($username);
						break;
						
					case "listen":
						self::listen($username, $sub_id);
						break;
						
					default:
						self::comments($username);
						break;
				}
			}
			elseif (isset($_SESSION['twitter']['user_id']) && $_SESSION['twitter']['user_id'] > 0)
			{
				self::friends();
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
		
		static private function comments($username)
		{
			$data = API::get("user", "comments", array("user" => $username));
			
			VIEW::render(TEMPLATE::get("pages/comments", $data));
		}
				
		static private function listens($username)
		{
			$data = API::get("user", "timeline", array("user" => $username));
			VIEW::render(TEMPLATE::get("pages/listens", $data));
		}
				
		static private function listen($username, $listen_id)
		{
			$data = API::get("listen", "get_info", array("user" => $username, "listen_id" => $listen_id));
			
			VIEW::render(TEMPLATE::get("pages/listen", $data));
		}
				
		static private function friends()
		{
			VIEW::render(TEMPLATE::get("pages/friend_timeline", $data));
		}
		
		static private function comment($username, $comment_id)
		{
			$comment = API::get("user", "get_comment", array("user" => $username, "comment_id" => $comment_id));
			VIEW::render(TEMPLATE::get("pages/comment", $comment));
		}
	}
?>