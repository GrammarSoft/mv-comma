<!DOCTYPE html>
<html lang="da">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Kommaforslag Login Error</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="static/komma.css?<?=filemtime(__DIR__.'/static/komma.css');?>">
</head>
<body>

<div id="container">
<div id="headbar">
<div id="logo"><span class="icon icon-logo"></span><span>Kommaforslag</span></div>
</div>

<div id="content">
<div id="login">
<b>
<?php
if (!empty($_REQUEST['error_message'])) {
	echo htmlspecialchars($_REQUEST['error_message']);
}
?>
</b>
<br>
<a href="./login.php">MV-ID Login</a>
</div>
</div>

<div id="footbar">
<div id="footer">
<a href="https://www.mv-nordic.com/dk/privatlivspolitik">Privatlivspolitik</a>
&nbsp; - &nbsp;
<a href="https://grammarsoft.com/">© 2016-2018 GrammarSoft ApS</a>
&nbsp; - &nbsp;
<a href="https://www.mv-nordic.com/">Distribueret af MV-Nordic</a>
</div>
</div>

</div>

</body>
</html>
