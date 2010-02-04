
<? foreach ($comments as $comment) { ?>
	<div>
		<p><?= $comment['comment']; ?></p>
		<p><?= $comment['track']['artist'] ?> - <?= $comment['track']['track']?></p>
		<p><a href="<?= URL::site($author['username'] . "/comment/" . $comment['comment_id']); ?>"><?= TIME::ago(strtotime($comment['date_created'])) ?></a></p>
	</div>
	<hr />
<? } ?>

<script>
	
	
	$(document).ready(function(){
		
	})

</script>