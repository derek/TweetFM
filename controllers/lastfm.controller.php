<?
	class Controller_lastfm
	{
		static public function main()
		{
			$url = "http://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user=drgath&api_key=6436ab894729af9414d64f4847791dca&format=json";
			
			$json = file_get_contents($url);
			$data = json_decode($json, true);

			foreach ($data['recenttracks']['track'] as $i => $track)
			{
				if ($i < 10)
				{
					$tracks[] = array(
						'artist' => $track['artist']['#text'],
						'album'  => $track['album']['#text'],
						'track'  => $track['name'],
					);
				}
			}
			
			$data = array(
				"tracks" => $tracks,
				"username" => "derek"
			);

			VIEW::render(TEMPLATE::get("pages/lastfm", $data));
		}
	}
?>