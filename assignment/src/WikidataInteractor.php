<?php

use DataValues\StringValue;
use Mediawiki\Api\MediawikiApi;
use Wikibase\Api\Service\RevisionsGetter;
use Wikibase\Api\WikibaseFactory;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\SiteLink;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\StatementList;

class WikidataInteractor {

	/**
	 * @var RevisionsGetter
	 */
	private $revisionsGetter;

	public function __construct() {
		$api = new MediawikiApi( "http://www.wikidata.org/w/api.php" );
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
		$revisions = $this->revisionsGetter->getRevisions( $siteLinks );

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

		$extraChampionDataMapping = array();
		foreach( $statementListMapping as $enWikiLink => $statementList ) {
			$extraData = array();

			/** @var StatementList $statementList */
			foreach( $statementList->toArray() as $statement ) {
				if( $statement->getPropertyId()->getNumericId() === 18 ) {
					/** @var PropertyValueSnak $imageSnak */
					$imageSnak = $statement->getMainSnak();
					/** @var StringValue $imageDataValue */
					$imageDataValue = $imageSnak->getDataValue();
					$extraData['image'] = $imageDataValue->getValue();
				}
			}

			if( !empty( $extraData ) ) {
				$extraChampionDataMapping[ $enWikiLink ] = new ExtraChampionData(
					( array_key_exists( 'image', $extraData ) ? $extraData['image'] : null )
				);
			}
		}

		return $extraChampionDataMapping;
	}

	private function getArticleFromEnWikiLink( $enWikiLink ) {
		$split = explode( 'en.wikipedia.org/wiki/', $enWikiLink );
		if( count( $split ) < 2 ) {
			return null;
		}
		return $split[1];
	}

	private function normaliseTitleString( $string ) {
		return strtolower( str_replace( '_', ' ', $string ) );
	}

} 