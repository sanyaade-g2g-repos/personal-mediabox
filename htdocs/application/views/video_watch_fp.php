<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Watch <?php echo $Title;?></title>
<script type="text/javascript" src="http://www.sandbox.ch/ci/assets/js/jquery-1.6.2.js"></script>
<script type="text/javascript" src="http://www.sandbox.ch/ci/assets/js/flowplayer-3.2.7/flowplayer/flowplayer-3.2.6.min.js"></script>
 
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

</style>
</head>
<body>
<h1><?php echo $Title;?></h1>
<div class="video-box">
	<a href="http://www.sandbox.ch<?php echo $FileUrl;?>" style="display:block;width:520px;height:330px" id="player"></a> 
	<!-- this will install flowplayer inside previous A- tag. -->
	<script>
		flowplayer("player", "http://www.sandbox.ch/ci/assets/js/flowplayer-3.2.7/flowplayer/flowplayer-3.2.7.swf");
	</script>
</div>
<div class="mediainfo" style="border-top: 1px solid #D0D0D0; padding: 20px 0;">
	<?php echo $Mediainfo;?>
</div>
</body>
</html>