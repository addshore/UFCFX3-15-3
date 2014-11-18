<?php

class XmlGenerator implements OutputGenerator {

	/**
	 * @param Champion[] $champions
	 * @param ExtraChampionData[] $wikidataItems with keys pointing to the enwikilink
	 *                            May not include all champions that are in $champions
	 *
	 * @return string
	 */
	public function generate( array $champions, array $wikidataItems = array() ) {
		$xml = new SimpleXMLElement( '<chesschampions/>' );
		foreach( $champions as $champion ) {
			$championXml = $xml->addChild( 'champion' );
			$championXml->addAttribute( 'id', $champion->getId() );
			$championXml->addAttribute( 'name', $champion->getName() );
			$championXml->addAttribute( 'enwikilink', $champion->getEnwikilink() );

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

} 