<?php

class Location {

	/**
	 * @var int|null
	 */
	private $id;

	/**
	 * @var string
	 */
	private $country;

	/**
	 * @var string|null
	 */
	private $countryLink;

	/**
	 * @var string|null
	 */
	private $historical;

	/**
	 * @var string|null
	 */
	private $historicalLink;

	/**
	 * @var string|null
	 */
	private $flagUrl;

	/**
	 * @param int|null $id is is null if not in the database
	 * @param string $country
	 * @param string|null $countryLink
	 * @param string|null $flagUrl
	 * @param string|null $historical
	 * @param string|null $historicalLink
	 */
	public function __construct( $id, $country, $countryLink = null, $flagUrl = null, $historical = null, $historicalLink = null ) {
		$this->country = $country;
		$this->countryLink = $countryLink;
		$this->flagUrl = $flagUrl;
		$this->historical = $historical;
		$this->historicalLink = $historicalLink;
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * @return null|string
	 */
	public function getCountryLink() {
		return $this->countryLink;
	}

	/**
	 * @return null|string
	 */
	public function getFlagUrl() {
		return $this->flagUrl;
	}

	/**
	 * @return null|string
	 */
	public function getHistorical() {
		return $this->historical;
	}

	/**
	 * @return null|string
	 */
	public function getHistoricalLink() {
		return $this->historicalLink;
	}

	/**
	 * @return int|null
	 */
	public function getId() {
		return $this->id;
	}



} 