<?php

namespace Suin\RSSWriter;

class ItemTest extends \XoopsUnit\TestCase
{
	private $channelInterface = '\Suin\RSSWriter\ChannelInterface';

	public function testTitle()
	{
		$title = uniqid();
		$item = new Item();
		$this->assertSame($item, $item->title($title));
		$this->assertAttributeSame($title, 'title', $item);
	}

	public function testUrl()
	{
		$url = uniqid();
		$item = new Item();
		$this->assertSame($item, $item->url($url));
		$this->assertAttributeSame($url, 'url', $item);
	}

	public function testDescription()
	{
		$description = uniqid();
		$item = new Item();
		$this->assertSame($item, $item->description($description));
		$this->assertAttributeSame($description, 'description', $item);
	}

	public function testCategory()
	{
		$category = uniqid();
		$item = new Item();
		$this->assertSame($item, $item->category($category));
		$this->assertAttributeSame(array(
			array($category, null),
		), 'categories', $item);
	}

	public function testCategory_with_domain()
	{
		$category = uniqid();
		$domain   = uniqid();
		$item = new Item();
		$this->assertSame($item, $item->category($category, $domain));
		$this->assertAttributeSame(array(
			array($category, $domain),
		), 'categories', $item);
	}

	public function testGuid()
	{
		$guid = uniqid();
		$item = new Item();
		$this->assertSame($item, $item->guid($guid));
		$this->assertAttributeSame($guid, 'guid', $item);
	}

	public function testGuid_with_permalink()
	{
		$item = new Item();
		$item->guid('guid', true);
		$this->assertAttributeSame(true, 'isPermalink', $item);

		$item->guid('guid', false);
		$this->assertAttributeSame(false, 'isPermalink', $item);

		$item->guid('guid'); // default
		$this->assertAttributeSame(false, 'isPermalink', $item);
	}

	public function testPubDate()
	{
		$pubDate = mt_rand(1000000, 9999999);
		$item = new Item();
		$this->assertSame($item, $item->pubDate($pubDate));
		$this->assertAttributeSame($pubDate, 'pubDate', $item);
	}

	public function testAppendTo()
	{
		$item = new Item();
		$channel = $this->getMock($this->channelInterface);
		$channel->expects($this->once())->method('addItem')->with($item);
		$this->assertSame($item, $item->appendTo($channel));
	}

	public function testAsXML()
	{
		$now = time();
		$nowString = date(DATE_RSS, $now);

		$data = array(
			'title'       => "Venice Film Festival Tries to Quit Sinking",
			'url'         => 'http://nytimes.com/2004/12/07FEST.html',
			'description' => "Some of the most heated chatter at the Venice Film Festival this week was about the way that the arrival of the stars at the Palazzo del Cinema was being staged.",
			'categories'  => array(
				array("Grateful Dead", null),
				array("MSFT", 'http://www.fool.com/cusips'),
			),
			'guid' => "http://inessential.com/2002/09/01.php#a2",
			'isPermalink' => true,
			'pubDate' => $now,
		);

		$item = new Item();

		foreach ( $data as $key => $value )
		{
			$this->reveal($item)->attr($key, $value);
		}

		$expect ="
		<item>
			<title>{$data['title']}</title>
			<link>{$data['url']}</link>
			<description>{$data['description']}</description>
			<category>{$data['categories'][0][0]}</category>
			<category domain=\"{$data['categories'][1][1]}\">{$data['categories'][1][0]}</category>
			<guid isPermaLink=\"true\">{$data['guid']}</guid>
			<pubDate>{$nowString}</pubDate>
		</item>
		";
		$this->assertXmlStringEqualsXmlString($expect, $item->asXML()->asXML());
	}

	public function testAsXML_test_Japanese()
	{
		$now = time();
		$nowString = date(DATE_RSS, $now);

		$data = array(
			'title'       => "日本語",
			'url'         => 'http://nytimes.com/2004/12/07FEST.html',
			'description' => "Some of the most heated chatter at the Venice Film Festival this week was about the way that the arrival of the stars at the Palazzo del Cinema was being staged.",
		);

		$item = new Item();

		foreach ( $data as $key => $value )
		{
			$this->reveal($item)->attr($key, $value);
		}

		$expect = "
		<item>
			<title>{$data['title']}</title>
			<link>{$data['url']}</link>
			<description>{$data['description']}</description>
		</item>
		";

		$this->assertXmlStringEqualsXmlString($expect, $item->asXML()->asXML());
	}

	public function test_with_amp()
	{
		$item = new Item();
		$item
			->title('test&test')
			->url('url&url')
			->description('desc&desc');
		$expect = '<?xml version="1.0" encoding="UTF-8"?>
<item><title>test&amp;test</title><link>url&amp;url</link><description>desc&amp;desc</description></item>
';

		$this->assertSame($expect, $item->asXML()->asXML());
	}

	public function test_fail_safe_against_invalid_string()
	{
		$item = new Item();
		$item
			->title("test\0test")
			->url("url\0test")
			->description("desc\0desc");
		$expect = '<?xml version="1.0" encoding="UTF-8"?>
<item><title>test</title><link>url</link><description>desc</description></item>
';

		$this->assertSame($expect, $item->asXML()->asXML());
	}
}
