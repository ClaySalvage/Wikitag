<?php

declare(strict_types=1);

namespace wongery\wikitext\listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

function debug_to_console($data)
{
	$output = $data;
	if (is_array($output))
		$output = implode(',', $output);

	echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

class main implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return ['core.text_formatter_s9e_configure_after' => 'onConfigure'];
	}



	public function onConfigure($event)
	{
		$configurator = $event['configurator'];
		var_dump($configurator);
		debug_to_console($configurator);
		if (!isset($configurator->BBCodes['WIKI'], $configurator->tags['WIKI'])) {
			return;
		}

		// Declare the height and width attributes
		$tag = $configurator->tags['WIKI'];
		echo "***********";
		var_dump($tag);
		exit(1);
		/*
		foreach (['height', 'width'] as $attrName)
		{
			if (isset($tag->attributes[$attrName]))
			{
				continue;
			}

			$attribute = $tag->attributes->add($attrName);
			$attribute->filterChain->append('#uint');
			$attribute->required = false;
		}

		// Reparse the default attribute's value as a pair of dimensions
		$configurator->BBCodes['IMG']->defaultAttribute = 'dimensions';
		$tag->attributePreprocessors->add(
			$configurator->BBCodes['IMG']->defaultAttribute,
			'/^(?<width>\\d+),(?<height>\\d+)/'
		);

		// Preserve the ability to use the default attribute to specify the URL
		$tag->attributePreprocessors->add(
			$configurator->BBCodes['IMG']->defaultAttribute,
			'/^(?!\\d+,\\d+)(?<src>.*)/'
		);

		// Update the template
		$dom = $tag->template->asDOM();
		foreach ($dom->query('//img') as $img)
		{
			$img->prependXslCopyOf('@width');
			$img->prependXslCopyOf('@height');
		}
		$dom->saveChanges();
	}    */
	}
}
