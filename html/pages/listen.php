<h2>Track Info</h2>
<p><?= $track['artist'] ?> - <?= $track['album'] ?> - <?= $track['track'] ?></p>

<br />
<br />

<h2>Comments</h2>
	<? foreach ($comments as $comment) { ?>	
		<div class="comment">
			<img src="<?= $comment['author']['picture_url'] ?>" style="float:left">
			<a href="<?= URL::site($comment['author']['username'])?>"><?= $comment['author']['username'] ?></a>: <?= $comment['comment'] ?> 
			<br />
			<a href="<?= URL::site($comment['author']['username'] . "/comment/" . $comment['comment_id']) ?>"><?= TIME::ago(strtotime($comment['date_created'])) ?></a>
			<div style="clear:both"></div>
		</div>
	<? } ?>
	
	<? if(isset($_SESSION['key']) && !empty($_SESSION['key'])) { ?>
	<div class="comment">
		<img src="<?= $_SESSION['twitter']['picture_url']?>" /> <textarea id="new_comment" name="comment" style="width:400px; height:50px;"></textarea>
		<input type="checkbox" name="twitter" checked="checked"> Send to Twitter
		<input type="button" value="Submit" class="submit_comment_button"> 
		<span class="pseudolink cancel_comment_link">Cancel</span>
	</div>
	<? } else { ?>
		<p>You can leave a comment too, but you need to <a href="/action/login">sign in</a> first.<p>
	<? }?>
</ul>

<br />
<br />
<!--
<h2>All comments on this track</h2>
<ul>
</ul>

-->


<script>

	function submitCommentHandler(e)
	{
		listen_id 	= "<?= $listen_id ?>"
		comment 	= $("#new_comment").val();
		twitter		= $("input[name=twitter]").attr("checked");
		
		API.post("comment", "create", {
			"comment"	: comment,
			"listen_id"	: listen_id,
			"twitter"	: twitter,
		}, function(response, error){
			window.location.reload();
		});
	}
	
	
	$(document).ready(function(){
		$(".submit_comment_button").live("click", submitCommentHandler);
	})
	
</script>

<style>
	.comment{
		padding:10px;
	}
</style>