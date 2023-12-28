<?php

namespace wongery\wikitext\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

define("WIKISTARTTAG", '<span class="wikitext">');
define("WIKIENDTAG", "</span>");
define("ALTSTARTTAG", "<span");

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
		return [
			'core.text_formatter_s9e_configure_after' => 'configure_wikitext',
			'core.text_formatter_s9e_render_after' => 'parse_wikitext'
		];
	}

	public function configure_wikitext($event)
	{
		$configurator = $event['configurator'];

		// Let's unset any existing BBCode that might already exist
		unset($configurator->BBCodes['wiki']);
		unset($configurator->tags['wiki']);

		// We're going to use a custom filter, so...
		$configurator->attributeFilters->set('#wikitext', __CLASS__ . '::parse_wikitext');
		$configurator->BBCodes->bbcodeMonkey->allowedFilters[] = 'wikitext';

		// Let's create the new BBCode
		$configurator->BBCodes->addCustom(
			'[wiki]{TEXT}[/wiki]',
			'<span class="wikitext">{TEXT}</span>'
		);
		$tag = $event['configurator']->tags['WIKI'];
		$tag->rules->ignoreTags();
	}

	public function parse_wikitext($event)
	{
		$endpoint = "http://www.virtualwongery.com/w/api.php";
		// $endpoint = "https://www.wongery.com/w/api.php";
		// I tried to get it to read this from composer.json, but no luck so far...
		// You'll have to change it manually.  Sorry.
		// You also have to set $wgEnableScaryTranscluding to true in your
		// MediaWiki LocalSettings.php file.

		if (strpos($event['html'], WIKISTARTTAG) === false)
			return true;
		$newstring = '';
		$oldstring = $event['html'];
		while (($pos = strpos($oldstring, WIKISTARTTAG)) !== false) {
			$newstring .= substr($oldstring, 0, $pos);
			$oldstring = substr($oldstring, $pos + strlen(WIKISTARTTAG));
			$wikitext = "";
			$pos = strpos($oldstring, WIKIENDTAG);
			// Broken for nested spans inside wikitext, but that's not an issue... yet.
			while (
				strpos($oldstring, ALTSTARTTAG) !== false &&
				(strpos($oldstring, ALTSTARTTAG) < $pos)
			) {
				$wikitext .= substr($oldstring, 0, $pos + strlen(WIKIENDTAG));
				$oldstring = substr($oldstring, $pos + strlen(WIKIENDTAG));
				$pos = strpos($oldstring, WIKIENDTAG);
			}
			$wikitext .= substr($oldstring, 0, $pos);
			$oldstring = substr($oldstring, $pos + strlen(WIKIENDTAG));
			$newstring .= MWParse($wikitext, $endpoint);
		}
		$newstring .= $oldstring;

		$event['html'] = $newstring;
	}
}

function MWParse($MWtext, $endPoint)
{
	$params = [
		"action" => "parse",
		"contentmodel" => "wikitext",
		"text" => $MWtext,
		"format" => "json",
	];
	$url = $endPoint;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// COMMENT THE FOLLOWING OUT FOR PRODUCTION VERSION
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

	$output = curl_exec($ch);
	if (curl_errno($ch)) echo "<h1>ERROR :" . curl_error($ch) . "</h1>";
	curl_close($ch);

	$parseresult = json_decode($output, true);
	return $parseresult["parse"]["text"]["*"];
}
