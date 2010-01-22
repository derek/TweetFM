<?
	class Controller_admin
	{
		static public function main()
		{
			SECURITY::permission("admin") or ERROR::throw_403();
			echo "admin";
		}
	}

?>