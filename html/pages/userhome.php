
<? foreach ($timeline['listens'] as $listen) { ?>
	<div class="listen_div" id="listen_id_<?= $listen['listen_id'] ?>">
		
		<p><strong><?= $listen['artist'] ?></strong> -  <?= $listen['track'] ?></p>
		
		<div class="listen_footer">
			<a href="<?= URL::site($timeline['listener']['username'] . "/listen/" . $listen['listen_id']); ?>"><?= TIME::ago(strtotime($listen['date_created'])) ?></a>

			<? if(isset($_SESSION['key']) && !empty($_SESSION['key'])) { ?>
				-
				<span class="pseudolink add_comment_button">Comment</span>
				<span class="pseudolink like_listen_link"><?= ($listen['liked'] == "true")?"Unlike":"Like"; ?></span>
			<? } ?>
		</div>
		<? foreach ($listen['comments'] as $comment) { ?>
			<div class="comment"><?= $comment['author']['username'] ?>: <?= $comment['comment'] ?></div>
		<? } ?>
	
		<div  class="comment add_comment_div">
			<?= $_SESSION['twitter']['username'] ?>: 
			<input type="text" name="comment">
			<input type="button" value="Comment" class="submit_comment_button"> 
			<span class="pseudolink cancel_comment_link">Cancel</span>
		</div>
	</div>
	<hr />
<? } ?>

<script>
	
	function addCommentHandler(e)
	{
		listen_id = $(e.target).parents(".listen_div")[0].id.replace("listen_id_", "");
		$("#listen_id_" + listen_id + " .add_comment_div").show();
		$("#listen_id_" + listen_id + " .add_comment_div input[type=text]").focus();
	}

	function cancelCommentHandler(e)
	{
		listen_id = $(e.target).parents(".listen_div")[0].id.replace("listen_id_", "");
		$("#listen_id_" + listen_id + " .add_comment_div input[type=text]").val('');
		$("#listen_id_" + listen_id + " .add_comment_div").hide();
	}

	function submitCommentHandler(e)
	{
		listen_id = $(e.target).parents(".listen_div")[0].id.replace("listen_id_", "");
		comment = $("#listen_id_" + listen_id + " .add_comment_div input[type=text]").val();
		API.post("comment", "create", {
			"listen_id" : listen_id,
			"comment"	: comment
		}, function(response, error){
			window.location.reload();
		});
	}
	
	function likeListenHandler(e)
	{
		listen_id = $(e.target).parents(".listen_div")[0].id.replace("listen_id_", "");
		
		if ($("#listen_id_" + listen_id + " .like_listen_link").html() == "Like")
			text = "Unlike";
		else
			text = "Like";
			
		API.post("listen", "like", {
			"listen_id" : listen_id,
		}, function(response, error){
			$("#listen_id_" + listen_id + " .like_listen_link").html(text);
		});
		
	}
	
	$(document).ready(function(){
		$(".add_comment_button").live("click", addCommentHandler);
		$(".submit_comment_button").live("click", submitCommentHandler);
		$(".cancel_comment_link").live("click", cancelCommentHandler);
		$(".like_listen_link").live("click", likeListenHandler);
	})
	
	function add_comment(listen_id){
		alert(listen_id)
	}
</script>

<style>
	.add_comment_div{
		display:none;
	}
</style>