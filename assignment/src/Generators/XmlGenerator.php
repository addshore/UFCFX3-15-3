<?php

require_once( __DIR__ . '/../../viewsource.php' );

class XmlGenerator implements OutputGenerator {

	/**
	 * @param Champion[] $champions
	 * @param ExtraChampionData[] $extraChampionData with keys pointing to the enwikilink
	 *                            May not include all champions that are in $champions
	 *
	 * @return string
	 */
	public function generate( array $champions, array $extraChampionData = array() ) {
		$xml = new SimpleXMLElement( '<chesschampions/>' );
		foreach( $champions as $champion ) {
			$championXml = $xml->addChild( 'champion' );
			$championXml->addAttribute( 'id', $champion->getId() );
			$championXml->addAttribute( 'name', $champion->getName() );
			$championXml->addAttribute( 'enwikilink', $champion->getEnwikilink() );

			if( array_key_exists( $champion->getEnwikilink(), $extraChampionData ) ) {
				$extraData = $extraChampionData[$champion->getEnwikilink()];
				if( !is_null( $extraData->getImageLocation() ) ) {
					$championXml->addAttribute( 'image', $extraData->getImageLocation() );
				}
				if( !is_null( $extraData->getDateOfBrith() ) ) {
					$championXml->addAttribute( 'birthDate', $extraData->getDateOfBrith() );
				}
				if( !is_null( $extraData->getDateOfDeath() ) ) {
					$championXml->addAttribute( 'deathDate', $extraData->getDateOfDeath() );
				}
				foreach( $extraData->getDataLinks() as $dataSource => $dataIdentifier ) {
					$sameAs = $championXml->addChild( 'sameAs' );
					$sameAs->addAttribute( 'uri', $this->getDataLinkPrefix( $dataSource ) . $dataIdentifier );
				}
			}

			foreach( $champion->getReigns() as $reign ) {
				$reignXml = $championXml->addChild( 'reign' );
				$reignXml->addAttribute( 'start', $reign->getStartYear() );
				$reignXml->addAttribute( 'end', $reign->getEndYear() );
				if( !is_null( $reign->getType() ) ) {
					$reignXml->addAttribute( 'type', $reign->getType() );
				}
			}

			foreach( $champion->getLocations() as $location ) {
				$locationXml = $championXml->addChild( 'location' );
				$locationXml->addAttribute( 'country', $location->getCountry() );
				if( !is_null( $location->getCountryLink() ) ) {
					$locationXml->addAttribute( 'country_link', $location->getCountryLink() );
				}
				if( !is_null( $location->getFlagUrl() ) ) {
					$locationXml->addAttribute( 'flag', $location->getFlagUrl() );
				}
				if( !is_null( $location->getHistorical() ) ) {
					$locationXml->addAttribute( 'historical', $location->getHistorical() );
				}
				if( !is_null( $location->getHistoricalLink() ) ) {
					$locationXml->addAttribute( 'historical_link', $location->getHistoricalLink() );
				}
			}
		}

		// Format the XML
		$dom = new DOMDocument;
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML( $xml->asXML() );
		$dom->formatOutput = TRUE;
		return $dom->saveXml();
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