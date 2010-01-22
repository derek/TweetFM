<?
	class Controller_home
	{
		static public function main($method = null)
		{
			if (isset($_SESSION['twitter']['user_id']) && $_SESSION['twitter']['user_id'] > 0)
				URL::redirect("/user");
			else
				self::homepage();
		}
		
		static private function homepage()
		{	
			VIEW::render(TEMPLATE::get("pages/connect", $page));
		}
	}
?>