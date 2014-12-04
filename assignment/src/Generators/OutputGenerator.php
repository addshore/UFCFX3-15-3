<?php

require_once( __DIR__ . '/../../viewsource.php' );

/**
 * Interface OutputGenerator for page output
 */
interface OutputGenerator {

	/**
	 * @param Champion[] $champions
	 * @param ExtraChampionData[] $extraChampionData with keys pointing to the enwikilink
	 *                            May not include all champions that are in $champions
	 *
	 * @return string
	 */
	public function generate( array $champions, array $extraChampionData = array() ) ;


} 