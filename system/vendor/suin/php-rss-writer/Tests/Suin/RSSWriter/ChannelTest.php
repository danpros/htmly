<?php

namespace Suin\RSSWriter;

class ChannelTest extends \XoopsUnit\TestCase
{
    private $itemInterface = '\Suin\RSSWriter\ItemInterface';
    private $feedInterface = '\Suin\RSSWriter\FeedInterface';

    public function testTitle()
    {
        $title = uniqid();
        $channel = new Channel();
        $this->assertSame($channel, $channel->title($title));
        $this->assertAttributeSame($title, 'title', $channel);
    }

    public function testUrl()
    {
        $url = uniqid();
        $channel = new Channel();
        $this->assertSame($channel, $channel->url($url));
        $this->assertAttributeSame($url, 'url', $channel);
    }

    public function testDescription()
    {
        $description = uniqid();
        $channel = new Channel();
        $this->assertSame($channel, $channel->description($description));
        $this->assertAttributeSame($description, 'description', $channel);
    }

    public function testLanguage()
    {
        $language = uniqid();
        $channel = new Channel();
        $this->assertSame($channel, $channel->language($language));
        $this->assertAttributeSame($language, 'language', $channel);
    }

    public function testCopyright()
    {
        $copyright = uniqid();
        $channel = new Channel();
        $this->assertSame($channel, $channel->copyright($copyright));
        $this->assertAttributeSame($copyright, 'copyright', $channel);
    }

    public function testPubDate()
    {
        $pubDate = mt_rand(0, 9999999);
        $channel = new Channel();
        $this->assertSame($channel, $channel->pubDate($pubDate));
        $this->assertAttributeSame($pubDate, 'pubDate', $channel);
    }

    public function testLastBuildDate()
    {
        $lastBuildDate = mt_rand(0, 9999999);
        $channel = new Channel();
        $this->assertSame($channel, $channel->lastBuildDate($lastBuildDate));
        $this->assertAttributeSame($lastBuildDate, 'lastBuildDate', $channel);
    }

    public function testTtl()
    {
        $ttl = mt_rand(0, 99999999);
        $channel = new Channel();
        $this->assertSame($channel, $channel->ttl($ttl));
        $this->assertAttributeSame($ttl, 'ttl', $channel);
    }

    public function testPubsubhubbub()
    {
        $channel = new Channel();
        $channel->pubsubhubbub('http://example.com/feed.xml', 'http://pubsubhubbub.appspot.com');
        $xml = $channel->asXML()->asXML();
        $this->assertContains('<atom:link rel="self" href="http://example.com/feed.xml" type="application/rss+xml"/>', $xml);
        $this->assertContains('<atom:link rel="hub" href="http://pubsubhubbub.appspot.com"/>', $xml);
    }

    public function testAddItem()
    {
        $item = $this->getMock($this->itemInterface);
        $channel = new Channel();
        $this->assertSame($channel, $channel->addItem($item));
        $this->assertAttributeSame([$item], 'items', $channel);
    }

    public function testAppendTo()
    {
        $channel = new Channel();
        $feed = $this->getMock($this->feedInterface);
        $feed->expects($this->once())->method('addChannel')->with($channel);
        $this->assertSame($channel, $channel->appendTo($feed));
    }

    /**
     * @param       $expect
     * @param array $data
     * @dataProvider dataForAsXML
     */
    public function testAsXML($expect, array $data)
    {
        $data = (object)$data;
        $channel = new Channel();

        foreach ($data as $key => $value) {
            $this->reveal($channel)->attr($key, $value);
        }

        $this->assertXmlStringEqualsXmlString($expect, $channel->asXML()->asXML());
    }

    public static function dataForAsXML()
    {
        $now = time();
        $nowString = date(DATE_RSS, $now);

        return [
            [
                "
                <channel>
                    <title>GoUpstate.com News Headlines</title>
                    <link>http://www.goupstate.com/</link>
                    <description>The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.</description>
                </channel>
                ",
                [
                    'title'       => "GoUpstate.com News Headlines",
                    'url'         => 'http://www.goupstate.com/',
                    'description' => "The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.",
                ]
            ],
            [
                "
                <channel>
                    <title>GoUpstate.com News Headlines</title>
                    <link>http://www.goupstate.com/</link>
                    <description>The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.</description>
                    <language>en-us</language>
                </channel>
                ",
                [
                    'title'       => "GoUpstate.com News Headlines",
                    'url'         => 'http://www.goupstate.com/',
                    'description' => "The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.",
                    'language'    => 'en-us',
                ]
            ],
            [
                "
                <channel>
                    <title>GoUpstate.com News Headlines</title>
                    <link>http://www.goupstate.com/</link>
                    <description>The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.</description>
                    <pubDate>{$nowString}</pubDate>
                </channel>
                ",
                [
                    'title'       => "GoUpstate.com News Headlines",
                    'url'         => 'http://www.goupstate.com/',
                    'description' => "The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.",
                    'pubDate'     => $now,
                ]
            ],
            [
                "
                <channel>
                    <title>GoUpstate.com News Headlines</title>
                    <link>http://www.goupstate.com/</link>
                    <description>The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.</description>
                    <lastBuildDate>{$nowString}</lastBuildDate>
                </channel>
                ",
                [
                    'title'         => "GoUpstate.com News Headlines",
                    'url'           => 'http://www.goupstate.com/',
                    'description'   => "The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.",
                    'lastBuildDate' => $now,
                ]
            ],
            [
                "
                <channel>
                    <title>GoUpstate.com News Headlines</title>
                    <link>http://www.goupstate.com/</link>
                    <description>The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.</description>
                    <ttl>60</ttl>
                </channel>
                ",
                [
                    'title'       => "GoUpstate.com News Headlines",
                    'url'         => 'http://www.goupstate.com/',
                    'description' => "The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.",
                    'ttl'         => 60,
                ]
            ],
            [
                "
                <channel>
                    <title>GoUpstate.com News Headlines</title>
                    <link>http://www.goupstate.com/</link>
                    <description>The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.</description>
                    <copyright>Copyright 2002, Spartanburg Herald-Journal</copyright>
                </channel>
                ",
                [
                    'title'       => "GoUpstate.com News Headlines",
                    'url'         => 'http://www.goupstate.com/',
                    'description' => "The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.",
                    'copyright'   => "Copyright 2002, Spartanburg Herald-Journal",
                ]
            ],
        ];
    }

    public function testAppendTo_with_items()
    {
        $channel = new Channel();

        $xml1 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><item><title>item1</title></item>');
        $xml2 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><item><title>item2</title></item>');
        $xml3 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><item><title>item3</title></item>');

        $item1 = $this->getMock($this->itemInterface);
        $item1->expects($this->once())->method('asXML')->will($this->returnValue($xml1));
        $item2 = $this->getMock($this->itemInterface);
        $item2->expects($this->once())->method('asXML')->will($this->returnValue($xml2));
        $item3 = $this->getMock($this->itemInterface);
        $item3->expects($this->once())->method('asXML')->will($this->returnValue($xml3));

        $this->reveal($channel)
            ->attr('title', "GoUpstate.com News Headlines")
            ->attr('url', 'http://www.goupstate.com/')
            ->attr('description', "The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.")
            ->attr('items', [$item1, $item2, $item3]);

        $expect = '<?xml version="1.0" encoding="UTF-8" ?>
            <channel>
                <title>GoUpstate.com News Headlines</title>
                <link>http://www.goupstate.com/</link>
                <description>The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.</description>
                <item>
                    <title>item1</title>
                </item>
                <item>
                    <title>item2</title>
                </item>
                <item>
                    <title>item3</title>
                </item>
            </channel>
        ';

        $this->assertXmlStringEqualsXmlString($expect, $channel->asXML()->asXML());
    }
}
