<?php
require_once __DIR__.'/mvid_ai.php';
$mv_has_access = mvid_check_access($GLOBALS['mv-session-id']);
if (!$mv_has_access) {
	header('Location: ./login.php');
	die();
}

function file2attr($f) {
	$t = file_get_contents($f);
	$t = preg_replace('~\n+~s', '', $t);
	$t = preg_replace('~\s+~s', ' ', $t);
	$t = htmlspecialchars($t);
	return $t;
}

?>
<!DOCTYPE html>
<html lang="da" prefix="og: http://ogp.me/ns#">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="GrammarSoft ApS">
	<meta name="keywords" content="kommaforslag, kommakontrol, kommaretning, komma, retskrivning, grammatik">
	<meta name="description" content="Få kommaerne korrekt placeret med dette værktøj">
	<link rel="apple-touch-icon" sizes="76x76" href="favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="favicons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="favicons/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="favicons/manifest.json">
	<link rel="mask-icon" href="favicons/safari-pinned-tab.svg" color="#0a7fb9">
	<meta name="theme-color" content="#0a7fb9">
	<meta name="msapplication-config" content="favicons/browserconfig.xml">
	<meta property="og:locale" content="da_DK">
	<meta property="og:image" content="https://kommaer.dk/static/og_image.png">
	<title>Kommaforslag,</title>

	<script src="https://cdn.tinymce.com/4.3/tinymce.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	<link rel="stylesheet" href="static/komma.css?v=1698ba24">
	<script src="static/komma.js?v=b3b9a45a"></script>

<script>
var sid = <?=json_encode_num($GLOBALS['mv-session-id']);?>;
var mvid_to = null;
var mvid_errs = 0;
var mvid_first = true;

var mvid_ka = null;
function mvid_keepalive_at(msec) {
	if (mvid_ka) {
		clearTimeout(mvid_ka);
	}
	mvid_ka = setTimeout(mvid_keepalive, msec);
}

function mvid_error() {
	++mvid_errs;
	console.log(mvid_errs);
	if (mvid_first) {
		document.location = './login.php';
		return;
	}
	if (mvid_errs > 3) {
		alert('Kunne ikke få forbindelse til MV-Nordic MV-ID login efter mange forsøg - gem dit arbejde og genindlæs siden.');
		return;
	}
	mvid_keepalive_at(3000);
}

function mvid_keepalive() {
	if (mvid_to) {
		clearTimeout(mvid_to);
	}
	mvid_to = setTimeout(mvid_error, mvid_first ? 2500 : 5000);

	$.post('./callback.php', {a: 'keepalive', SessionID: sid}).done(function(rv) {
		console.log(rv);
		if (!rv.hmac) {
			document.location = './login.php';
			return;
		}
		g_access = rv;
		sid = rv.sessionid;
		clearTimeout(mvid_to);
		mvid_to = null;
		mvid_errs = 0;
		mvid_first = false;
		// Keepalive 5 minutes
		mvid_keepalive_at(5*60*1000);
	}).fail(mvid_error);
}
</script>
</head>
<body>

<div id="container">
<div id="headbar">
<div id="logo"><span class="icon icon-logo"></span><span>Kommaforslag</span></div>
</div>

<div id="content">
<div id="warning"></div>
<div id="editor">
<div id="ed-head">
<span class="button button-blue" id="btn-check" data-toggle="popover" data-trigger="focus" data-content="Din tekst er blevet tjekket for kommaer." data-placement="bottom auto"><span class="icon icon-check"></span><span class="text">Tjek komma</span></span>
<span class="button button-green" id="btn-correct-all" data-toggle="popover" data-trigger="focus" data-content="Din tekst er blevet tjekket for kommaer. Tjek evt. teksten igen." data-placement="bottom auto"><span class="icon icon-approve-all"></span><span class="text">Indsæt korrekte kommaer</span></span>
<span class="button button-red" id="btn-wrong-all"><span class="icon icon-delete-all"></span><span class="text">Fjern forkerte kommaer</span></span>
<span tabindex="0" class="button button-blue" id="btn-options" data-toggle="popover" data-trigger="manual" data-content="&lt;h4&gt;Færdighedsniveau&lt;/h4&gt;&lt;ul&gt; &lt;li&gt;&lt;label&gt;&lt;input type=&quot;radio&quot; name=&quot;opt_level&quot; value=&quot;1&quot;&gt; &lt;b&gt;Niveau 1&lt;/b&gt;: Du kan sætte tegn og har viden om punktum, spørgsmålstegn og udråbstegn.&lt;/label&gt;&lt;/li&gt; &lt;li&gt;&lt;label&gt;&lt;input type=&quot;radio&quot; name=&quot;opt_level&quot; value=&quot;2&quot;&gt; &lt;b&gt;Niveau 2&lt;/b&gt;: Du kan anvende afsnit og sætte komma og har viden om sætnings- og tekststruktur.&lt;/label&gt;&lt;/li&gt; &lt;li&gt;&lt;label&gt;&lt;input type=&quot;radio&quot; name=&quot;opt_level&quot; value=&quot;3&quot;&gt; &lt;b&gt;Niveau 3&lt;/b&gt;: Du kan fremstille tekster med korrekt grammatik, stavning, tegnsætning og layout.&lt;/label&gt;&lt;/li&gt;&lt;/ul&gt;&lt;h4&gt;Kommavisning&lt;/h4&gt;&lt;ul&gt; &lt;li&gt;&lt;label&gt;&lt;input type=&quot;checkbox&quot; id=&quot;opt_green&quot;&gt; Vis kun manglende kommaer&lt;/label&gt;&lt;/li&gt; &lt;li&gt;&lt;label&gt;&lt;input type=&quot;checkbox&quot; id=&quot;opt_maybe&quot;&gt; Fremhæv valgfrie kommaer&lt;/label&gt;&lt;/li&gt; &lt;li&gt;&lt;label&gt;&lt;input type=&quot;checkbox&quot; id=&quot;opt_color&quot;&gt; Farveblind/alternativ visning&lt;/label&gt;&lt;/li&gt;&lt;/ul&gt;&lt;button type=&quot;button&quot; class=&quot;btn btn-primary&quot; id=&quot;btn-options-close&quot;&gt;Luk&lt;/button&gt;" data-html="true" data-placement="bottom auto"><span class="icon icon-settings"></span><span class="text">Indstillinger</span></span>
<span tabindex="0" class="button button-blue button-small" id="btn-menu" data-toggle="popover" data-trigger="focus" data-content="&lt;a href=&quot;https://www.mv-nordic.com/dk/produkter/kommaforslag&quot; target=&quot;_blank&quot;&gt;&lt;span class=&quot;icon icon-info&quot;&gt;&lt;/span&gt;&lt;span&gt;Information&lt;/span&gt;&lt;/a&gt;&lt;br&gt;&lt;a href=&quot;https://www.mv-nordic.com/dk/produkter/kommaforslag/inspiration/inspirationsmateriale-kommaforslag&quot; target=&quot;_blank&quot;&gt;&lt;span class=&quot;icon icon-inspire&quot;&gt;&lt;/span&gt;&lt;span&gt;Inspiration til undervisningen&lt;/span&gt;&lt;/a&gt;&lt;br&gt;&lt;a href=&quot;#&quot; onclick=&quot;$('.mce-toolbar-grp').toggle(); onResize(); return false;&quot;&gt;&lt;span class=&quot;icon icon-format&quot;&gt;&lt;/span&gt;&lt;span&gt;Formatering af teksten&lt;/span&gt;&lt;/a&gt;&lt;br&gt;" data-html="true" data-placement="bottom auto"><span class="icon icon-menu"></span><span class="text">Mere</span></span>
</div>
<textarea placeholder="… indsæt eller skriv din tekst her …">
</textarea>
<div id="ed-foot">
<span class="button button-yellow" id="btn-close"><span class="icon icon-ignore"></span><span class="text">Ignorer resten</span></span>
<span class="button button-blue" id="btn-copy"><span class="icon icon-copy"></span><span class="text">Kopier teksten</span></span>
<span class="button button-red" id="btn-erase"><span class="icon icon-delete-all"></span><span class="text">Slet al tekst</span></span>
</div>
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

<div id="working"><img src="static/ellipsis.gif"> <span>arbejder, vent lidt</span> <img src="static/ellipsis.gif"></div>

<script>
  ga = null;
<?php
if (!empty($GLOBALS['-config']['GOOGLE_AID'])) {
	echo <<<XOUT
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', '{$GLOBALS['-config']['GOOGLE_AID']}', 'auto');
  ga('send', 'pageview');
XOUT;
}
?>

mvid_keepalive_at(1000);
</script>
</body>
</html>
