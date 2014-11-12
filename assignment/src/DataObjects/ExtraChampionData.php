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
	 * @var string|null
	 */
	private $dob;

	/**
	 * @var string|null
	 */
	private $dod;

	/**
	 * @param string|null $imageLocation
	 * @param string|null $dob
	 * @param string|null $dod
	 */
	public function __construct( $imageLocation = null, $dob = null, $dod = null ) {
		$this->imageLocation = $imageLocation;
		$this->dob = $dob;
		$this->dod = $dod;
	}

	/**
	 * @return null|string
	 */
	public function getImageLocation() {
		return $this->imageLocation;
	}

	/**
	 * @return null|string
	 */
	public function getDateOfBrith() {
		return $this->dob;
	}

	/**
	 * @return null|string
	 */
	public function getDateOfDeath() {
		return $this->dod;
	}

} 