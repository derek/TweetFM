

<h1><?= $comment['track']['artist']?> - <?= $comment['track']['track']?></h1>
<div class="comment">
	<img src="<?= $comment['author']['picture_url'] ?>" style="float:left">
	<a href="<?= URL::site($comment['author']['username'])?>"><?= $comment['author']['username'] ?></a>: <?= $comment['comment'] ?> 
	<br />
	<a href="<?= URL::site($comment['author']['username'] . "/comment/" . $comment['comment_id']) ?>"><?= TIME::ago(strtotime($comment['date_created'])) ?></a>
	<div style="clear:both"></div>
</div>