<?
	class Controller_user
	{
		static public function main($username = null)
		{
			if (empty($username))
			{
				$username = $_SESSION['twitter']['username'];
				$public = false;
			}
			else
			{
				$public = true;
			}

			if ($public) 
			{	
				self::userhome($username);
			}
			else
			{	
				self::userhome($username);
			}
		}
		
		
		static private function userhome($username)
		{
			$timeline = API::get("user", "get_timeline", array("user" => $username));
			
			$data = array(
				"timeline" => $timeline,
			);
			
			VIEW::render(TEMPLATE::get("pages/userhome", $data));
		}
		
		static private function userpublic($username)
		{	
			
		}
	}
?>