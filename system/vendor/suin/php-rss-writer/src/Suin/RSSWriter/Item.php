<?php

namespace Suin\RSSWriter;

/**
 * Class Item
 * @package Suin\RSSWriter
 */
class Item implements ItemInterface
{
    /** @var string */
    protected $title;

    /** @var string */
    protected $url;

    /** @var string */
    protected $description;

    /** @var string */
    protected $contentEncoded;

    /** @var array */
    protected $categories = [];

    /** @var string */
    protected $guid;

    /** @var bool */
    protected $isPermalink;

    /** @var int */
    protected $pubDate;

    /** @var array */
    protected $enclosure;

    /** @var string */
    protected $author;

    /** @var string */
    protected $creator;

    protected $preferCdata = false;

    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    public function url($url)
    {
        $this->url = $url;
        return $this;
    }

    public function description($description)
    {
        $this->description = $description;
        return $this;
    }

    public function contentEncoded($content)
    {
        $this->contentEncoded = $content;
        return $this;
    }

    public function category($name, $domain = null)
    {
        $this->categories[] = [$name, $domain];
        return $this;
    }

    public function guid($guid, $isPermalink = false)
    {
        $this->guid = $guid;
        $this->isPermalink = $isPermalink;
        return $this;
    }

    public function pubDate($pubDate)
    {
        $this->pubDate = $pubDate;
        return $this;
    }

    public function enclosure($url, $length = 0, $type = 'audio/mpeg')
    {
        $this->enclosure = ['url' => $url, 'length' => $length, 'type' => $type];
        return $this;
    }

    public function author($author)
    {
        $this->author = $author;
        return $this;
    }

    public function creator($creator)
    {
        $this->creator = $creator;
        return $this;
    }

    public function preferCdata($preferCdata)
    {
        $this->preferCdata = (bool)$preferCdata;
        return $this;
    }

    public function appendTo(ChannelInterface $channel)
    {
        $channel->addItem($this);
        return $this;
    }

    public function asXML()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><item></item>', LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_ERR_FATAL);

        if ($this->preferCdata) {
            $xml->addCdataChild('title', $this->title);
        } else {
            $xml->addChild('title', $this->title);
        }

        $xml->addChild('link', $this->url);

        if ($this->preferCdata) {
            $xml->addCdataChild('description', $this->description);
        } else {
            $xml->addChild('description', $this->description);
        }

        if ($this->contentEncoded) {
            $xml->addCdataChild('xmlns:content:encoded', $this->contentEncoded);
        }

        foreach ($this->categories as $category) {
            $element = $xml->addChild('category', $category[0]);

            if (isset($category[1])) {
                $element->addAttribute('domain', $category[1]);
            }
        }

        if ($this->guid) {
            $guid = $xml->addChild('guid', $this->guid);

            if ($this->isPermalink === false) {
                $guid->addAttribute('isPermaLink', 'false');
            }
        }

        if ($this->pubDate !== null) {
            $xml->addChild('pubDate', date(DATE_RSS, $this->pubDate));
        }

        if (is_array($this->enclosure) && (count($this->enclosure) == 3)) {
            $element = $xml->addChild('enclosure');
            $element->addAttribute('url', $this->enclosure['url']);
            $element->addAttribute('type', $this->enclosure['type']);

            if ($this->enclosure['length']) {
                $element->addAttribute('length', $this->enclosure['length']);
            }
        }

        if (!empty($this->author)) {
            $xml->addChild('author', $this->author);
        }

        if (!empty($this->creator)) {
            $xml->addChild('dc:creator', $this->creator,"http://purl.org/dc/elements/1.1/");
        }

        return $xml;
    }
}
