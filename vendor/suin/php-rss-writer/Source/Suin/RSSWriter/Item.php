<?php

namespace Suin\RSSWriter;

use \Suin\RSSWriter\SimpleXMLElement;

class Item implements \Suin\RSSWriter\ItemInterface
{
	/** @var string */
	protected $title;
	/** @var string */
	protected $url;
	/** @var string */
	protected $description;
	/** @var array */
	protected $categories = array();
	/** @var string */
	protected $guid;
	/** @var bool */
	protected $isPermalink;
	/** @var int */
	protected $pubDate;
    /** @var array */
    protected $enclosure;

	/**
	 * Set item title
	 * @param string $title
	 * @return $this
	 */
	public function title($title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * Set item URL
	 * @param string $url
	 * @return $this
	 */
	public function url($url)
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * Set item description
	 * @param string $description
	 * @return $this
	 */
	public function description($description)
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * Set item category
	 * @param string $name   Category name
	 * @param string $domain Category URL
	 * @return $this
	 */
	public function category($name, $domain = null)
	{
		$this->categories[] = array($name, $domain);
		return $this;
	}

	/**
	 * Set GUID
	 * @param string $guid
	 * @param bool   $isPermalink
	 * @return $this
	 */
	public function guid($guid, $isPermalink = false)
	{
		$this->guid = $guid;
		$this->isPermalink = $isPermalink;
		return $this;
	}

	/**
	 * Set published date
	 * @param int $pubDate Unix timestamp
	 * @return $this
	 */
	public function pubDate($pubDate)
	{
		$this->pubDate = $pubDate;
		return $this;
	}

	/**
	 * Set enclosure 
	 * @param var $url Url to media file
     * @param int $length Length in bytes of the media file
     * @param var $type Media type, default is audio/mpeg
	 * @return $this
	 */
	public function enclosure($url, $length = 0, $type = 'audio/mpeg')
	{
		$this->enclosure = array('url' => $url, 'length' => $length, 'type' => $type);
		return $this;
	}

	/**
	 * Append item to the channel
	 * @param \Suin\RSSWriter\ChannelInterface $channel
	 * @return $this
	 */
	public function appendTo(ChannelInterface $channel)
	{
		$channel->addItem($this);
		return $this;
	}

	/**
	 * Return XML object
	 * @return \Suin\RSSWriter\SimpleXMLElement
	 */
	public function asXML()
	{
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><item></item>', LIBXML_NOERROR|LIBXML_ERR_NONE|LIBXML_ERR_FATAL);
		$xml->addChild('title', $this->title);
		$xml->addChild('link', $this->url);
		$xml->addChild('description', $this->description);

		foreach ( $this->categories as $category )
		{
			$element = $xml->addChild('category', $category[0]);

			if ( isset($category[1]) )
			{
				$element->addAttribute('domain', $category[1]);
			}
		}

		if ( $this->guid )
		{
			$guid = $xml->addChild('guid', $this->guid);

			if ( $this->isPermalink )
			{
				$guid->addAttribute('isPermaLink', 'true');
			}
		}

		if ( $this->pubDate !== null )
		{
			$xml->addChild('pubDate', date(DATE_RSS, $this->pubDate));
		}


        if (is_array($this->enclosure) && (count($this->enclosure) == 3))
		{
			$element = $xml->addChild('enclosure');
            $element->addAttribute('url', $this->enclosure['url']);
            $element->addAttribute('type', $this->enclosure['type']);
            
            if ($this->enclosure['length']) 
            {
                $element->addAttribute('length', $this->enclosure['length']);
            }
		}
		return $xml;
	}
}
