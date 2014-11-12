<?php

/**
 * Class ExtraChampionData represents extra data taken from Wikidata
 */
class ExtraChampionData {

	/**
	 * @var string|null
	 */
	private $imageLocation;

	/**
	 * @param null $imageLocation
	 */
	public function __construct( $imageLocation = null ) {
		$this->imageLocation = $imageLocation;
	}

	/**
	 * @return null|string
	 */
	public function getImageLocation() {
		return $this->imageLocation;
	}

} 