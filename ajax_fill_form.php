<?php

// ajax get the data fill the forms

// setup variables and arrays
$url = 'https://www.ebay.com/itm/274983985114';
$title = '';
$condition = '';
$price = 0.00;
$results = [];
$images = [];

// check for post data
if (isset($_POST['url']) && !empty($_POST['url'])) {
	
	// set report to this number
	$url = $_POST['url'];
	
}

// check for valid url or being empty
if (filter_var($url, FILTER_VALIDATE_URL) === false) {
	// exit cuz no url no data
    exit;
}

// check if url is empty
if (empty($url)) {
	// exit cuz no url no data
	exit;
}
	
// require the main import class
require_once(dirname(__FILE__) . '/classes/class.digdeep.php');

// assign object for class import
$digdeep = new digdeep;

// step zero get the html
$html = $digdeep->gethtml($url);

// step one get page title
$title = $digdeep->page_title($html);

// step two get condition
$condition = $digdeep->get_condition($html);

// step three get prices
$price = $digdeep->get_price($html);

// step four get iframe src
$iframe_src = $digdeep->get_iframesrc($html);

// step five get description from iframe src
$description = $digdeep->getdesc($iframe_src);

// step six get html from iframe_src
$new_html = $digdeep->gethtml($iframe_src);

// step seven get iframe images
$images = $digdeep->iframephotos($new_html);

// step eight get country/region from url
$region = $digdeep->ip_info($url, 'Country Code');

// final compile all results into an array
$results['title'] = $title;
$results['condition'] = $condition;
$results['price'] = $price;
$results['description'] = $description;
$results['region'] = $region;

// check each image
if (isset($images[0])) {
	
	// set image one
	$results['imageone'] = $images[0];

}
if (isset($images[1])) {
	
	// set image one
	$results['imagetwo'] = $images[1];

}
if (isset($images[2])) {
	
	// set image one
	$results['imagethree'] = $images[2];

}
if (isset($images[3])) {
	
	// set image one
	$results['imagefour'] = $images[3];

}
if (isset($images[4])) {
	
	// set image one
	$results['imagefive'] = $images[4];

}
if (isset($images[5])) {
	
	// set image one
	$results['imagesix'] = $images[5];

}

// return results
echo json_encode($results);

?>