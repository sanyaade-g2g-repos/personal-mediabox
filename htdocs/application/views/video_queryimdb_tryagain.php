<p>Die IMDB enthält keinen Film mit dem Titel '<?php echo $movie_title;?>'</p>
<p>Die Antwort der IMDb API war:</p>
<code><?php print_r($imdb_info);?></code>
<a href="http://www.imdb.com/find?s=all&q=<?php echo $movie_title; ?>" target="_blank">Das kann nicht sein.</br>Ich will die imdb selbst durchsuchen.</a>
<form name="updatetitleform" id="updatetitleform" method="post" action="">
	<p>Kopieren Sie die IMDb ID des Films in dieses Feld und wählen Sie dann &quot;Film aktualisieren&quot; um den Film mit den Daten aus der IMDb zu aktualisieren</p>
	<label for="movieid">Film ID</label>
	<input type="text" name="movieid" value="" />
	<input type="submit" name="updatetitle" value="Film aktualisieren"
</form>