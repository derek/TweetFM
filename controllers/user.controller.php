<?
	class Controller_user
	{
		static public function main($username = null)
		{
			if (is_null($username))
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
			$location_call 	= API::get("user", "get_locations", array("user" => $username));
			$friends_call 	= API::get("user", "get_friends", array("user" => $username));
			
			$data = array(
				"locations" => $location_call['locations'],
				"friends"	=> $friends_call['friends']
			);
			
			VIEW::render(TEMPLATE::get("pages/userhome", $data));
		}
		
		static private function userpublic($username)
		{	
			
		}
	}
?>