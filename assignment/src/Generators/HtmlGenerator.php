<?php

class HtmlGenerator implements OutputGenerator {

	/**
	 * @var bool
	 */
	private $microdata;

	/**
	 * @var bool
	 */
	private $rdfa;

	/**
	 * @param bool $microdata should the html include microdata?
	 * @param bool $rdfa should the html include rdfa stuff?
	 */
	public function __construct( $microdata = false, $rdfa = false ) {
		if( !is_bool( $microdata ) || !is_bool( $rdfa ) ) {
			throw new InvalidArgumentException( __CLASS__ . ' $microdata and $rdfa must be bools' );
		}
		$this->microdata = $microdata;
		$this->rdfa = $rdfa;
	}

	/**
	 * @param Champion[] $champions
	 * @param ExtraChampionData[] $wikidataItems with keys pointing to the enwikilink
	 *                            May not include all champions that are in $champions
	 *
	 * @return string
	 */
	public function generate( array $champions, array $wikidataItems = array() ) {
		$dom = new DOMDocument();
		$dom->formatOutput = true;

		$this->appendHtmlToDom( $dom, $champions, $wikidataItems );

		return '<!DOCTYPE html>' . "\n" . $dom->saveHTML();
	}

	/**
	 * Append HTML to the DOM
	 */
	private function appendHtmlToDom( DOMDocument $dom, array $champions, array $wikidataItems ) {
		$html = $dom->appendChild( $dom->createElement( 'html' ) );
		/** @var DOMElement $html */
		$html->setAttribute( 'lang', 'en' );

		$this->appendHeadToNode( $dom, $html );
		$this->appendBodyToNode( $dom, $html, $champions, $wikidataItems );
	}

	/**
	 * Append HEAD to HTML
	 */
	private function appendHeadToNode( DOMDocument $dom, DOMNode $html ) {
		$head = $html->appendChild( $dom->createElement( 'head' ) );

		$charset = $head->appendChild( $dom->createElement( 'meta' ) );
		/** @var DOMElement $charset */
		$charset->setAttribute( 'charset',  'utf-8' );

		$head->appendChild( $dom->createElement( 'title', 'Chess World Champions' ) );
		$head->appendChild( $dom->createElement( 'style', 'table {
    border-collapse: collapse;
}

table, td, th {
    border: 1px solid grey;
}
') );
	}

	/**
	 * Append BODY to HTML
	 *
	 * @param DOMDocument $dom
	 * @param DOMNode $html
	 * @param Champion[] $champions
	 * @param ExtraChampionData[] $extraChampionData where keys are the enwikilink url for the champion
	 */
	private function appendBodyToNode( DOMDocument $dom, DOMNode $html, $champions, $extraChampionData ) {
		$body = $html->appendChild( $dom->createElement( 'body' ) );
		$body->appendChild( $dom->createElement( 'h3', 'Chess World Champions (1886–2013)' ) );

		$table = $body->appendChild( $dom->createElement( 'table' ) );
		$tr1 = $table->appendChild( $dom->createElement( 'tr' ) );
		$tr1->appendChild( $dom->createElement( 'th', 'Image' ) );
		$tr1->appendChild( $dom->createElement( 'th', 'Name' ) );
		$tr1->appendChild( $dom->createElement( 'th', 'Lived in' ) );
		$tr1->appendChild( $dom->createElement( 'th', 'Dob' ) );
		$tr1->appendChild( $dom->createElement( 'th', 'Dod' ) );
		$tr1->appendChild( $dom->createElement( 'th', 'Reigns' ) );
		$tr1->appendChild( $dom->createElement( 'th', 'Data Links' ) );

		foreach( $champions as $champion ) {
			if( array_key_exists( $champion->getEnwikilink(), $extraChampionData ) ) {
				$extraData = $extraChampionData[ $champion->getEnwikilink() ];
			} else {
				$extraData = null;
			}
			$this->appendChampionRowToNode( $dom, $table, $champion, $extraData );
		}
	}

	/**
	 * Append a ROW for a TABLE
	 *
	 * @param DOMDocument $dom
	 * @param DOMNode $table
	 * @param Champion $champion
	 * @param ExtraChampionData|null $extraData
	 */
	private function appendChampionRowToNode( DOMDocument $dom, DOMNode $table, $champion, $extraData = null ) {
		/** @var DOMElement $tr */
		$tr = $table->appendChild( $dom->createElement( 'tr' ) );

		if( $this->microdata ) {
			$tr->setAttribute( 'itemscope', '' );
			$tr->setAttribute( 'itemtype', 'http://schema.org/Person' );
		}
		if( $this->rdfa ) {
			$tr->setAttribute( 'vocab', 'http://schema.org/' );
			$tr->setAttribute( 'typeof', 'Person' );
		}

		$this->appendImageColToNode( $dom, $tr, $champion, $extraData );
		$this->appendNameColToNode( $dom, $tr, $champion, $extraData );
		$this->appendLivedInColToNode( $dom, $tr, $champion );
		$this->appendDobColToNode( $dom, $tr, $extraData );
		$this->appendDodColToNode( $dom, $tr, $extraData );
		$this->appendReignsColToNode( $dom, $tr, $champion );
		$this->appendDataLinksColToNode( $dom, $tr, $extraData->getDataLinks() );
	}

	/**
	 * Append some ROW DATA to a ROW
	 *
	 * @param DOMDocument $dom
	 * @param DOMNode $tr
	 * @param Champion $champion
	 * @param null|ExtraChampionData $extraData
	 */
	private function appendImageColToNode( DOMDocument $dom, DOMNode $tr, $champion, $extraData ) {
		$imageCol = $tr->appendChild( $dom->createElement( 'td' ) );

		if( !is_null( $extraData ) ) {
			$commonsImageName = $extraData->getImageLocation();
			if( !is_null( $commonsImageName ) ) {
				$commonsImageNameHash = md5( $commonsImageName );
				$imageLocation = '//upload.wikimedia.org/wikipedia/commons/' .
					substr( $commonsImageNameHash, 0, 1 ) . '/' .
					substr( $commonsImageNameHash, 0, 2 ) . '/' .
					$commonsImageName;
				/** @var DOMElement $imageLink */
				$imageLink = $imageCol->appendChild( $dom->createElement( 'a' ) );
				$imageLink->setAttribute( 'href', $imageLocation );
				/** @var DOMElement $image */
				$image = $imageLink->appendChild( $dom->createElement( 'img' ) );
				$image->setAttribute( 'src', htmlentities( $imageLocation ) );
				$image->setAttribute( 'width', 50 );
				$image->setAttribute( 'height', 50 );
				$image->setAttribute( 'alt', 'Image of ' . $champion->getName() );
				if( $this->microdata ) {
					$image->setAttribute( 'itemprop', 'image' );
				}
				if( $this->rdfa ) {
					$image->setAttribute( 'property', 'image' );
				}
			}
		}
	}

	/**
	 * Append some ROW DATA to a ROW
	 *
	 * @param DOMDocument $dom
	 * @param DOMNode $tr
	 * @param Champion $champion
	 * @param null|ExtraChampionData $extraData
	 */
	private function appendNameColToNode( DOMDocument $dom, DOMNode $tr, Champion $champion, $extraData ) {
		$name = $tr->appendChild( $dom->createElement( 'td' ) );

		if( !is_null( $champion->getEnwikilink() ) ) {
			/** @var DOMElement $nameLink */
			$nameLink = $name->appendChild( $dom->createElement( 'a', $champion->getName() ) );
			$nameLink->setAttribute( 'href', $champion->getEnwikilink() );
			$nameLink->setAttribute( 'title', $champion->getName() );
			if( $this->microdata ) {
				$nameLink->setAttribute( 'itemprop', 'name' );
			}
			if( $this->rdfa ) {
				$nameLink->setAttribute( 'property', 'name' );
			}
		} else {
			$name->appendChild( $dom->createTextNode( $champion->getName() ) );
		}

	}

	/**
	 * @param DOMDocument $dom
	 * @param DOMNode $tr
	 * @param ExtraChampionData $extraData
	 */
	private function appendDobColToNode( DOMDocument $dom, DOMNode $tr, $extraData ) {
		$dobCol = $tr->appendChild( $dom->createElement( 'td' ) );
		if( !is_null( $extraData ) ) {
			$dob = $extraData->getDateOfBrith();
			//If we have both a dob and dod
			if( $dob !== null ) {
				$dob = date_parse( ltrim( $dob, '+0' ) );
				/** @var DOMElement $dobSpan */
				$dobSpan = $dobCol->appendChild( $dom->createElement( 'span', $dob['day'] . '/' . $dob['month'] . '/' . $dob['year'] ) );
				if( $this->microdata ) {
					$dobSpan->setAttribute( 'itemprop', 'birthDate' );
				}
				if( $this->rdfa ) {
					$dobSpan->setAttribute( 'property', 'birthDate' );
				}
			}
		}
	}

	/**
	 * @param DOMDocument $dom
	 * @param DOMNode $tr
	 * @param ExtraChampionData $extraData
	 */
	private function appendDodColToNode( DOMDocument $dom, DOMNode $tr, $extraData ) {
		$dodCol = $tr->appendChild( $dom->createElement( 'td' ) );
		if( !is_null( $extraData ) ) {
			$dod = $extraData->getDateOfDeath();
			//If we have both a dob and dod
			if( $dod !== null ) {
				$dod = date_parse( ltrim( $dod, '+0' ) );
				/** @var DOMElement $dodSpan */
				$dodSpan = $dodCol->appendChild( $dom->createElement( 'span', $dod['day'] . '/' . $dod['month'] . '/' . $dod['year'] ) );
				if( $this->microdata ) {
					$dodSpan->setAttribute( 'itemprop', 'deathDate' );
				}
				if( $this->rdfa ) {
					$dodSpan->setAttribute( 'property', 'deathDate' );
				}
			}
		}

	}

	/**
	 * Append some ROW DATA to a ROW
	 *
	 * @param DOMDocument $dom
	 * @param DOMNode $tr
	 * @param Champion $champion
	 */
	private function appendReignsColToNode( DOMDocument $dom, DOMNode $tr, Champion $champion ) {
		$year = $tr->appendChild( $dom->createElement( 'td' ) );
		foreach( $champion->getReigns() as $reign ) {
			$year->appendChild( $dom->createTextNode( $reign->getStartYear() . '-' . $reign->getEndYear() ) );
			if( !is_null( $reign->getType() ) ) {
				$year->appendChild( $dom->createElement( 'small', ' (' . $reign->getType() . ')' ) );
			}
			$year->appendChild( $dom->createElement( 'br' ) );
		}
		//Remove the last br tag
		if( count( $champion->getReigns() ) > 0 ) {
			$year->removeChild( $year->lastChild );
		}
	}

	/**
	 * Append some ROW DATA to a ROW
	 *
	 * @param DOMDocument $dom
	 * @param DOMNode $tr
	 * @param Champion $champion
	 */
	private function appendLivedInColToNode( DOMDocument $dom, DOMNode $tr, Champion $champion ) {
		$country = $tr->appendChild( $dom->createElement( 'td' ) );
		foreach( $champion->getLocations() as $location ) {
			if( !is_null( $location->getFlagUrl() ) ) {
				/** @var DOMElement $flagImg */
				$flagImg = $country->appendChild( $dom->createElement( 'img' ) );
				$flagImg->setAttribute( 'alt', $location->getCountry() . ' flag' );
				$flagImg->setAttribute( 'src', $location->getFlagUrl() );
				$flagImg->setAttribute( 'width', 30 );
			} else {
				die();//TODO do something if there isnt a flag...
			}
			$country->appendChild( $dom->createTextNode( ' ' ) );

			if( !is_null( $location->getCountryLink() ) ) {
				/** @var DOMElement $countryLink */
				$countryLink = $country->appendChild( $dom->createElement( 'a', $location->getCountry() ) );
				$countryLink->setAttribute( 'href', $location->getCountryLink() );
				$countryLink->setAttribute( 'title', $location->getCountry() );
			} else {
				$country->appendChild( $dom->createTextNode( $location->getCountry() ) );
			}
			if( !is_null( $location->getHistorical() ) ) {
				$historicalSmall = $country->appendChild( $dom->createElement( 'small' ) );
				$historicalSmall->appendChild( $dom->createTextNode( ' (' ) );
				if( !is_null( $location->getHistoricalLink() ) ) {
					/** @var DOMElement $historicalLink */
					$historicalLink = $historicalSmall->appendChild( $dom->createElement( 'a', $location->getHistorical() ) );
					$historicalLink->setAttribute( 'href', $location->getHistoricalLink() );
					$historicalLink->setAttribute( 'title', $location->getHistorical() );
				} else {
					$historicalSmall->appendChild( $dom->createTextNode( $location->getHistorical() ) );
				}
				$historicalSmall->appendChild( $dom->createTextNode( ')' ) );
			}
			$country->appendChild( $dom->createElement( 'br' ) );
		}
	}

	/**
	 * @param DOMDocument $dom
	 * @param DOMNode $tr
	 * @param array $dataLinks
	 */
	private function appendDataLinksColToNode( DOMDocument $dom, DOMNode $tr, array $dataLinks ) {
		$dataLinksCol = $tr->appendChild( $dom->createElement( 'td' ) );
		foreach( $dataLinks as $dataSource => $dataIdentifier ) {
			/** @var DOMElement $dataLink */
			$dataLink = $dataLinksCol->appendChild( $dom->createElement( 'a', strtoupper( $dataSource ) ) );
			$dataLink->setAttribute( 'href', $this->getDataLinkPrefix( $dataSource ) . $dataIdentifier );
			$dataLink->setAttribute( 'title', $dataSource . ' ' . $dataIdentifier );
			if( $this->microdata ) {
				$dataLink->setAttribute( 'itemprop', 'sameAs' );
			}
			if( $this->rdfa ) {
				$dataLink->setAttribute( 'property', 'sameAs' );
			}
			$dataLinksCol->appendChild( $dom->createTextNode( ', ' ) );
		}
		//Remove the last comma
		if( count( $dataLinks ) > 0 ) {
			$dataLinksCol->removeChild( $dataLinksCol->lastChild );
		}
	}

	/**
	 * @param string $dataSource identifiers for the datasource
	 *
	 * @return string prefix url for the identifier
	 * @throws Exception if datasource identifier is not known here
	 */
	private function getDataLinkPrefix( $dataSource ) {
		switch ( $dataSource ) {
			case 'viaf':
				return '//viaf.org/viaf/';
			case 'isni':
				return '//http://isni.org/isni/';
			case 'gnd':
				return '//d-nb.info/gnd/';
			case 'lcnaf':
				return '//lccn.loc.gov/';
		}
		throw new Exception( 'Unknown DataLink DataSource' );
	}

} 