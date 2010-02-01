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
				Tweet.FM (alpha)
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
		
		<script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
		try {
		var pageTracker = _gat._getTracker("UA-51709-15");
		pageTracker._trackPageview();
		} catch(err) {}</script>
	</body>
</html>