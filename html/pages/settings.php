<? if (!empty($_POST)) { ?>
	<p>Saved!</p>
<? } ?>

<form action="<?= URL::site("/settings")?>" method="POST">

Last.FM Username: <input type="text" name="lastfm_name" value="<?= $user['lastfm_name'] ?>">
<br />
<br />
Email: <input type="email" name="email" value="<?= $user['email'] ?>">
<br />
<br />
<input type="submit" value="Submit">

</form>