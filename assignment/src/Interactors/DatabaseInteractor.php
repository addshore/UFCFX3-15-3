<?php

require_once( __DIR__ . '/../../viewsource.php' );

final class DatabaseInteractor {

	/**
	 * @return PDO linked to default details
	 */
	private function newPDO() {
		//TODO these details should be somewhere else
		return new PDO( 'mysql:host=127.0.0.1', 'root', 'toor' );
	}

	/**
	 * Runs the create.sql script
	 *
	 * @throws Exception
	 * @return bool
	 */
	public function create() {
		$sql = file_get_contents( __DIR__ . '/../sql/create.sql' );
		$result = $this->newPDO()->exec( $sql );
		if( $result === false ) {
			throw new Exception( $this->newPDO()->errorInfo() );
		}
		return $result;
	}

	/**
	 * Runs the drop.sql script
	 *
	 * @throws Exception
	 * @return bool
	 */
	public function drop() {
		$sql = file_get_contents( __DIR__ . '/../sql/drop.sql' );
		$result = $this->newPDO()->exec( $sql );
		if( $result === false ) {
			throw new Exception( $this->newPDO()->errorInfo() );
		}
		return $result;
	}

	/**
	 * Inserts the given champions into the database
	 *
	 * @param Champion[] $champions
	 *
	 * @throws Exception
	 * @return bool
	 */
	public function insertChampions( $champions ) {
		$db = $this->newPDO();
		//TODO database name should not be hard coded
		$db->query( 'use atwd_assignment' );

		$championInsertStatement  = $db->prepare(
			'INSERT INTO champion ( name, enwikilink ) VALUES (:name, :enwikilink )'
		);
		$championSelectStatement = $db->prepare(
			'SELECT * FROM champion WHERE name = ?'
		);
		$reignInsertStatement = $db->prepare(
			'INSERT INTO reign ( champion_id, start_year, end_year, type ) VALUES (:champion_id, :start_year, :end_year, :type )'
		);
		$locationInsertStatement = $db->prepare(
			'INSERT INTO location
			( country, country_link, historical, historical_link, flag_img )
			VALUES (:country, :country_link, :historical, :historical_link, :flag_img )'
		);
		$championLocationInsertStatement = $db->prepare(
			'INSERT INTO champion_location ( champion_id, location_id ) VALUES (:champion_id, :location_id )'
		);

		foreach( $champions as $champion ) {
			$queryData = array(
				':name' => $champion->getName(),
				':enwikilink' => $champion->getEnwikilink(),
			);
			$result = $championInsertStatement->execute( $queryData );
				if( $result === false ) {
					throw new Exception( implode( ', ', $championInsertStatement->errorInfo() ) . ': ' . implode( ', ', $queryData ) );
				}

			$result = $championSelectStatement->execute( array( $champion->getName() ) );
				if( $result === false ) {
					throw new Exception( implode( ', ', $championSelectStatement->errorInfo() ) . ': ' . implode( ', ', $queryData ) );
				}
			$championId = $championSelectStatement->fetchColumn( 0 );

			foreach( $champion->getReigns() as $reign ) {
				$queryData = array(
					':champion_id' => $championId,
					':start_year' => $reign->getStartYear(),
					':end_year' => $reign->getEndYear(),
					':type' => $reign->getType(),
				);
				$result = $reignInsertStatement->execute( $queryData );
				if( $result === false ) {
					throw new Exception( implode( ', ', $reignInsertStatement->errorInfo() ) . ': ' . implode( ', ', $queryData ) );
				}
			}

			$locationIds = array();
			foreach( $champion->getLocations() as $location ) {
				$queryData = array(
					':country' => $location->getCountry(),
					':country_link' => $location->getCountryLink(),
					':historical' => $location->getHistorical(),
					':historical_link' => $location->getHistoricalLink(),
					':flag_img' => $location->getFlagUrl(),
				);

				$locationSelectQuery = 'SELECT * FROM location WHERE';
				foreach( $queryData as $key => $value ) {
					if( !is_null( $value ) ) {
						$locationSelectQuery .= ' ' . trim( $key, ':' ) . ' = ' . $db->quote( $value ) . ' AND';
					} else {
						$locationSelectQuery .= ' ' . trim( $key, ':' ) . ' IS NULL AND';
					}
				}
				//Remove the final AND
				$locationSelectQuery = substr( $locationSelectQuery, 0, -4 );

				$result = $db->query( $locationSelectQuery );
				if( $result === false ) {
					throw new Exception( implode( ', ', $db->errorInfo() ) . ': ' . implode( ', ', $queryData ) );
				}
				$locationId = $result->fetchColumn( 0 );
				if( $locationId === false ) {
					$result = $locationInsertStatement->execute( $queryData );
					if( $result === false ) {
						throw new Exception( implode( ', ', $locationInsertStatement->errorInfo() ) . ': ' . implode( ', ', $queryData ) );
					}
					$result = $db->query( $locationSelectQuery );
					if( $result === false ) {
						throw new Exception( implode( ', ', $db->errorInfo() ) . ': ' . implode( ', ', $queryData ) );
					}
					$locationId = $result->fetchColumn( 0 );
				}
				$locationIds[] = $locationId;

			}

			foreach( $locationIds as $locationId ) {
				$queryData = array(
					':champion_id' => $championId,
					':location_id' => $locationId,
				);
				$result = $championLocationInsertStatement->execute( $queryData );
				if( $result === false ) {
					throw new Exception( implode( ', ', $db->errorInfo() ) . ': ' . implode( ', ', $queryData ) );
				}
			}
		}

		return true;
	}

	/**
	 * Gets all champions in the database
	 *
	 * @throws Exception
	 * @return Champion[]
	 */
	public function getChampions() {
		$db = $this->newPDO();
		$db->query( 'use atwd_assignment' );
		$champions = array();

		$championQuery = 'SELECT c.*, GROUP_CONCAT( DISTINCT cl.location_id ) AS locations, GROUP_CONCAT( DISTINCT r.id ) AS reigns
			FROM champion AS c
			LEFT JOIN champion_location AS cl ON c.id = cl.champion_id
			LEFT JOIN reign AS r ON c.id = r.champion_id
			GROUP BY c.id';

		$championRows = $this->indexRowsById( $this->getRows( $db, $championQuery ) );
		$locationRows = $this->indexRowsById( $this->getRows( $db, 'SELECT * FROM location' ) );
		$reignRows = $this->indexRowsById( $this->getRows( $db, 'SELECT * FROM reign' ) );

		foreach( $championRows as $championId => $championRow ) {

			$locations = array();
			foreach( explode( ',', $championRow['locations'] ) as $locationId ) {
				$locations[$locationId] = new Location(
					$locationId,
					$locationRows[$locationId]['country'],
					$locationRows[$locationId]['country_link'],
					$locationRows[$locationId]['flag_img'],
					$locationRows[$locationId]['historical'],
					$locationRows[$locationId]['historical_link']
				);
			}

			$reigns = array();
			foreach( explode( ',', $championRow['reigns'] ) as $reignId ) {
				$reigns[$reignId] = new Reign(
					$reignId,
					$reignRows[$reignId]['start_year'],
					$reignRows[$reignId]['end_year'],
					$reignRows[$reignId]['type']
				);
			}

			$champions[$championId] = new Champion(
				$championId,
				$championRow['name'],
				$locations,
				$reigns,
				$championRow['enwikilink']
			);
		}

		return $champions;
	}

	/**
	 * Return all rows in a given table
	 *
	 * @param PDO $db
	 * @param string $query
	 *
	 * @return array
	 * @throws Exception
	 */
	private function getRows( PDO $db, $query ) {
		$championResult = $db->query( $query );
		if( $championResult === false ) {
			throw new Exception( implode( ', ', $db->errorInfo() ) . ' : ' . $query );
		}
		return $championResult->fetchAll();
	}

	/**
	 * @todo this logic might be better in some generic LIST DataObject
	 * @param array $rows
	 *
	 * @return array
	 */
	private function indexRowsById( array $rows ) {
		$newRows = array();
		foreach( $rows as $row ) {
			$newRows[$row['id']] = $row;
		}
		return $newRows;
	}
} 