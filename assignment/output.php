<?php

require_once __DIR__ . '/src/autoload.php';

$format = null;
if( array_key_exists( 'format', $_GET ) ) {
	$format = $_GET['format'];
}

$dbInteractor = new DatabaseInteractor();
$wikidataInteractor = new WikidataInteractor();

$champions = $dbInteractor->getChampions();

switch ( $format ) {

	case 'xml':
		$generator = new XmlGenerator();
		header( 'Content-Type:text/xml' );
		echo $generator->generate(
			$dbInteractor->getChampions()
		);
		break;

	case 'html':
		$wikidataItems = $wikidataInteractor->getExtraData( $champions );
		$generator = new HtmlGenerator();
		echo $generator->generate(
			$champions,
			$wikidataItems
		);
		break;

	case 'rdfa':
		echo "TODO";
		break;

	case 'jsonld':
		echo "TODO";
		break;

	default:
		echo '<html><head></head><body>
		<h1>Assignment Output</h1>
		<p>Select a format:</p>
		<ul>
		<li><a href="?format=xml">xml</a></li>
		<li><a href="?format=html">html (with microdata)</a></li>
		<li><a href="?format=rdfa">html (with rdfa)</a></li>
		<li><a href="?format=jsonld">lsonld</a></li>
		</ul>
		</body></html>';
		break;
}