<?php

/**
 * Class Champion representing a single Champion
 */
class Champion {

	/**
	 * @var null|int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string|null
	 */
	private $enwikilink;

	/**
	 * @var Location[]
	 */
	private $locations;

	/**
	 * @var Reign[]
	 */
	private $reigns;

	/**
	 * @param int|null $id is is null if not in the database
	 * @param string $name
	 * @param Location[] $locations
	 * @param Reign[] $reigns
	 * @param string|null $enwikilink
	 */
	public function __construct( $id, $name, $locations, $reigns, $enwikilink = null ) {
		$this->enwikilink = $enwikilink;
		$this->id = $id;
		$this->locations = $locations;
		$this->name = $name;
		$this->reigns = $reigns;
	}

	/**
	 * @return null|string
	 */
	public function getEnwikilink() {
		return $this->enwikilink;
	}

	/**
	 * @return int|null
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return Location[]
	 */
	public function getLocations() {
		return $this->locations;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return Reign[]
	 */
	public function getReigns() {
		return $this->reigns;
	}



}