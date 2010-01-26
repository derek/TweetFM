<html>
	
	<head>
		<link rel="stylesheet" href="/css/reset.css" type="text/css" media="screen, projection">
		<link rel="stylesheet" href="/css/default.css" type="text/css" media="screen, projection">
		<link rel="stylesheet" href="/css/main.css" type="text/css" media="screen, projection">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.0/jquery.js"></script>
		<script src="/js/general.js"></script>
		<script>
			BASE_URL = '<?= BASE_URL ?>';
			API_URL = '<?= API_URL ?>';
		</script>		
	</head>
	
	<body>
		<div id="header">
			<div style="float:left; font-weight:bold;">
				tweet.fm - Social Music
			</div>
			<div style="float:right; font-size:12px;">
				<? if (isset($_SESSION['twitter']['username'])){ ?>
				<a href="/home">Home</a> | 
				<a href="/<?= $_SESSION['twitter']['username'] ?>">Profile</a> | 
				<a href="/settings">Settings</a> | 
				<a href="/action/logout">Sign Out</a>
				<? } else { ?>
					<a href="/action/login">Sign In</a>
				<? } ?>
			</div>
			<div style="clear:both"></div>
		</div>
		
		<div id="content">
			<div style="margin:10px;">
				<?= $content ?>
			</div>
		</div>

	</body>
</html>