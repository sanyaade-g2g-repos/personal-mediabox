<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Watch <?php echo $Title;?></title>
<script type="text/javascript" src="http://www.sandbox.ch/ci/assets/js/jquery-1.6.2.js"></script>
<script type="text/javascript" src="http://html5.kaltura.org/js"></script> 
<style type="text/css">

body {
 background-color: #fff;
 margin: 40px;
 font-family: Lucida Grande, Verdana, Sans-serif;
 font-size: 14px;
 color: #4F5155;
}

a {
 color: #003399;
 background-color: transparent;
 font-weight: normal;
}

h1 {
 color: #444;
 background-color: transparent;
 border-bottom: 1px solid #D0D0D0;
 font-size: 16px;
 font-weight: bold;
 margin: 24px 0 2px 0;
 padding: 5px 0 6px 0;
}

code {
 font-family: Monaco, Verdana, Sans-serif;
 font-size: 12px;
 background-color: #f9f9f9;
 border: 1px solid #D0D0D0;
 color: #002166;
 display: block;
 margin: 14px 0 14px 0;
 padding: 12px 10px 12px 10px;
}

.mediainfo {
	font-size: 12px;
}


#imdb-update-results p {
	margin: 0;
}
#imdb-update-results .error {
	padding: 5px 20px;
	width: 350px;
	font-family: Verdana, Geneva, Tahoma, sans-serif;
	font-size: 12px;
	border: 1px solid red;
	border-top: 3px solid red;
	background-color: #FFF0F0;
}
#imdb-update-results .info {
	padding: 5px 20px;
	width: 350px;
	font-family: Verdana, Geneva, Tahoma, sans-serif;
	font-size: 12px;
	border: 1px solid blue;
	border-top: 3px solid blue;
	background-color: #F0F0FF;
}


</style>
<script type="text/javascript">
var updateFromImdb = function() {
	$.ajax({
		url: "/index.php/video/updateFromImdb?v=<?php echo $Title;?>&noretry=1"
		,context: $('#imdb-update-results')
		,success: function(data, textStatus, jqXHR){
			$(this).show().html(data).delay(1000).fadeOut("slow");
		}
		,error: function(data, textStatus, jqXHR) {
			$(this).show().html(data).delay(1000).fadeOut("slow");
		}
	});
}
var getMediaInfo = function() {
	$.ajax({
		url: "/index.php/video/getMediaInfo?v=<?php echo $Title;?>"
		,context: $('#mediainfo-results')
		,success: function(data, textStatus, jqXHR) {
			$('#mediainfo-results .results').html(data);
			$(this).show();
		}
		,error: function(data, textStatus, jqXHR) {
			$('#mediainfo-results .results').html(data);
			$(this).show().delay(1000).fadeOut("slow");
		}
	});
}
var closeMediaInfo = function() {
	$('#mediainfo-results').fadeOut();
}
var removeMediaItem = function() {
	$.ajax({
		url: '/index.php/video/removeItem'
		,data: {
			'id': '<?php echo $Title; ?>'
		}
		,type: 'post'
		,success: function(data, textStatus, jqXHR) {
			$('#remove-results').fadeIn("slow");
		}
		,error: function(data, textStatus, jqXHR) {
			$('#remove-results').fadeIn("slow").delay(2000).fadeOut("slow");
		}
	});
}
var editMediaItem = function() {
	$('#edit-messages').html('<div>Loading...</div>').fadeIn().delay(1000).fadeOut("slow");
	$.ajax({
		url: '/index.php/video/edit'
		,data: {
			'v': '<?php echo $Title; ?>'
		}
		,type: 'post'
		,success: function(data, textStatus, jqXHR) {
			$('#edit-messages').html(
					'<div>Loading Successful</div>'
			).fadeIn().delay(1000).fadeOut("slow").empty();
			$('.video-info').html(data);
		}
		,error: function(data, textStatus, jqXHR) {
			$('#edit-messages').html(data).fadeIn("slow").delay(2000).fadeOut("slow").empty();
		}
	});
}
</script>
</head>
<body>
<div style="position:absolute; margin-left:300px;">
	<a href="javascript:editMediaItem();" title="Metadaten von <?php echo $Title; ?> bearbeiten">Bearbeiten</a>
	<div id="edit-messages" style="display:none; position:absolute; z-index:1000; background: white;">
	</div>
	|
	<a href="javascript:removeMediaItem();" title="<?php echo $Title; ?> aus der Bibliothek entfernen">Entfernen</a>
	<div id="remove-results" style="display:none; position:absolute; z-index:1000; background:white;">
		Das Element wurde gelöscht. <a href="/index.php" title="">Klicken Sie hier</a> um zurück zur Startseite zu gelangen</a>
	</div>
	|
	<a href="javascript:updateFromImdb();" title="Informationen auf imdb.com suchen">IMDb Update</a>
	<div id="imdb-update-results" style="position:absolute; margin-top:20px; z-index:1000;"> </div>
	|
	<a href="javascript:getMediaInfo();" title="Erweiterte Informationen &uuml;ber den Medienstream anzeigen">MediaInfo</a>
	<div id="mediainfo-results" style="display:none; position:absolute; z-index:1000; background:white; border:1px solid #F0F0F0;">
		<div style="float:right;"><a href="javascript:closeMediaInfo();" title="Erweiterte Informationen ausblenden">Schliessen</a></div>
		<div class="results"></div>
	</div>
	|
	<a href="/index.php" title="Zurück zur Startseite">Zur&uuml;ck</a>
</div>
<h1><?php echo $Title;?></h1>
<div class="video-box">
	File Url: <a href="<?php echo $FileUrl;?>"><?php echo $FileUrl;?></a>
	<video id="vid<?php echo $Index;?>" width="480" height="267" durationHint="33" poster="<?php echo $PreviewImgUrl;?>">
		<source	src="<?php echo $FileUrl;?>"/>
	</video>
</div>
<div class="video-info">
	<p><?php echo $Description; ?></p>
	<?php echo isset($Year) ? '<p>Released in: '.$Year.'</p>' : ''; ?>
	<? if (count($Tags) > 0) { ?>
	<h3>Tags</h3>
	<ul class="tags">
	<? foreach ($Tags as $Tag) { ?>
		<li><? echo $Tag; ?></li>
	<? } /*endforeach*/ ?>
	</ul>
	<? } /*endif */ ?>
	<? if (count($Artists) > 0) { ?>
	<h3>Schauspieler</h3>
	<ul>
	<? foreach ($Artists as $Artist) { ?>
		<li><? echo $Artist; ?></li>
	<? } /*endforeach*/ ?>
	</ul>
	<? } /*endif */ ?>
	<h3>Links</h3>
	<? if (!isset($ImdbHref)) { ?>
	<form action="" method="post" name="setImdbId">
		<label for="imdbid">IMDb ID</label> <input type="text" name="imdbid" value="" /> <input type="button" value="Speichern" />
	</form>
	<? } else { ?>
	<a href="<? echo $ImdbHref; ?>" title="" target="_blank"><? echo $Title; ?> on IMDb (<? echo $ImdbId; ?>)</a>
	<? } ?>
</div>
</body>
</html>