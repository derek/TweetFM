<h1>Your Recent Listens</h1>

<? foreach ($tracks as $i => $track) { ?>
	<div class="listen_div" id="listen_id_<?= $i ?>">
		
		<p><span class="artist"><?= $track['artist'] ?></span> - <span class="track"><?= $track['track'] ?></span> (<span class="album"><?= $track['album'] ?></span>)</p>
		
		<div class="listen_footer">
			<?= (empty($track['date']))?"Now Listening":TIME::ago($track['date']) ?>
			<? if(isset($_SESSION['key']) && !empty($_SESSION['key'])) { ?>
				<span class="pseudolink add_comment_button">Comment</span>
			<? } ?>
		</div>
		
		<div  class="comment add_comment_div">
			You say: <input type="text" name="comment" style="width:700px">
			<input type="checkbox" name="twitter" disabled="disabled"> Send to Twitter
			<input type="button" value="Submit" class="submit_comment_button"> 
			<span class="pseudolink cancel_comment_link">Cancel</span>
		</div>

		<? foreach ($track['comments'] as $comment) { ?>
			<div class="comment">
				<img src="<?= $comment['author']['picture_url'] ?>" style="float:left">
				<a href="<?= URL::site($comment['author']['username'])?>"><?= $comment['author']['username'] ?></a>: <?= $comment['comment'] ?> 
				<br />
				<a href="<?= URL::site($comment['author']['username'] . "/comment/" . $comment['comment_id']) ?>"><?= TIME::ago(strtotime($comment['date_created'])) ?></a>
				<div style="clear:both"></div>
			</div>
		<? } ?>
	
	</div>
	<hr />
<? } ?>

<script>
	
	function addCommentHandler(e)
	{
		listen_id = $(e.target).parents(".listen_div")[0].id.replace("listen_id_", "");
		$("#listen_id_" + listen_id + " .add_comment_button").hide();
		$("#listen_id_" + listen_id + " .add_comment_div").show();
		$("#listen_id_" + listen_id + " .add_comment_div input[type=text]").focus();
	}

	function cancelCommentHandler(e)
	{
		listen_id = $(e.target).parents(".listen_div")[0].id.replace("listen_id_", "");
		$("#listen_id_" + listen_id + " .add_comment_div input[type=text]").val('');
		$("#listen_id_" + listen_id + " .add_comment_button").show();
		$("#listen_id_" + listen_id + " .add_comment_div").hide();
	}

	function submitCommentHandler(e)
	{
		listen_id 	= $(e.target).parents(".listen_div")[0].id.replace("listen_id_", "");
		comment 	= $("#listen_id_" + listen_id + " .add_comment_div input[name=comment]").val();
		artist 		= $("#listen_id_" + listen_id + " .artist").html();
		album 		= $("#listen_id_" + listen_id + " .album").html();
		track 		= $("#listen_id_" + listen_id + " .track").html();
		twitter		= $("#listen_id_" + listen_id + " input[name=twitter]").attr("checked");
		
		API.post("comment", "create", {
			"comment"	: comment,
			"artist"	: artist,
			"album"		: album,
			"track"		: track,
			"twitter"	: twitter,
		}, function(response, error){
			window.location.reload();
		});
	}
	
	function likeListenHandler(e)
	{
		listen_id = $(e.target).parents(".listen_div")[0].id.replace("listen_id_", "");
		var state = $("#listen_id_" + listen_id + " .like_listen_link").html();
		console.log(state);
		if (state == "Like")
		{
			text 	= "Unlike"
			action 	= "like";
		}
		else
		{
			text 	= "Like"
			action 	= "unlike";
		}	
		API.post("listen", action, {
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