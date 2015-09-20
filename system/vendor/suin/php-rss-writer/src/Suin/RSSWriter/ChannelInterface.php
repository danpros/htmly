<?php

namespace Suin\RSSWriter;

/**
 * Interface ChannelInterface
 * @package Suin\RSSWriter
 */
interface ChannelInterface
{
    /**
     * Set channel title
     * @param string $title
     * @return $this
     */
    public function title($title);

    /**
     * Set channel URL
     * @param string $url
     * @return $this
     */
    public function url($url);

    /**
     * Set channel description
     * @param string $description
     * @return $this
     */
    public function description($description);

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
    public function language($language);

    /**
     * Set channel copyright
     * @param string $copyright
     * @return $this
     */
    public function copyright($copyright);

    /**
     * Set channel published date
     * @param int $pubDate Unix timestamp
     * @return $this
     */
    public function pubDate($pubDate);

    /**
     * Set channel last build date
     * @param int $lastBuildDate Unix timestamp
     * @return $this
     */
    public function lastBuildDate($lastBuildDate);

    /**
     * Set channel ttl (minutes)
     * @param int $ttl
     * @return $this
     */
    public function ttl($ttl);

    /**
     * Add item object
     * @param ItemInterface $item
     * @return $this
     */
    public function addItem(ItemInterface $item);

    /**
     * Append to feed
     * @param FeedInterface $feed
     * @return $this
     */
    public function appendTo(FeedInterface $feed);

    /**
     * Return XML object
     * @return SimpleXMLElement
     */
    public function asXML();
}
