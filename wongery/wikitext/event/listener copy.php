<?php

namespace wongery\wikitext\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

// function debug_to_console($data)
// {
// 	$output = $data;
// 	if (is_array($output))
// 		$output = implode(',', $output);

// 	// echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
// 	echo "<script>console.log('Debug Objects: " . json_encode($output) . "' );</script>";
// }

function debug_log($object = null, $label = null)
{
	$message = json_encode($object, JSON_PRETTY_PRINT);
	$label = "Debug" . ($label ? " ($label): " : ': ');
	echo "<script>console.log(\"$label\", $message);</script>";
}

class listener implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		// return ['core.text_formatter_s9e_configure_after' => 'onConfigure'];
		return ['core.text_formatter_s9e_configure_after' => 'configure_wikitext'];
	}

	public function configure_wikitext($event)
	{
		debug_log("AAAG");
		debug_log($event['configurator']);
		debug_log($event['configurator']->tags['QUOTE']);
		debug_log($event['configurator']->tags['WIKI']);
		debug_log($event['configurator']->tags['quote']);
		debug_log($event['configurator']->tags['wiki']);
		$event['configurator']->tags['WIKI']->filterChain->append([__CLASS__, 'parse_wikitext']);
		$event['configurator']->tags['WIKI']->filterChain
			->append([__CLASS__, 'parse_wikitext']);
		$event['configurator']->tags['wiki']->filterChain
			->append([__CLASS__, 'parse_wikitext']);
	}

	static public function parse_wikitext(\s9e\TextFormatter\Parser\Tag $tag)
	{
		debug_log($tag);
		debug_log("***********");
	}

	// public function onConfigure($event)
	// {
	// 	$configurator = $event['configurator'];
	// 	// var_dump($configurator);
	// 	debug_log($configurator);
	// 	if (!isset($configurator->BBCodes['WIKI'], $configurator->tags['WIKI'])) {
	// 		return;
	// 	}

	// 	// Declare the height and width attributes
	// 	$tag = $configurator->tags['WIKI'];
	// 	// echo "***********";
	// 	// var_dump($tag);
	// 	debug_log($tag);
	// 	// exit(1);
	// 	/*
	// 	foreach (['height', 'width'] as $attrName)
	// 	{
	// 		if (isset($tag->attributes[$attrName]))
	// 		{
	// 			continue;
	// 		}

	// 		$attribute = $tag->attributes->add($attrName);
	// 		$attribute->filterChain->append('#uint');
	// 		$attribute->required = false;
	// 	}

	// 	// Reparse the default attribute's value as a pair of dimensions
	// 	$configurator->BBCodes['IMG']->defaultAttribute = 'dimensions';
	// 	$tag->attributePreprocessors->add(
	// 		$configurator->BBCodes['IMG']->defaultAttribute,
	// 		'/^(?<width>\\d+),(?<height>\\d+)/'
	// 	);

	// 	// Preserve the ability to use the default attribute to specify the URL
	// 	$tag->attributePreprocessors->add(
	// 		$configurator->BBCodes['IMG']->defaultAttribute,
	// 		'/^(?!\\d+,\\d+)(?<src>.*)/'
	// 	);

	// 	// Update the template
	// 	$dom = $tag->template->asDOM();
	// 	foreach ($dom->query('//img') as $img)
	// 	{
	// 		$img->prependXslCopyOf('@width');
	// 		$img->prependXslCopyOf('@height');
	// 	}
	// 	$dom->saveChanges();
	// }    */
	// }
}
