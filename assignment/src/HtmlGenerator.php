<?php

class HtmlGenerator {

	/**
	 * @param Champion[] $champions
	 *
	 * @return string
	 */
	public function generate( array $champions ) {
		$dom = new DOMDocument();

		$html = $dom->appendChild( $dom->createElement( 'html' ) );
		$html->setAttribute( 'lang', 'en' );

		$head = $html->appendChild( $dom->createElement( 'head' ) );

		$charset = $head->appendChild( $dom->createElement( 'meta' ) );
		$charset->setAttribute( 'charset',  'utf-8' );

		$head->appendChild( $dom->createElement( 'title', 'Chess World Champions') );
		$head->appendChild( $dom->createElement( 'style', 'table {
    border-collapse: collapse;
}

table, td, th {
    border: 1px solid grey;
}
') );

		$body = $html->appendChild( $dom->createElement( 'body' ) );
		$body->appendChild( $dom->createElement( 'h3', 'Chess World Champions (1886–2013)' ) );

		$table = $body->appendChild( $dom->createElement( 'table' ) );
		$tr1 = $table->appendChild( $dom->createElement( 'tr' ) );
		$tr1->appendChild( $dom->createElement( 'th', 'Name' ) );
		$tr1->appendChild( $dom->createElement( 'th', 'Year' ) );
		$tr1->appendChild( $dom->createElement( 'th', 'Country' ) );

		foreach( $champions as $champion ) {
			$tr = $table->appendChild( $dom->createElement( 'tr' ) );

			$name = $tr->appendChild( $dom->createElement( 'td' ) );
			if( !is_null( $champion->getEnwikilink() ) ) {
				$nameLink = $name->appendChild( $dom->createElement( 'a', $champion->getName() ) );
				$nameLink->setAttribute( 'href', $champion->getEnwikilink() );
				$nameLink->setAttribute( 'title', $champion->getName() );
			} else {
				$name->appendChild( $dom->createTextNode( $champion->getName() ) );
			}


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

			$country = $tr->appendChild( $dom->createElement( 'td' ) );
			foreach( $champion->getLocations() as $location ) {
				if( !is_null( $location->getFlagUrl() ) ) {
					$flagImg = $country->appendChild( $dom->createElement( 'img' ) );
					$flagImg->setAttribute( 'alt', $location->getCountry() . ' flag' );
					$flagImg->setAttribute( 'src', $location->getFlagUrl() );
					$flagImg->setAttribute( 'width', 30 );
				} else {
					die();//TODO do something if there isnt a flag...
				}
				$country->appendChild( $dom->createTextNode( ' ' ) );

				if( !is_null( $location->getCountryLink() ) ) {
					$countryLink = $country->appendChild( $dom->createElement( 'a', $location->getCountry() ) );
					$countryLink->setAttribute( 'href', $location->getCountryLink() );
					$countryLink->setAttribute( 'title', $location->getCountry() );
				} else {
					$country->appendChild( $dom->createTextNode( $location->getCountry() ) );
				}
				if( !is_null( $location->getHistorical() ) ) {
					$country->appendChild( $dom->createTextNode( '(' ) );
					if( !is_null( $location->getHistoricalLink() ) ) {
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

		$dom->formatOutput = true;
		return $dom->saveHTML();
	}

} 