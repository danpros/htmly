<?php

namespace Suin\RSSWriter;

use \DOMDocument;
use \Suin\RSSWriter\ChannelInterface;
use \Suin\RSSWriter\SimpleXMLElement;

class Feed implements \Suin\RSSWriter\FeedInterface
{
	/** @var \Suin\RSSWriter\ChannelInterface[] */
	protected $channels = array();

	/**
	 * Add channel
	 * @param \Suin\RSSWriter\ChannelInterface $channel
	 * @return $this
	 */
	public function addChannel(ChannelInterface $channel)
	{
		$this->channels[] = $channel;
		return $this;
	}

	/**
	 * Render XML
	 * @return string
	 */
	public function render()
	{
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><rss version="2.0" />', LIBXML_NOERROR|LIBXML_ERR_NONE|LIBXML_ERR_FATAL);

		foreach ( $this->channels as $channel )
		{
			$toDom   = dom_import_simplexml($xml);
			$fromDom = dom_import_simplexml($channel->asXML());
			$toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
		}

		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->appendChild($dom->importNode(dom_import_simplexml($xml), true));
		$dom->formatOutput = true;
		return $dom->saveXML();
	}

	/**
	 * Render XML
	 * @return string
	 */
	public function __toString()
	{
		return $this->render();
	}
}
