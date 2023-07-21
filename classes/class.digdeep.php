<?php

class digdeep {

	public function list_links(string $html) {

		// check if html is empty
		if (empty($html)) {
			// exit cuz no url no data
			return 'html content is missing';
		}

		// create new dom object based on html
		$dom = new DOMDocument;
		libxml_use_internal_errors(true);

		$html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html);
		$dom->strictErrorChecking = false;
		$dom->loadHTML($html);
		libxml_clear_errors();

		$xpath = new DOMXpath($dom);
		// get href by class s-item__link		
		$hrefs = $xpath->query('//a/@href[contains(@class, "s-item__link")]'); 
		
		// create array & variables
		$links = [];
		$counter = 0;
		
		// loop array
		foreach ($hrefs as $href) {
			
			// get value and text
			$links[$counter]['text'] = $href->text;
			$links[$counter]['href'] = substr($href->value, 0, 37);
			
			// increment counter
			$counter++;

		}
		
		// now return links array
		return $links;
			
	}

	public function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
		$output = null;
		if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
			$ip = $_SERVER["REMOTE_ADDR"];
			if ($deep_detect) {
				if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
					$ip = $_SERVER['HTTP_CLIENT_IP'];
			}
		}
		$purpose    = str_replace(["name", "\n", "\t", " ", "-", "_"], null, strtolower(trim($purpose)));
		$support    = ["country", "countrycode", "state", "region", "city", "location", "address"];
		$continents = [
			"AF" => "Africa",
			"AN" => "Antarctica",
			"AS" => "Asia",
			"EU" => "Europe",
			"OC" => "Australia (Oceania)",
			"NA" => "North America",
			"SA" => "South America"
		];
		if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
			$ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
			if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
				switch ($purpose) {
					case "location":
						$output = [
							"city"           => @$ipdat->geoplugin_city,
							"state"          => @$ipdat->geoplugin_regionName,
							"country"        => @$ipdat->geoplugin_countryName,
							"country_code"   => @$ipdat->geoplugin_countryCode,
							"continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
							"continent_code" => @$ipdat->geoplugin_continentCode
						];
						break;
					case "address":
						$address = [$ipdat->geoplugin_countryName];
						if (@strlen($ipdat->geoplugin_regionName) >= 1)
							$address[] = $ipdat->geoplugin_regionName;
						if (@strlen($ipdat->geoplugin_city) >= 1)
							$address[] = $ipdat->geoplugin_city;
						$output = implode(", ", array_reverse($address));
						break;
					case "city":
						$output = @$ipdat->geoplugin_city;
						break;
					case "state":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "region":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "country":
						$output = @$ipdat->geoplugin_countryName;
						break;
					case "countrycode":
						$output = @$ipdat->geoplugin_countryCode;
						break;
				}
			}
		}
		return $output;
	}	

	public function get_iframesrc(string $html){

		// check if html is empty
		if (empty($html)) {
			// exit cuz no url no data
			return 'html content is missing';
		}

		// setup variables
		$iframe_src = '';

		// create new dom object based on html
		$dom = new DOMDocument;
		libxml_use_internal_errors(true);
		// libxml_parsehuge(true);

		$html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html);
		$dom->strictErrorChecking = false;
		$dom->loadHTML($html);
		libxml_clear_errors();

		$xpath = new DOMXpath($dom);
		foreach($xpath->query('//iframe') as $iframe) {
			$iframe_src = $iframe->getAttribute('src');
		}
		
		// return src
		return $iframe_src;
		
	}

	public function get_price(string $html){

		// check if html is empty
		if (empty($html)) {
			// exit cuz no url no data
			return 'html content is missing';
		}

		// create new dom object based on html
		$dom = new DOMDocument;
		libxml_use_internal_errors(true);
		// libxml_parsehuge(true);
		$html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html);
		$dom->strictErrorChecking = false;
		$dom->loadHTML($html);
		libxml_clear_errors();

		// specify this class
		$classname = 'u-fil condText';
		$price = '';

		// xpath to get the condition
		$xpath = new DOMXpath($dom);
		foreach($xpath->query('//span[contains(@itemprop,"price")]') as $div){
			
			// get the price from xpath node content
			$price =  $div->textContent;

		}

		// strip non-numeric
		$price = preg_replace("/[^0-9.]/", "", $price);

		// return price
		return $price;
		
	}


	public function get_condition(string $html){

		// check if html is empty
		if (empty($html)) {
			// exit cuz no url no data
			return 'html content is missing';
		}

		// create dom document
		$dom = new DOMDocument;
		libxml_use_internal_errors(true);
		$html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html);
		$dom->strictErrorChecking = false;
		$dom->loadHTML($html);
		$xpath = new DOMXPath($dom);
		libxml_clear_errors();
		$classname = 'u-fil condText';

		// xpath to get the condition
		foreach($xpath->query('//div[contains(@class,"condText")]') as $div){
			
			// get the condition from xpath node content
			$condition =  $div->textContent;

		}

		// return condition
		return $condition;
		
	}

	public function page_title(string $html): ?string {
		
		// check if html is empty
		if (empty($html)) {
			// exit cuz no url no data
			return 'html content is missing';
		}

		// get page title from passed html
        $res = preg_match("/<title>(.*)<\/title>/siU", $html, $title_matches);
        if (!$res) 
            return null; 

        // Clean up title: remove EOL's and excessive whitespace.
        $title = preg_replace('/\s+/', ' ', $title_matches[1]);
        $title = trim($title);
		
		// remove the ebay part of the title
		$title = str_replace(' | eBay','', $title);
		
		// return it
        return $title;
    }

	public function getiframesrc(string $url) {

		// check if html is empty
		if (empty($html)) {
			// exit cuz no url no data
			return 'html content is missing';
		}
	
		// setup variables
		$iframe_src = '';

		// create dom document
		$dom = new DOMDocument;
		libxml_use_internal_errors(true);
		$html = file_get_contents($url);
		$html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html);
		$dom->strictErrorChecking = false;
		$dom->loadHTML($html);
		libxml_clear_errors();

		$xpath = new DOMXpath($dom);
		foreach($xpath->query('//iframe') as $iframe) {
			$iframe_src = $iframe->getAttribute('src');
		}
		
		// return src
		return $iframe_src;
	}
	
	public function getdesc($iframe_src): string {

		// create dom document
		$dom = new DOMDocument;
		libxml_use_internal_errors(true);
		$html = file_get_contents($iframe_src);
		$html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html);
		$dom->strictErrorChecking = false;
		$dom->loadHTML($html);
		$xpath = new DOMXPath($dom);
		libxml_clear_errors();
		$classname="Description";
		$results = $xpath->query("//*[@class='" . $classname . "']");
		
		// get div content
		$desc = $results->item(0)->nodeValue;
		
		// remove the word description
		$desc = str_replace('Description', '', $desc);
		
		// escape html special characters
		$desc = htmlentities($desc, ENT_QUOTES);
		
		// return description
		return $desc;

		
	}

	// get the html of an url and return it
	/**
	 * @return bool|null|string
	 */
	public function gethtml(string $url,$start='',$end='') {

		// check if url is empty
		if (empty($url)) {
			return;
		}
		
		// grab content via curl
		$ch = curl_init();
		$options = [
			CURL_IPRESOLVE_V4 => true,
			CURLOPT_ENCODING => 'gzip, deflate',
			CURLOPT_FAILONERROR => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HEADER => true,
			CURLOPT_IPRESOLVE => true,
			CURLOPT_NOBODY => false,
			CURLOPT_REFERER => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_URL => $url,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']
		];

		// setup options
		curl_setopt_array($ch, $options);
		
		// get the data
		$html = curl_exec($ch);
		
		// close connection
		curl_close($ch);

		// check if html is empty
		if (empty($html)) {
			return;
		}
		
		// check if start and end are empty
		if (!empty($start) && !empty($end)) {

			// get the start position
			$sp = stripos($html, $start);
			
			// get the end position
			$ep = stripos($html, $end, $sp);
			
			// get the length of this new string
			$length = abs($sp - $ep);
			
			// now streamlined html1			
			$html = substr($html, $sp - 1, $length);

		}

		// now return data
		return $html;
		
	}

	/**
	 * @return string[]
	 *
	 * @psalm-return list<string>
	 */
	public function iframephotos($html): array {

		// setup array
		$images = [];
		
		// get html inside div content gallThumbs
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$tags = $doc->getElementsByTagName('img');

		// loop thru tags
		foreach ($tags as $tag) {
			
			// get url of img src
			$img_url = $tag->getAttribute('src');
			
			// check if url starts with https://kyozoufs.blob.core.windows.net - 38 chars
			// check if already exists in array
			if (substr( $img_url, 0, 38 ) === "https://kyozoufs.blob.core.windows.net" && !in_array($img_url, $images)) {
				
				// echo $img_url.'<br/>';
				$images[] = $img_url;
				
			}
			
		}

		// return images
		return $images;
		
		
	}


}

?>