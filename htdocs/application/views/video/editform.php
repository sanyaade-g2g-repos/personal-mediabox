<script type="text/javascript">
var EditVideoForm = function(config) {
	config = config || {};
	this.panel = $('#'+config.panel_id);
	this.video_id = config.video_id;
}
$.extend(EditVideoForm.prototype,{

	updateVideo: function() {
		var formData = {
			video_id: this.video_id
			,title: this.panel.find('input[name=title]').val()
			,imdbid: this.panel.find('input[name=imdbid]').val()
			,year: this.panel.find('input[name=year]').val()
			,description: this.panel.find('textarea[name=description]').val()
			,tags: this.panel.find('textarea[name=tags]').val()
			,artists: this.panel.find('textarea[name=artists]').val()
			,filepath: this.panel.find('input[name=filepath]').val()
			,fileurl: this.panel.find('input[name=fileurl]').val()
			,width: this.panel.find('input[name=width]').val()
			,height: this.panel.find('input[name=height]').val()
		};
		$.ajax({
			url: '/index.php/video/update'
			,type: 'post'
			,data: formData
			,context: this
			,success: function(data, statusText, jqXHR) {
				this.close();
			}
			,error: function(data, statusText, jqXHR) {
				
			}
		});	
	},
	
	close: function() {
		var parent = this.panel.parent();
		this.panel.remove();
		$.ajax({
			url: '/index.php/video/info'
			,type: 'post'
			,data: {
				'v': this.video_id
			}
			,context: parent
			,success: function(data, statusText, jqXHR) {
				$(this).html(data);
			}
			,error: function(data, statusText, jqXHR) {
				
			}
		});
	}
	
});

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
			<div><label>Titel <input type="text" name="title" value="<? echo $Title; ?>" /></label></div>
			<div><label>IMDb# <input type="text" name="imdbid" value="<? echo $ImdbId; ?>" /></label></div>
		</div>
		<div>
			<div><label>Jahr <input type="text" name="year" value="<? echo $Year; ?>"/></label></div>
			<div><label>Beschreibung <textarea name="description"><? echo $Description; ?></textarea></label></div>
			<div><label>Tags <textarea name="tags"><?
				$tagstr_s = array();
				foreach ($Tags as $tag) { 
					$tagstr_s[] = $tag;
				}  
				echo implode("\n",$tagstr_s); 
			?></textarea></label></div>
			<div><label>Schauspieler <textarea name="artists"><?
				$artiststr_s = array();
				foreach ($Artists as $artist) { 
					$artiststr_s[] = $artist;
				}  
				echo implode("\n",$artiststr_s); 
			?></textarea></label></div>
		</div>
		<div>
			<div><label>Dateipfad <input type="text" name="filepath" value="<? echo $FilePath; ?>"/></label></div>
			<div><label>Datei URL <input type="text" name="fileurl" value="<? echo $FileUrl; ?>"/></label></div>
			<div><label>Breite <input type="text" name="width" value="<? echo $Width; ?>"/></label></div>
			<div><label>Höhe <input type="text" name="height" value="<? echo $Height; ?>"/></label></div>
		</div>
		<div><input type="button" value="Zurück" onclick="javascript:<? echo $panel_id; ?>.close();" /> <input type="submit" value="Speichern" /></div>
	</form>
</div>