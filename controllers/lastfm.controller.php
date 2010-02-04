<?
	class Controller_lastfm
	{
		static public function main()
		{
			$data = API::get("user", "timeline", array("user" => "derek"));

			VIEW::render(TEMPLATE::get("pages/lastfm", $data));
		}
	}
?>