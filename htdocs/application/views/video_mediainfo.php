<h3><? echo $Item['Title'];?></h3>
<style type="text/css">
.mediainfotable table {
	padding: 0 20px 0 20px;
	font-size: 10px;
}
.mediainfotable table.border {
	border-left: 1px solid;
}
#mediainfo-results {
	padding: 20px;
}
#mediainfo-results .results h3 {
	margin: 0 0 5px 0;
}
#mediainfo-results .results td {
	vertical-align: top;
}
#mediainfo-results .results td.label {
	white-space: nowrap;
	padding-right: 20px;
}
#mediainfo-results .results td.value {
	width: 180px;
	display: block;
}
</style>
<div>
<table class="mediainfotable">
	<tr><td valign="top">
		<table>
			<tr><th colspan="2">General Information</th></tr>
		<? foreach($General as $Name => $Value) { 
		   if ($Name == 'Complete name') continue; ?>
			<tr><td class="label"><? echo $Name; ?></td><td class="value"><? echo $Value; ?></td></tr>
		<? } ?>
		<? foreach($Item as $Name => $Value) { 
		   if ($Name == 'FilePath') continue;
		   if ($Name == 'WatchUrl') continue;
		   if ($Name == 'FileUrl') continue;
		   if ($Name == 'Index') continue;
		   if ($Name == '_xpath_') continue;
		   if ($Name == 'Description') continue; ?>
			<tr><td class="label"><? echo $Name; ?></td><td class="value"><? echo $Value; ?></td></tr>
		<? } ?>
		</table>
	</td><td valign="top">
		<table class="border">
			<tr><th colspan="2">Video Codec Information</th></tr>
		<? foreach($Video as $Name => $Value) { ?>
			<tr><td class="label"><? echo $Name; ?></td><td class="value"><? echo $Value; ?></td></tr>
		<? } ?>			
		</table>
	</td><td valign="top">
		<table class="border">
			<tr><th colspan="2">Audio Codec Information</th></tr>
		<? foreach($Audio as $Name => $Value) { ?>
			<tr><td class="label"><? echo $Name; ?></td><td class="value"><? echo $Value; ?></td></tr>
		<? } ?>
		</table>
	</td></tr>
</table>
</div>
