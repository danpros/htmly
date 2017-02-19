<?php

namespace Suin\RSSWriter;

use \Suin\RSSWriter\ChannelInterface;
use \Suin\RSSWriter\SimpleXMLElement;

interface ItemInterface
{
	/**
	 * Set item title
	 * @param string $title
	 * @return $this
	 */
	public function title($title);

	/**
	 * Set item URL
	 * @param string $url
	 * @return $this
	 */
	public function url($url);

	/**
	 * Set item description
	 * @param string $description
	 * @return $this
	 */
	public function description($description);

	/**
	 * Set item category
	 * @param string $name Category name
	 * @param string $domain Category URL
	 * @return $this
	 */
	public function category($name, $domain = null);

	/**
	 * Set GUID
	 * @param string $guid
	 * @param bool $isPermalink
	 * @return $this
	 */
	public function guid($guid, $isPermalink = false);

	/**
	 * Set published date
	 * @param int $pubDate Unix timestamp
	 * @return $this
	 */
	public function pubDate($pubDate);

	/**
	 * Set enclosure 
	 * @param var $url Url to media file
     * @param int $length Length in bytes of the media file
     * @param var $type Media type, default is audio/mpeg
	 * @return $this
	 */
	public function enclosure($url, $length = 0, $type = 'audio/mpeg');

	/**
	 * Append item to the channel
	 * @param \Suin\RSSWriter\ChannelInterface $channel
	 * @return $this
	 */
	public function appendTo(ChannelInterface $channel);

	/**
	 * Return XML object
	 * @return \Suin\RSSWriter\SimpleXMLElement
	 */
	public function asXML();
}
