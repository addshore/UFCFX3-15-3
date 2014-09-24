<?php
/**
 * @link http://www.cems.uwe.ac.uk/~p-chatterjee/2014-15/modules/atwd1/workshop1.html
 */

$path = __DIR__ . DIRECTORY_SEPARATOR . 'quotes_12.txt';

if( !file_exists( $path ) ) {
	echo "Getting csv from web\n";
	$file = file_get_contents( 'http://www.cems.uwe.ac.uk/~p-chatterjee/2014-15/modules/atwd1/quotes_12.txt' );
	file_put_contents( $path, $file );
} else {
	echo "Getting local csv\n";
	$file = file_get_contents( $path );
}

echo "Processing csv file\n";
$lines = explode( "\n" ,$file );

$heading = array_shift( $lines );
$headings = explode( '|', $heading );

$data = array();
foreach( $lines as $lineNum => $line ) {
	$cols = explode( '|', $line );
	foreach( $cols as $colNum => $col ) {
		$data[$lineNum][$headings[$colNum]] = $col;
	}
}

echo "Saving JSON\n";
$json = json_encode( $data );
file_put_contents( __DIR__ . DIRECTORY_SEPARATOR . 'quotes_12.json', $json );

echo "Saving XML\n";
$xml = array_to_xml( $data );
file_put_contents( __DIR__ . DIRECTORY_SEPARATOR . 'quotes_12.xml', $xml->asXML() );

echo "Done";

/**
 * @param array $data
 * @param SimpleXMLElement|null $xml
 *
 * @return SimpleXMLElement
 */
function array_to_xml($data, &$xml = null) {
	if( is_null( $xml ) ) {
		$xml = new SimpleXMLElement( '<root/>' );
	}
	foreach( $data as $key => $value ) {
		if( is_array( $value ) ) {
			if( !is_numeric( $key ) ) {
				$subnode = $xml->addChild( "$key" );
				array_to_xml( $value, $subnode );
			}
			else{
				$subnode = $xml->addChild( "item$key" );
				array_to_xml( $value, $subnode );
			}
		}
		else {
			$xml->addChild( "$key", htmlspecialchars( "$value" ) );
		}
	}
	return $xml;
}