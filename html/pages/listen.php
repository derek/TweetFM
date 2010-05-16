<h2>Track Info</h2>
<p><?= $track['artist'] ?> - <?= $track['album'] ?> - <?= $track['track'] ?></p>

<br />
<br />

<h2>Comments on this listen</h2>
<ul>
<? foreach ($comments as $comment) { ?>	
		<img src="<?= $comment['author']['picture_url'] ?>" style="float:left">
		<a href="<?= URL::site($comment['author']['username'])?>"><?= $comment['author']['username'] ?></a>: <?= $comment['comment'] ?> 
		<br />
		<a href="<?= URL::site($comment['author']['username'] . "/comment/" . $comment['comment_id']) ?>"><?= TIME::ago(strtotime($comment['date_created'])) ?></a>
		<div style="clear:both"></div>
<? } ?>
</ul>

<br />
<br />
<!--
<h2>All comments on this track</h2>
<ul>
</ul>

-->