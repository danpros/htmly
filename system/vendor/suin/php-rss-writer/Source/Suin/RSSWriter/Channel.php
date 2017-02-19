<?php

namespace Suin\RSSWriter;

use \Suin\RSSWriter\SimpleXMLElement;

class Channel implements \Suin\RSSWriter\ChannelInterface
{
	/** @var string */
	protected $title;
	/** @var string */
	protected $url;
	/** @var string */
	protected $description;
	/** @var string */
	protected $language;
	/** @var string */
	protected $copyright;
	/** @var int */
	protected $pubDate;
	/** @var int */
	protected $lastBuildDate;
	/** @var int */
	protected $ttl;
	/** @var \Suin\RSSWriter\ItemInterface[] */
	protected $items = array();

	/**
	 * Set channel title
	 * @param string $title
	 * @return $this
	 */
	public function title($title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * Set channel URL
	 * @param string $url
	 * @return $this
	 */
	public function url($url)
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * Set channel description
	 * @param string $description
	 * @return $this
	 */
	public function description($description)
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * Set ISO639 language code
	 *
	 * The language the channel is written in. This allows aggregators to group all
	 * Italian language sites, for example, on a single page. A list of allowable
	 * values for this element, as provided by Netscape, is here. You may also use
	 * values defined by the W3C.
	 *
	 * @param string $language
	 * @return $this
	 */
	public function language($language)
	{
		$this->language = $language;
		return $this;
	}

	/**
	 * Set channel copyright
	 * @param string $copyright
	 * @return $this
	 */
	public function copyright($copyright)
	{
		$this->copyright = $copyright;
		return $this;
	}

	/**
	 * Set channel published date
	 * @param int $pubDate Unix timestamp
	 * @return $this
	 */
	public function pubDate($pubDate)
	{
		$this->pubDate = $pubDate;
		return $this;
	}

	/**
	 * Set channel last build date
	 * @param int $lastBuildDate Unix timestamp
	 * @return $this
	 */
	public function lastBuildDate($lastBuildDate)
	{
		$this->lastBuildDate = $lastBuildDate;
		return $this;
	}

	/**
	 * Set channel ttl (minutes)
	 * @param int $ttl
	 * @return $this
	 */
	public function ttl($ttl)
	{
		$this->ttl = $ttl;
		return $this;
	}

	/**
	 * Add item object
	 * @param \Suin\RSSWriter\ItemInterface $item
	 * @return $this
	 */
	public function addItem(ItemInterface $item)
	{
		$this->items[] = $item;
		return $this;
	}

	/**
	 * Append to feed
	 * @param \Suin\RSSWriter\FeedInterface $feed
	 * @return $this
	 */
	public function appendTo(FeedInterface $feed)
	{
		$feed->addChannel($this);
		return $this;
	}

	/**
	 * Return XML object
	 * @return \Suin\RSSWriter\SimpleXMLElement
	 */
	public function asXML()
	{
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel></channel>', LIBXML_NOERROR|LIBXML_ERR_NONE|LIBXML_ERR_FATAL);
		$xml->addChild('title', $this->title);
		$xml->addChild('link', $this->url);
		$xml->addChild('description', $this->description);

		if ( $this->language !== null )
		{
			$xml->addChild('language', $this->language);
		}

		if ( $this->copyright !== null )
		{
			$xml->addChild('copyright', $this->copyright);
		}

		if ( $this->pubDate !== null )
		{
			$xml->addChild('pubDate', date(DATE_RSS, $this->pubDate));
		}

		if ( $this->lastBuildDate !== null )
		{
			$xml->addChild('lastBuildDate', date(DATE_RSS, $this->lastBuildDate));
		}

		if ( $this->ttl !== null )
		{
			$xml->addChild('ttl', $this->ttl);
		}

		foreach ( $this->items as $item )
		{
			$toDom   = dom_import_simplexml($xml);
			$fromDom = dom_import_simplexml($item->asXML());
			$toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
		}

		return $xml;
	}
}
