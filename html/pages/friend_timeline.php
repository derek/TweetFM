<p>If you haven't yet linked your Last.FM account, go do it on the <a href="/settings">settings page</a></p>
<br />
<br />
<p>This page is where your friends latest activity will eventually go. In the meantime, here are links to all the Tweet.fm users</p>

<ul>
	<? foreach ($users as $user) { ?>
		<li>@<?= $user['twitter_name'] ?> - <a href="/<?= $user['twitter_name'] ?>">Comments</a> / <a href="/<?= $user['twitter_name'] ?>/listens">Listens</a></li>
	<? } ?>
</ul>
