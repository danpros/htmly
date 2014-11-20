<?php

namespace Suin\RSSWriter;

class SimpleXMLElement extends \SimpleXMLElement
{
	public function addChild($name, $value = null, $namespace = null)
	{
		if ( $value !== null and is_string($value) === true )
		{
			$value = str_replace('&', '&amp;', $value);
		}

		return parent::addChild($name, $value, $namespace);
	}
}
