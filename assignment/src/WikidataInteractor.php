<?php

use Mediawiki\Api\MediawikiApi;
use Wikibase\Api\Service\RevisionsGetter;
use Wikibase\Api\WikibaseFactory;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\SiteLink;

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
	 * @return Item[]
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

		$entities = array();
		foreach( $revisions->toArray() as $revision ) {
			/** @var Item $entity */
			$entity = $revision->getContent()->getNativeData();
			foreach( $siteLinks as $enWikiLink => $siteLink ) {
				/** @var SiteLink $siteLink */
				$entityArticle = $entity->getSiteLinkList()->getBySiteId( 'enwiki' )->getPageName();
				$questionArticle = $siteLink->getPageName();
				if( $this->normaliseTitleString( $entityArticle ) == $this->normaliseTitleString( $questionArticle ) ) {
					$entities[ $enWikiLink ] = $entity;
				}
			}
		}

		return $entities;
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