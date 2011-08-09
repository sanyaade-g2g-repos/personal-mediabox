<select id="<? echo $SortControlId; ?>">
	<option value="">Sortierung w&auml;hlen...</option>
	<option value="{&quot;groupby&quot;:&quot;title&quot;}" onclick="javascript:applySorting($('#<? echo $SortControlId; ?>'));">Titel</option>
	<option value="{&quot;groupby&quot;:&quot;year&quot;}" onclick="javascript:applySorting($('#<? echo $SortControlId; ?>'));">Jahr</option>
	<option value="{&quot;groupby&quot;:&quot;artist&quot;}" onclick="javascript:applySorting($('#<? echo $SortControlId; ?>'));">Schauspieler</option>
	<option value="{&quot;groupby&quot;:&quot;tags&quot;}" onclick="javascript:applySorting($('#<? echo $SortControlId; ?>'));">Tags</option>
</select>
<h3><? echo $Title; ?></h3>
<? if (count($Groups)>0) { ?>
<? foreach ($Groups as $GroupName=>$Items) { ?>
<div>
	<a name="<? echo $GroupName ?>"><? echo $GroupName ?></a>
	<? if (count($Items)>0) { ?>
	<ul>
		<? foreach ($Items as $Item) { ?>
		<li>
			<a href="<? echo $Item['WatchUrl'] ?>" title="<?php echo $Item['Description']; ?>"><?php echo $Item['Title']; ?></a>
		</li>	
		<? } /*foreach*/ ?>
	</ul>
	<? } /*if*/ ?>
</div>
<? } /*foreach*/ ?>
<? } /*if*/ ?>