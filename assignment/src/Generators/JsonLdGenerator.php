<?php

require_once( __DIR__ . '/../../viewsource.php' );

class JsonLdGenerator implements OutputGenerator {

	/**
	 * @param Champion[] $champions
	 * @param ExtraChampionData[] $extraChampionData with keys pointing to the enwikilink
	 *                            May not include all champions that are in $champions
	 *
	 * @return string
	 */
	public function generate( array $champions, array $extraChampionData = array() ) {
		$outArray = array();

		foreach( $champions as $champion ) {
			$championArray = array(
				'@context' => 'http://schema.org/',
				'@type' => 'Person',
				'name' => $champion->getName(),
			);
			if( array_key_exists( $champion->getEnwikilink(), $extraChampionData ) ) {
				$extraData = $extraChampionData[$champion->getEnwikilink()];
				if( !is_null( $extraData->getImageLocation() ) ) {
					$championArray['image'] = $extraData->getImageLocation();
				}
				if( !is_null( $extraData->getDateOfBrith() ) ) {
					$championArray['birthDate'] = $extraData->getDateOfBrith();
				}
				if( !is_null( $extraData->getDateOfDeath() ) ) {
					$championArray['deathDate'] = $extraData->getDateOfDeath();
				}
				$sameAsArray = array();
				foreach( $extraData->getDataLinks() as $dataSource => $dataIdentifier ) {
					$sameAsArray[] = $this->getDataLinkPrefix( $dataSource ) . $dataIdentifier;
				}
				if( !empty( $sameAsArray ) ) {
					$championArray['sameAs'] = $sameAsArray;
				}
			}
			$outArray[] = $championArray;
		}

		return json_encode( $outArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
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