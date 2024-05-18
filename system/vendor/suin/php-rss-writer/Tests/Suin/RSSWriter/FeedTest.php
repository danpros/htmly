<?php

namespace Suin\RSSWriter;

use Mockery;

class FeedTest extends \XoopsUnit\TestCase
{
    private $channelInterface = '\Suin\RSSWriter\ChannelInterface';

    public function testAddChannel()
    {
        $channel = Mockery::mock($this->channelInterface);
        $feed = new Feed();
        $this->assertSame($feed, $feed->addChannel($channel));
        $this->assertAttributeSame([$channel], 'channels', $feed);
    }

    public function testRender()
    {
        $feed = new Feed();
        $xml1 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>channel1</title></channel>');
        $xml2 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>channel2</title></channel>');
        $xml3 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>channel3</title></channel>');
        $channel1 = $this->getMock($this->channelInterface);
        $channel1->expects($this->once())->method('asXML')->will($this->returnValue($xml1));
        $channel2 = $this->getMock($this->channelInterface);
        $channel2->expects($this->once())->method('asXML')->will($this->returnValue($xml2));
        $channel3 = $this->getMock($this->channelInterface);
        $channel3->expects($this->once())->method('asXML')->will($this->returnValue($xml3));
        $this->reveal($feed)->attr('channels', [$channel1, $channel2, $channel3]);
        $expect = '<?xml version="1.0" encoding="UTF-8" ?>
            <rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
                <channel><title>channel1</title></channel>
                <channel><title>channel2</title></channel>
                <channel><title>channel3</title></channel>
            </rss>
        ';
        $this->assertXmlStringEqualsXmlString($expect, $feed->render());
    }

    public function testRender_with_japanese()
    {
        $feed = new Feed();
        $xml1 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>日本語1</title></channel>');
        $xml2 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>日本語2</title></channel>');
        $xml3 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>日本語3</title></channel>');
        $channel1 = $this->getMock($this->channelInterface);
        $channel1->expects($this->once())->method('asXML')->will($this->returnValue($xml1));
        $channel2 = $this->getMock($this->channelInterface);
        $channel2->expects($this->once())->method('asXML')->will($this->returnValue($xml2));
        $channel3 = $this->getMock($this->channelInterface);
        $channel3->expects($this->once())->method('asXML')->will($this->returnValue($xml3));
        $this->reveal($feed)->attr('channels', [$channel1, $channel2, $channel3]);
        $expect = <<< 'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
  <channel>
    <title>日本語1</title>
  </channel>
  <channel>
    <title>日本語2</title>
  </channel>
  <channel>
    <title>日本語3</title>
  </channel>
</rss>

XML;
        $this->assertSame($expect, $feed->render());

    }

    public function test__toString()
    {
        $feed = new Feed();
        $xml1 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>channel1</title></channel>');
        $xml2 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>channel2</title></channel>');
        $xml3 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>channel3</title></channel>');
        $channel1 = $this->getMock($this->channelInterface);
        $channel1->expects($this->once())->method('asXML')->will($this->returnValue($xml1));
        $channel2 = $this->getMock($this->channelInterface);
        $channel2->expects($this->once())->method('asXML')->will($this->returnValue($xml2));
        $channel3 = $this->getMock($this->channelInterface);
        $channel3->expects($this->once())->method('asXML')->will($this->returnValue($xml3));
        $this->reveal($feed)->attr('channels', [$channel1, $channel2, $channel3]);
        $expect = '<?xml version="1.0" encoding="UTF-8" ?>
            <rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
                <channel><title>channel1</title></channel>
                <channel><title>channel2</title></channel>
                <channel><title>channel3</title></channel>
            </rss>
        ';
        $this->assertXmlStringEqualsXmlString($expect, strval($feed));
    }
}
