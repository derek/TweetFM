<img src="<?= $author['picture_url'] ?>" style="float:left; padding:0px 10px 10px 0px;" />
<h1><?= $author['username'] ?></h1>

<div style="clear:both;" ></div>

<? foreach ($comments as $comment) { ?>
	<div>
		<p style="font-weight:bold;">
			<?= $comment['track']['artist'] ?> - <?= $comment['track']['track']?>
			<a style='font-size:10px; font-weight:normal' href="<?= URL::site($author['username'] . "/comment/" . $comment['comment_id']); ?>"><?= TIME::ago(strtotime($comment['date_created'])) ?></a>
		</p>
		<p style='margin-left:10px;'><?= $comment['comment']; ?></p>
		
	</div>
	<hr />
<? } ?>

<script>
	
	
	$(document).ready(function(){
		
	})

</script>