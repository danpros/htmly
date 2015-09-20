<?php

namespace Suin\RSSWriter;

/**
 * Interface FeedInterface
 * @package Suin\RSSWriter
 */
interface FeedInterface
{
    /**
     * Add channel
     * @param ChannelInterface $channel
     * @return $thisJ
     */
    public function addChannel(ChannelInterface $channel);

    /**
     * Render XML
     * @return string
     */
    public function render();

    /**
     * Render XML
     * @return string
     */
    public function __toString();
}
