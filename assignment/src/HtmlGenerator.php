<?php

use Wikibase\DataModel\Entity\Item;

class HtmlGenerator {

	/**
	 * @param Champion[] $champions
	 * @param Item[] $wikidataItems
	 *
	 * @return string
	 *
	 * @todo make sure of $wikidataItems
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

		$head->appendChild( $dom->createElement( 'title', 'Chess World Champions') );
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
		$tr1->appendChild( $dom->createElement( 'th', 'Name' ) );
		$tr1->appendChild( $dom->createElement( 'th', 'Year' ) );
		$tr1->appendChild( $dom->createElement( 'th', 'Country' ) );

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
		$tr->setAttribute( 'itemscope', '' );
		$tr->setAttribute( 'itemtype', 'http://schema.org/Person' );

		$this->appendNameColToNode( $dom, $tr, $champion, $extraData );
		$this->appendYearColToNode( $dom, $tr, $champion );
		$this->appendCountryColToNode( $dom, $tr, $champion );
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

		if( !is_null( $extraData ) ) {
			$commonsImageName = $extraData->getImageLocation();
			if( !is_null( $commonsImageName ) ) {
				$commonsImageNameHash = md5( $commonsImageName );
				$imageLocation = '//upload.wikimedia.org/wikipedia/commons/' .
					substr( $commonsImageNameHash, 0, 1 ) . '/' .
					substr( $commonsImageNameHash, 0, 2 ) . '/' .
					$commonsImageName;
				/** @var DOMElement $image */
				$image = $name->appendChild( $dom->createElement( 'img' ) );
				$image->setAttribute( 'src', htmlentities( $imageLocation ) );
				$image->setAttribute( 'width', 50 );
				$image->setAttribute( 'height', 50 );
				$image->setAttribute( 'itemprop', 'image' );
			}
		}

		if( !is_null( $champion->getEnwikilink() ) ) {
			/** @var DOMElement $nameLink */
			$nameLink = $name->appendChild( $dom->createElement( 'a', $champion->getName() ) );
			$nameLink->setAttribute( 'href', $champion->getEnwikilink() );
			$nameLink->setAttribute( 'title', $champion->getName() );
			$nameLink->setAttribute( 'itemprop', 'name' );
		} else {
			$name->appendChild( $dom->createTextNode( $champion->getName() ) );
		}

		if( !is_null( $extraData ) ) {
			$dob = $extraData->getDateOfBrith();
			$dod = $extraData->getDateOfDeath();
			//If we have both a dob and dod
			if( $dob !== null && $dod !== null ) {
				$dob = date_parse( $dob );
				$dod = date_parse( $dod );
				// Fix oddities parsing Wikidata date format
				if( strlen( $dob['year'] ) === 3 ) {
					$dob['year'] = $dob['year'] + 1000;
				}
				if( strlen( $dod['year'] ) === 3 ) {
					$dod['year'] = $dod['year'] + 1000;
				}

				$name->appendChild( $dom->createElement( 'br' ) );
				/** @var DOMElement $dobSpan */
				$dobSpan = $name->appendChild( $dom->createElement( 'span', $dob['day'] . '/' . $dob['month'] . '/' . $dob['year'] ) );
				$dobSpan->setAttribute( 'itemprop', 'birthDate' );
				$name->appendChild( $dom->createTextNode( ' - ' ) );
				/** @var DOMElement $dodSpan */
				$dodSpan = $name->appendChild( $dom->createElement( 'span', $dod['day'] . '/' . $dod['month'] . '/' . $dod['year'] ) );
				$dodSpan->setAttribute( 'itemprop', 'deathDate' );
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
	private function appendYearColToNode( DOMDocument $dom, DOMNode $tr, Champion $champion ) {
		$year = $tr->appendChild( $dom->createElement( 'td' ) );
		foreach( $champion->getReigns() as $reign ) {
			$year->appendChild( $dom->createTextNode( $reign->getStartYear() . '-' . $reign->getEndYear() ) );
			if( !is_null( $reign->getType() ) ) {
				$year->appendChild( $dom->createTextNode( ' (' . $reign->getType() . ')' ) );
			}
			$year->appendChild( $dom->createElement( 'br' ) );
		}
		//Remove the last br tag
		$year->removeChild( $year->lastChild );
	}

	/**
	 * Append some ROW DATA to a ROW
	 *
	 * @param DOMDocument $dom
	 * @param DOMNode $tr
	 * @param Champion $champion
	 */
	private function appendCountryColToNode( DOMDocument $dom, DOMNode $tr, Champion $champion ) {
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
				$country->appendChild( $dom->createTextNode( ' (' ) );
				if( !is_null( $location->getHistoricalLink() ) ) {
					/** @var DOMElement $historicalLink */
					$historicalLink = $country->appendChild( $dom->createElement( 'a', $location->getHistorical() ) );
					$historicalLink->setAttribute( 'href', $location->getHistoricalLink() );
					$historicalLink->setAttribute( 'title', $location->getHistorical() );
				} else {
					$country->appendChild( $dom->createTextNode( $location->getHistorical() ) );
				}
				$country->appendChild( $dom->createTextNode( ')' ) );
			}
			$country->appendChild( $dom->createElement( 'br' ) );
		}
	}

} 