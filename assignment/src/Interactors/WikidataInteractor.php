<?php

require_once( __DIR__ . '/../../viewsource.php' );

use DataValues\StringValue;
use DataValues\TimeValue;
use Guzzle\Service\Mediawiki\MediawikiApiClient;
use Mediawiki\Api\MediawikiApi;
use Wikibase\Api\Service\RevisionsGetter;
use Wikibase\Api\WikibaseFactory;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\SiteLink;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\StatementList;

/**
 * Class WikidataInteractor
 *
 * Allows access to extra data from http://www.wikidata.org
 * This uses the addwiki/wikibase-api library written by me!
 */
class WikidataInteractor {

	/**
	 * @var RevisionsGetter
	 */
	private $revisionsGetter;

	public function __construct() {
		$config = array( 'base_url' => 'http://www.wikidata.org/w/api.php' );
		if( array_key_exists( 'HTTP_HOST', $_SERVER ) && strstr( $_SERVER['HTTP_HOST'], 'uwe.ac.uk' ) ) {
			$config['curl.options'] = array(
				'CURLOPT_PROXY' => 'tcp://proxy.uwe.ac.uk:8080',
			);
		}
		$client = MediawikiApiClient::factory( $config );
		$api = new MediawikiApi( $client );
		$services = new WikibaseFactory( $api );
		$this->revisionsGetter = $services->newRevisionsGetter();
	}

	/**
	 * Returns a collection of Wikidata Items mapped to their associated wikipedia url
	 *
	 * @param Champion[] $champions
	 *
	 * @return ExtraChampionData[]
	 */
	public function getExtraData( array $champions ) {
		$enWikiArticles = array();
		foreach( $champions as $champion ) {
			$enWikiLink = $champion->getEnwikilink();
			if( $enWikiLink !== null ) {
				$enWikiArticle = $this->getArticleFromEnWikiLink( $enWikiLink );
				if( $enWikiArticle !== null ) {
					$enWikiArticles[$enWikiLink] = $enWikiArticle;
				}
			}
		}

		$siteLinks = array();
		foreach( $enWikiArticles as $enWikiLink => $article ) {
			$siteLinks[ $enWikiLink ] = new SiteLink( 'enwiki', $article );
		}

		try{
			$revisions = $this->revisionsGetter->getRevisions( $siteLinks );
		}
		catch( \Guzzle\Http\Exception\CurlException $e ) {
			// This will happen if run @uwe and the proxy is broken (for example)
			return array();
		}

		$statementListMapping = array();
		foreach( $revisions->toArray() as $revision ) {
			/** @var Item $item */
			$item = $revision->getContent()->getNativeData();
			foreach( $siteLinks as $enWikiLink => $siteLink ) {
				/** @var SiteLink $siteLink */
				$entityArticle = $item->getSiteLinkList()->getBySiteId( 'enwiki' )->getPageName();
				$questionArticle = $siteLink->getPageName();
				if( $this->normaliseTitleString( $entityArticle ) == $this->normaliseTitleString( $questionArticle ) ) {
					$statementList = $item->getStatements()->getBestStatementPerProperty();
					if( !$statementList->isEmpty() ) {
						$statementListMapping[ $enWikiLink ] = $item->getStatements()->getBestStatementPerProperty();
					}
				}
			}
		}

		$timeFormatter = new \ValueFormatters\TimeFormatter( new \ValueFormatters\FormatterOptions() );

		$extraChampionDataMapping = array();
		foreach( $statementListMapping as $enWikiLink => $statementList ) {
			$extraData = array(
				'datalinks' => array(),
			);

			/** @var StatementList $statementList */
			foreach( $statementList->toArray() as $statement ) {
				$propertyNumber = $statement->getPropertyId()->getNumericId();
				if( $propertyNumber === 18 ) {
					/** @var PropertyValueSnak $imageSnak */
					$imageSnak = $statement->getMainSnak();
					/** @var StringValue $imageDataValue */
					$imageDataValue = $imageSnak->getDataValue();
					$extraData['image'] = str_replace( ' ', '_', $imageDataValue->getValue() ) ;
				} elseif ( $propertyNumber === 569 ) {
					/** @var PropertyValueSnak $dateSnak */
					$dateSnak = $statement->getMainSnak();
					/** @var TimeValue $timeDataValue */
					$timeDataValue = $dateSnak->getDataValue();
					$extraData['dob'] = $timeFormatter->format( $timeDataValue );
				} elseif( $propertyNumber === 570 ) {
					/** @var PropertyValueSnak $dateSnak */
					$dateSnak = $statement->getMainSnak();
					/** @var TimeValue $timeDataValue */
					$timeDataValue = $dateSnak->getDataValue();
					$extraData['dod'] = $timeFormatter->format( $timeDataValue );
				} elseif ( $propertyNumber === 214 ) {
					/** @var PropertyValueSnak $mainSnak */
					$mainSnak = $statement->getMainSnak();
					/** @var StringValue $stringDataValue */
					$stringDataValue = $mainSnak->getDataValue();
					$extraData['datalinks']['viaf'] = $stringDataValue->getValue();
				} elseif ( $propertyNumber === 213 ) {
					/** @var PropertyValueSnak $mainSnak */
					$mainSnak = $statement->getMainSnak();
					/** @var StringValue $stringDataValue */
					$stringDataValue = $mainSnak->getDataValue();
					$extraData['datalinks']['isni'] = $stringDataValue->getValue();
				} elseif ( $propertyNumber === 227 ) {
					/** @var PropertyValueSnak $mainSnak */
					$mainSnak = $statement->getMainSnak();
					/** @var StringValue $stringDataValue */
					$stringDataValue = $mainSnak->getDataValue();
					$extraData['datalinks']['gnd'] = $stringDataValue->getValue();
				} elseif ( $propertyNumber === 244 ) {
					/** @var PropertyValueSnak $mainSnak */
					$mainSnak = $statement->getMainSnak();
					/** @var StringValue $stringDataValue */
					$stringDataValue = $mainSnak->getDataValue();
					$extraData['datalinks']['lcnaf'] = $stringDataValue->getValue();
				}
			}

			if( !empty( $extraData ) ) {
				$extraChampionDataMapping[ $enWikiLink ] = new ExtraChampionData(
					( array_key_exists( 'image', $extraData ) ? $extraData['image'] : null ),
					( array_key_exists( 'dob', $extraData ) ? $extraData['dob'] : null ),
					( array_key_exists( 'dod', $extraData ) ? $extraData['dod'] : null ),
					( $extraData['datalinks'] )
				);
			}
		}

		return $extraChampionDataMapping;
	}

	/**
	 * @param string $enWikiLink link in format (.*)(en\.wikipedia\.org\/wiki\/)(.*)
	 *
	 * @return string
	 */
	private function getArticleFromEnWikiLink( $enWikiLink ) {
		$split = explode( 'en.wikipedia.org/wiki/', $enWikiLink );
		if( count( $split ) < 2 ) {
			return null;
		}
		return $split[1];
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	private function normaliseTitleString( $string ) {
		return strtolower( str_replace( '_', ' ', $string ) );
	}

} 