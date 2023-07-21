<?php

// test get condition

// require the main import class
require_once(dirname(__FILE__) . '/classes/class.digdeep.php');

// assign object for class import
$digdeep = new digdeep;

// setup variables
$url = 'https://www.ebay.com/itm/154611055449';

// get dom object from function_exists
$html = file_get_contents($url);

// create new dom object based on html
$dom = new DOMDocument;
libxml_use_internal_errors(true);
$html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html);
$dom->strictErrorChecking = false;
$dom->loadHTML($html);
libxml_clear_errors();

$xpath = new DOMXpath($dom);

// get price 
$price = $digdeep->get_price($html);

// get iframe src
$iframe_src = $digdeep->get_iframesrc($html);

// debug
echo 'Url: ['.$url.']<br/>';
echo 'Price: ['.$price.']<br/>';
echo 'iFrame Src: ['.$iframe_src.']<br/>';

// get new html based on source
$new_html = $digdeep->gethtml($iframe_src);

// get html inside div content gallThumbs
$doc = new DOMDocument();
@$doc->loadHTML($new_html);
$tags = $doc->getElementsByTagName('img');
$images = [];

// loop thru tags
foreach ($tags as $tag) {
	
	// get url of img src
	$img_url = $tag->getAttribute('src');
	
	// check if url starts with https://kyozoufs.blob.core.windows.net - 38 chars
	// check if already exists in array
	if (substr( $img_url, 0, 38 ) === "https://kyozoufs.blob.core.windows.net" && !in_array($img_url, $images)) {
		
		echo $img_url.'<br/>';
		$images[] = $img_url;
		
	}
	
}


?>