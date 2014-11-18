<?php

class JsonLdGenerator implements OutputGenerator {

	/**
	 * @param Champion[] $champions
	 * @param ExtraChampionData[] $wikidataItems with keys pointing to the enwikilink
	 *                            May not include all champions that are in $champions
	 *
	 * @return string
	 */
	public function generate( array $champions, array $wikidataItems = array() ) {
		//TODO use $wikidataItems
		$outArray = array();

		foreach( $champions as $champion ) {
			$outArray[] = array(
				'@context' => 'http://schema.org/',
				'@type' => 'Person',
				'name' => $champion->getName(),
			);
		}

		return json_encode( $outArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	}

} 