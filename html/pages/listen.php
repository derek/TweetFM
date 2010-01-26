<h2>Track Info</h2>
<p><?= $listen['track']['artist'] ?> - <?= $listen['track']['album'] ?> - <?= $listen['track']['track'] ?></p>

<br />
<br />

<h2>Comments on this listen</h2>
<ul>
<? foreach ($listen['comments'] as $comment) { ?>
	<li><a href="<?= URL::site("/user/" . $comment['author']['username']) ?>"><?= $comment['author']['username'] ?></a>: <?= $comment['comment'] ?></li>
<? } ?>
</ul>

<br />
<br />

<h2>All comments on this track</h2>
<ul>
<? foreach ($listen['track']['comments'] as $comment) { ?>
	<li><a href="<?= URL::site("/user/" . $comment['author']['username']) ?>"><?= $comment['author']['username'] ?></a>: <?= $comment['comment'] ?></li>
<? } ?>
</ul>

