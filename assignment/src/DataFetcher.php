<?php

/**
 * Class DataFetcher
 *
 * Gets the original data from the dataLocation url
 * Must be in expected format
 */
class DataFetcher {

	/**
	 * @var string
	 */
	private $dataLocation;

	/**
	 * @param string $dataLocation
	 */
	public function __construct( $dataLocation ) {
		$this->dataLocation = $dataLocation;
	}

	/**
	 * @param string $dataLocation URL to location of data in expected format
	 *
	 * @return string
	 */
	private function getRawData( $dataLocation ) {
		return file_get_contents( $dataLocation );
	}

	/**
	 * @param string $rawData html string
	 *
	 * @return DOMDocument
	 */
	private function getRawDOM( $rawData ) {
		$DOM = new DOMDocument();
		$DOM->loadHTML( $rawData );
		return $DOM;
	}

	/**
	 * @param DOMNodeList $tableRows
	 *
	 * @return array
	 */
	private function getDataFromTableRows( DOMNodeList $tableRows ) {
		$extractedData = array();
		$headings = array();
		foreach ($tableRows as $rowNumber => $row)
		{
			foreach ( $row->childNodes as $key => $node) {
				/** @var DOMElement $node */
				$nodeValue = trim( $node->nodeValue );
				if( !empty( $nodeValue ) ) {
					if( $rowNumber === 0 ) {
						$headings[$key] = strtolower( $nodeValue );
					} else {
						$extractedData[$rowNumber][$headings[$key]] = '';
						foreach ($node->childNodes as $child) {
							$extractedData[$rowNumber][$headings[$key]] .= $child->ownerDocument->saveXML( $child );
						}
					}
				}
			}
		}
		return $extractedData;
	}

	/**
	 * @param array $rowData
	 *
	 * @return array
	 */
	private function getRefinedDataFromRowData( $rowData ) {
		$data = array();
		foreach( $rowData as $dataKey => $dataItem ) {
			$data[$dataKey] = array();

			// year
			$reigns = array();
			foreach( explode( '<br/>', $dataItem['year'] ) as $reign ) {
				$reign = trim( $reign );
				$reignSplit = explode( ' ', $reign );
				if( count( $reignSplit ) > 1  ) {
					$reignType = trim( strtolower( $reignSplit[1] ), "()" );
				} else {
					$reignType = null;
				}
				// The below splits 2 different dashes (although they look the same)
				$reignDateSplit = preg_split( '/(-|–)/', $reignSplit[0] );
				if( count( $reignDateSplit ) > 1 ) {
					$end = $reignDateSplit[1];
					// If we end date is only 2 digit get the century from the start year
					if( strlen( $end ) === 2 ) {
						$end = substr( $reignDateSplit[0], 0, 2 ) . $end;
					}
				} else {
					// If no explicit end date then use the start date! (1 year reign)
					$end = $reignDateSplit[0];
				}
				$reigns[] = new Reign(
					null,
					trim( $reignDateSplit[0] ),
					trim( $end ),
					$reignType
				);
			}

			// location
			$locations = array();
			foreach( explode( '<br/>', trim( $dataItem['country'] ) ) as $locationHtml ) {
				$locationArray = array();
				$locationDOM = new DOMDocument;
				$locationDOM->loadHTML( $locationHtml );
				$linkElements = $locationDOM->getElementsByTagName( 'a' );

				if( $locationDOM->getElementsByTagName( 'a' )->length > 1 ) {
					$historical = utf8_decode( trim( $linkElements->item( 1 )->nodeValue ) );
					//TODO allow there to be no href here
					$historicalLink = utf8_decode(trim( $linkElements->item( 1 )->attributes->getNamedItem('href')->nodeValue ) );
				} else {
					$historical = null;
					$historicalLink = null;
				}
				$data[$dataKey]['locations'][]  = $locationArray;

				$locations[] = new Location(
					null,
					utf8_decode( trim( $linkElements->item( 0 )->nodeValue ) ),
					//TODO allow there to be no href here
					utf8_decode( trim( $linkElements->item( 0 )->attributes->getNamedItem('href')->nodeValue ) ),
					//TODO allow there to be no flag img
					utf8_decode( trim( $locationDOM->getElementsByTagName( 'img' )->item( 0 )->attributes->getNamedItem('src')->nodeValue ) ),
					$historical,
					$historicalLink
				);
			}

			// name
			$nameDOM = new DOMDocument;
			$nameDOM->loadHTML( $dataItem['name'] );
			$champion = new Champion(
				null,
				utf8_decode( trim( $nameDOM->getElementsByTagName( 'a' )->item( 0 )->nodeValue ) ),
				$locations,
				$reigns,
				utf8_decode( trim( $nameDOM->getElementsByTagName( 'a' )->item( 0 )->attributes->getNamedItem('href')->nodeValue ) )
			);

			$data[$dataKey] = $champion;
		}
		return $data;
	}

	/**
	 * @return array of data
	 */
	public function fetch() {
		$DOM = $this->getRawDOM( $this->getRawData( $this->dataLocation ) );
		$tableRows = $DOM->getElementsByTagName( 'tr' );
		$rowData = $this->getDataFromTableRows( $tableRows );
		$data = $this->getRefinedDataFromRowData( $rowData );
		return $data;
	}

} 