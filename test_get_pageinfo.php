<?php

// get the page title and headers

// setup variables and arrays
$url = 'https://www.ebay.com/itm/274983985114';
$title = '';
$condition = '';
$LH_ItemCondition = 0;
$links = [];

// require the main import class
require_once(dirname(__FILE__) . '/classes/class.digdeep.php');

// assign object for class import
$digdeep = new digdeep;

// get the html
$html = $digdeep->gethtml($url);

// get page title
$title = $digdeep->page_title($html);

// get condition
$condition = $digdeep->get_condition($html);

// get lh itemcondition
switch ($condition) {
	case 'New':
		$LH_ItemCondition = 11;
	case 'New Other':
		$LH_ItemCondition = 1500;
	case 'Brand New':
		$LH_ItemCondition = 1000;
	case 'Used':
		$LH_ItemCondition = 12;
	case 'Not Specified':
		$LH_ItemCondition = 10;
	case 'Like New':
		$LH_ItemCondition = 2750;
	case 'Very Good':
		$LH_ItemCondition = 4000;
	case 'Good':
		$LH_ItemCondition = 5000;
	case 'Acceptable':
		$LH_ItemCondition = 6000;
	case 'Parts Only':
		$LH_ItemCondition = 7000;
	default:
		$LH_ItemCondition = 12;
}	
	

// get country/region from url
$region = $digdeep->ip_info($url, 'Country Code');

// get top 10 other titles same condition
$url = 'https://www.ebay.com/sch/i.html?_from=R40&_trksid=p2380057.m570.l1313&_nkw='.urlencode($title).'&LH_ItemCondition='.$LH_ItemCondition;

// get html for this top ten
$html = $digdeep->gethtml($url);

// get all list links for this
$links = $digdeep->list_links($html);

// now show results
echo 'URL: ['.$url.']<br/>';
echo 'Title: ['.$title.']<br/>';
echo 'Condition: ['.$condition.']<br/>';
echo 'LH_ItemCondition: ['.$LH_ItemCondition.']<br/>';
echo 'Region: ['.$region.']<br/>';

print_r($links);

?>