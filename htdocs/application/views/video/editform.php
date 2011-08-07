<script type="text/javascript">
$(function(){
	<? echo $panel_id; ?> = new EditVideoForm({
		panel_id: "<? echo $panel_id; ?>"
		,video_id: "<? echo $video_id; ?>"
	});
});
</script>
<div id="<? echo $panel_id; ?>">
	<form action="" method="post" onsubmit="javascript:<? echo $panel_id; ?>.updateVideo();return false;">
		<input type="hidden" type="video_id" value="<? echo $video_id; ?>" />
		<div>
			<label>Titel <input type="text" name="title" /></label>
			<label>IMDb# <input type="text" name="imdbid" /></label>
		</div>
		<div>
			<label>Jahr <input type="text" name="year" /></label>
			<label>Beschreibung <textarea name="description"></textarea></label>
			<label>Tags <textarea name="tags"></textarea></label>
		</div>
		<div>
			<label>Dateipfad <input type="text" name="filepath" /></label>
			<label>Datei URL <input type="text" name="fileurl" /></label>
			<label>Breite <input type="text" name="width" /></label>
			<label>Höhe <input type="text" name="height" /></label>
		</div>
		<label></label>
		<div><input type="button" value="Zurück" onclick="javascript:<? echo $panel_id; ?>.close();" /> <input type="submit" value="Speichern" /></div>
	</form>
</div>