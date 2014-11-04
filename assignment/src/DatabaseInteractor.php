<?php

final class DatabaseInteractor {

	/**
	 * @return PDO linked to default details
	 */
	private function getPDO() {
		//TODO these details should be somewhere else
		return new PDO( 'mysql:host=127.0.0.1', 'root', 'toor' );
	}

	/**
	 * @throws Exception
	 * @return bool
	 */
	public function create() {
		$sql = file_get_contents( __DIR__ . '/../sql/create.sql' );
		$result = $this->getPDO()->exec( $sql );
		if( $result === false ) {
			throw new Exception( $this->getPDO()->errorInfo() );
		}
		return $result;
	}

	/**
	 * @throws Exception
	 * @return bool
	 */
	public function drop() {
		$sql = file_get_contents( __DIR__ . '/../sql/drop.sql' );
		$result = $this->getPDO()->exec( $sql );
		if( $result === false ) {
			throw new Exception( $this->getPDO()->errorInfo() );
		}
		return $result;
	}

} 