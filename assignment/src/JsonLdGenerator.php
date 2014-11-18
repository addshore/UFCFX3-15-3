<?php

class JsonLdGenerator {

	/**
	 * @param Champion[] $champions
	 *
	 * @return string
	 */
	public function generate( array $champions ) {
		$outArray = array();

		foreach( $champions as $champion ) {
			$outArray[] = array(
				'@context' => 'http://schema.org/',
				'@type' => 'Person',
				'name' => $champion->getName(),
			);
		}

		return json_encode( $outArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	}

} 