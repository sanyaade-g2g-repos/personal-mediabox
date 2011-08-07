<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Welcome to CodeIgniter</title>
<script type="text/javascript" src="/assets/js/jquery-1.6.2.js"></script>
<link rel="stylesheet" type="text/css" href="/assets/css/vg.css" />
<script type="text/javascript">
var search = function() {
	$.ajax({
		url: "/index.php/video/results"
		,type: 'post'
		,data: {
			k: $('#keyword').val()
		}
		,context: $('#results')
		,success: function(data, textStatus, jqXHR){
			$(this).html(data);
		}
	});
}
var refreshLibraryList = function(options) {
	options = options || {
		order: 'abc'
	};
	$.ajax({
		url: "/index.php/library/listlibrary"
		,type: 'post'
		,data: options
		,context: $('#list')
		,success: function(data, textStatus, jqXHR) {
			$(this).html(data);
		}
	});
}
$(function(){
	$('#list').load('/index.php/library/listlibrary');
});
</script>
</head>
<body>
<div style="position:absolute; margin-left:300px;">
<?php echo $item_count; ?> items in the library
<a href="/index.php/services/import" title="Neue Filme importieren">Filme Importieren</a> 
</div>
<h1>Video Gallery</h1>
<table><tr><td width="300px" valign="top">
<div id="list">
	
</div>
</td><td valign="top">
<div id="search">
<p>Videogallery durchsuchen</p>
<form name="seachform" id="searchform" action="" method="post" onsubmit="javascript:search();return false;">
	<input type="text" id="keyword" name="keyword" value="" />
	<input type="button" class="search-button" onclick="javascript:search();" />
</form>
</div>
<div id="results">

</div>
</td></tr></table>

</body>
</html>