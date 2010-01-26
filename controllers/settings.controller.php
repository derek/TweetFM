<?
	class Controller_settings
	{
		static public function main()
		{	
			$data = array();
			VIEW::render(TEMPLATE::get("pages/settings", $data));
		}
	}
?>