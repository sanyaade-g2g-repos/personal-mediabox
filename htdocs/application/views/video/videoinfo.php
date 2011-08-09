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