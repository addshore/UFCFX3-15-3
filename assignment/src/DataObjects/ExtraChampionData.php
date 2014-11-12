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
	 * @var array
	 */
	private $dataLinks;

	/**
	 * @param string|null $imageLocation
	 * @param string|null $dob
	 * @param string|null $dod
	 * @param array $dataLinks
	 */
	public function __construct( $imageLocation = null, $dob = null, $dod = null, $dataLinks = array() ) {
		$this->imageLocation = $imageLocation;
		$this->dob = $dob;
		$this->dod = $dod;
		$this->dataLinks = $dataLinks;
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

	/**
	 * @return array
	 */
	public function getDataLinks() {
		return $this->dataLinks;
	}

} 