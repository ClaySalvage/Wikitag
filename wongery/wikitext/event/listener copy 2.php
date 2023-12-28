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
		// Get the BBCode configurator
		$configurator = $event['configurator'];

		// Let's unset any existing BBCode that might already exist
		unset($configurator->BBCodes['wiki']);
		unset($configurator->tags['wiki']);

		// We're going to use a custom filter, so...
		$configurator->attributeFilters->set('#wikitext', __CLASS__ . '::parse_wikitext');
		$configurator->BBCodes->bbcodeMonkey->allowedFilters[] = 'wikitext';

		// Let's create the new BBCode
		$configurator->BBCodes->addCustom(
			'[wiki]{WIKITEXT}[/wiki]',
			'{WIKITEXT}'
			// '<span class="wikitext">{WIKITEXT}</span>'
		);

		// $event['configurator']->tags['WIKI']->filterChain
		// 	->append([__CLASS__, 'parse_wikitext']);
		// debug_log("AAAG");
		// debug_log($event);
		// var_dump($event['configurator']->tags['QUOTE']);
		// var_dump($event['configurator']->tags['WIKI']);
		// var_dump($event['configurator']->tags['WIKI']->filterChain[0]);
		// var_dump($event['configurator']->tags['WIKI']->filterChain[2]);
		// debug_log($event['configurator']->tags);
		// debug_log($event['configurator']->tags['QUOTE']);
		// debug_log($event['configurator']->tags['WIKI']);
		// debug_log($event['configurator']->tags['quote']);
		// debug_log($event['configurator']->tags['wiki']);
		// $event['configurator']->tags['WIKI']->filterChain->append([__CLASS__, 'parse_wikitext']);
		// $event['configurator']->tags['WIKI']->filterChain
		// 	->append([__CLASS__, 'parse_wikitext']);
		// $event['configurator']->tags['wiki']->filterChain
		// 	->append([__CLASS__, 'parse_wikitext']);
	}

	// static public function parse_wikitext(\s9e\TextFormatter\Parser\Tag $tag)
	static public function parse_wikitext($value)
	{
		return '<span class="wikitext">' . $value . '</span>';
		// $configurator = $event['configurator'];
		// debug_log($tag);
		// debug_log("***********");
		// var_dump($tag);
		// $tag = $configurator->tags['WIKI'];
		// $tag->setAttribute('testing', 'testing');
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
