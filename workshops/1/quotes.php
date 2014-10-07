<?php
/**
 * Quotes Formatter stuff
 * @link http://www.cems.uwe.ac.uk/~p-chatterjee/2014-15/modules/atwd1/workshop1.html
 * @link http://www.cems.uwe.ac.uk/~p-chatterjee/2014-15/modules/atwd1/workshop2.html
 */

// Configure our source and our local copy
$csvLocation = 'http://www.cems.uwe.ac.uk/~p-chatterjee/php/quotes_13+img.txt';
$path = __DIR__ . DIRECTORY_SEPARATOR . 'quotes.csv';

// Get the CSV if we dont already have it
if( !file_exists( $path ) ) {
	$file = file_get_contents( $csvLocation );
	file_put_contents( $path, $file );
} else {
	$file = file_get_contents( $path );
}

// Create page output
$obj = new QuotesOutputer( $csvLocation );
if( array_key_exists( 'format', $_GET ) ) {
	$obj->output( $_GET['format'] );
} else {
	echo "Please select a format!\n<ul>";
	foreach( get_class_methods( $obj ) as $method ) {
		if( strpos( $method, 'output' ) === 0 && strlen( $method ) > 6 ) {
			$format = substr( $method, 6 );
			echo '<li><a href="' . $_SERVER['REQUEST_URI'] . "?format=$format\">$format</a></li>";
		}
	}
	echo '</ul>';
}

/**
 * Class to output quotes
 */
class QuotesOutputer {

	private $location;
	private $headings;
	private $data = array();

	/**
	 * @param string $quotesCsvLocation location to a csv file containing quotes
	 */
	public function __construct( $quotesCsvLocation ) {
		$this->location = $quotesCsvLocation;
		$this->processFile();
	}

	public function output( $format ) {
		$method = 'output' . ucfirst( $format );
		if( method_exists( $this, $method ) ) {
			$this->$method();
		} else {
			throw new Exception( 'Can\t output for format: ' . $format );
		}
	}

	private function getCsv() {
		return file_get_contents( $this->location );
	}

	private function processFile() {
		$file = $this->getCsv();

		$lines = explode( "\n", trim( $file, "\n\r" ) );

		$this->headings = array_replace(
			explode( '|', array_shift( $lines ) ),
			array( 'quote' ),
			array( 'text' )
		);

		foreach( $lines as $lineNum => $line ) {
			$cols = explode( '|', $line );
			foreach( $cols as $colNum => $col ) {
				$this->data[$lineNum][$this->headings[$colNum]] = $col;
			}
		}
	}

	/**
	 * Gets the Data from the csv File
	 */
	private function getData () {
		return $this->data;
	}

	public function outputCsv() {
		header( 'Content-Type:text/html' );
		echo $this->getCsv();
	}

	public function outputJson() {
		header( 'Content-Type:text/json' );
		echo json_encode( $this->getData() );
	}

	public function outputXml() {
		header( 'Content-Type:text/xml' );
		echo $this->quoteDataToXml(
			$this->getData(),
			new SimpleXMLElement( '<quotes/>' )
		)->asXML();

	}

	public function outputXsl() {
		$xslDoc = new DOMDocument();
		$xslDoc->load( __DIR__ . DIRECTORY_SEPARATOR . 'quotes.xsl' );
		$xmlDoc = new DOMDocument();
		$xmlDoc->load( __DIR__ . DIRECTORY_SEPARATOR . 'quotes.xml');
		$proc = new XSLTProcessor();
		$proc->importStylesheet($xslDoc);
		echo $proc->transformToXML($xmlDoc);
	}

	public function outputPhp() {
		echo serialize( $this->getData() );
	}

	/**
	 * @param array $quoteData to be added to the xml
	 * @param SimpleXMLElement $xml base xml
	 *
	 * @return SimpleXMLElement
	 */
	private function quoteDataToXml($quoteData, &$xml) {
		foreach( $quoteData as $key => $value ) {
			if( is_array( $value ) ) {
				if( !is_numeric( $key ) ) {
					$this->quoteDataToXml( $value, $xml->addChild( $key ) );
				} else {
					$this->quoteDataToXml( $value, $xml->addChild( 'quote' ) );
				}
			} else {
				$xml->addChild( $key, htmlspecialchars( $value ) );
			}
		}
		return $xml;
	}

}