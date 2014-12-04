<?php

require_once( __DIR__ . '/../../viewsource.php' );

class Reign {

	/**
	 * @var int|null
	 */
	private $id;

	/**
	 * @var int
	 */
	private $startYear;

	/**
	 * @var int|null
	 */
	private $endYear;

	/**
	 * @var string|null
	 */
	private $type;

	/**
	 * @param int|null $id is is null if not in the database
	 * @param int $startYear
	 * @param int|null $endYear
	 * @param string|null $type
	 */
	public function __construct( $id, $startYear, $endYear = null, $type = null ) {
		$this->endYear = $endYear;
		$this->id = $id;
		$this->startYear = $startYear;
		$this->type = $type;
	}

	/**
	 * @return int|null
	 */
	public function getEndYear() {
		return $this->endYear;
	}

	/**
	 * @return int|null
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getStartYear() {
		return $this->startYear;
	}

	/**
	 * @return null|string
	 */
	public function getType() {
		return $this->type;
	}



} 