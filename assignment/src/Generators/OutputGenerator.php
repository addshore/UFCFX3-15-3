<?php

/**
 * Interface OutputGenerator for page output
 */
interface OutputGenerator {

	/**
	 * @param Champion[] $champions
	 * @param ExtraChampionData[] $wikidataItems with keys pointing to the enwikilink
	 *                            May not include all champions that are in $champions
	 *
	 * @return string
	 */
	public function generate( array $champions, array $wikidataItems = array() ) ;


} 