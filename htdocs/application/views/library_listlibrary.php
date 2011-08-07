<h4>Alle Filme</h4>
<a href="javascript:refreshLibraryList({by:'title',dir:'asc'});" title="Hier klicken um alle Filme alphabetisch anzuordnen">Titel (Aufsteigend)</a>
<a href="javascript:refreshLibraryList({by:'title',dir:'desc'});" title="Hier klicken um alle Filme alphabetisch anzuordnen">Titel (Absteigend)</a>
<a href="javascript:refreshLibraryList({by:'year',dir:'asc'});" title="Hier klicken um alle Filme nach Jahr zu anzuordnen">Jahr (Aufsteigend)</a>
<a href="javascript:refreshLibraryList({by:'year',dir:'desc'});" title="Hier klicken um alle Filme nach Jahr zu anzuordnen">Jahr (Absteigend)</a>
<ul>
	<?php foreach ($Items as $Item) { ?>
	<li>
		<a href="<? echo $Item['WatchUrl'] ?>" title="Hier klicken um <?php echo $Item['Title']; ?> abzuspielen"><?php echo $Item['Title']; ?></a>
	</li>	
	<?php } ?>
</ul>