<?php

// Load everything
require_once __DIR__ . '/src/autoload.php';

// Get the format we want
$format = null;
if( array_key_exists( 'format', $_GET ) ) {
	$format = strtolower( $_GET['format'] );
}

// Get the right generator for the format
switch ( $format ) {
	case 'xml':
		$generator = new XmlGenerator();
		header( 'Content-Type:text/xml' );
		break;
	case 'html':
		$generator = new HtmlGenerator();
		break;
	case 'microdata':
	case 'micro':
		$generator = new HtmlGenerator( true );
		break;
	case 'rdfa':
		$generator = new HtmlGenerator( false, true );
		break;
	case 'jsonld':
	case 'json-ld':
		$generator = new JsonLdGenerator();
		break;
}

// Use a generator if we have one, if not output the default stuff
if( isset( $generator ) ) {
	$dbInteractor = new DatabaseInteractor();
	$wikidataInteractor = new WikidataInteractor();

	$champions = $dbInteractor->getChampions();
	echo $generator->generate(
		$champions,
		$wikidataInteractor->getExtraData( $champions )
	);
} else {
	echo '<html><head></head><body>
		<h1>Assignment Output</h1>
		<p>Select a format:</p>
		<ul>
		<li><a href="?format=xml">XML</a></li>
		<li><a href="?format=html">HTML</a></li>
		<li><a href="?format=microdata">HTML (with MicroData)</a></li>
		<li><a href="?format=rdfa">HTML (with RDFa)</a></li>
		<li><a href="?format=jsonld">JSON-ld</a></li>
		</ul>
		</body></html>';
}