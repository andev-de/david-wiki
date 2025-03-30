<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);

define('WSC_USER_SESSION', '82935b05410c1e0aca3053f3433c04d-AaOau0LCnC0YA6lvdabYch3gu6VD0Q=');
define('XSRF_TOKEN', 'd90aaed67151a6eb8e16f7dc868c77313c5390c0923bbb667f8a05ab7ad94211-ZlXsRn634PD+uiAgu5gEzA==');

function create_wiki_page($kb) {
	$xsrfToken = 'd90aaed67151a6eb8e16f7dc868c77313c5390c0923bbb667f8a05ab7ad94211-ZlXsRn634PD+uiAgu5gEzA==';
	$tmpArr = http_build_query(array($xsrfToken));
	$cookieValue = 'wsc_583127_user_session='.WSC_USER_SESSION.'; XSRF-TOKEN='.XSRF_TOKEN;

	$article  = "<h1>".$kb['kbid']." - ".$kb['title']."</h1>";
	$article .= "aus Tobit KB importiert - vom ".$kb['date'];
	if (!empty($kb['link']))
		$article .= " - <a href='".$kb['link']."'>Beitrag bei Tobit</a>";
	$article .= "<hr><br>";

	$article .= "<b>Problem</b><hr><p>".$kb['problem']."</p><br><br><br>";
	$article .= "<b>Antwort</b><hr><p>".$kb['answer']."</p>";

	$post_data['subject'] = $kb['kbid'];
	$post_data['synonyms'] = '';
	$post_data['tags'][] = $kb['product'];
	$post_data['restrictedWriteAccess'] = '';
	$post_data['category'] = '5';
	$post_data['excerpt'] = $kb['kbid'].' - '.$kb['title'];
	$post_data['message'] = $article;
	// $post_data['messageAttachments_tmpHash'][] = 'c7e0c4c02faa2a0e4b43b01acb0fbf6457822191';
	// $post_data['isLinkedByParser'] = '1';
	$post_data['t'] = $xsrfToken;

	// print_r($post_data); exit;

	$encodedData = http_build_query($post_data);

	// echo $encodedData; exit;

	echo "generating ",$kb['kbid'],"...\n";

	// URL to send the POST request to
	$url = "https://www.david-forum.de/wiki/entry-add/";

	$cookieFile = __DIR__ . "/cookies.txt";

	// Initialize cURL session
	$ch = curl_init($url);

	// Set cURL options
	curl_setopt($ch, CURLOPT_POST, true); // Use POST method
	curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData); // Encode data
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile); // Save cookies to file
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile); // Load cookies from file

	// Optional headers (if required)
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:136.0) Gecko/20100101 Firefox/136.0',
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		'Accept-Language: de,en-US;q=0.7,en;q=0.3',
		'Content-Type: application/x-www-form-urlencoded',
		'Origin: https://www.david-forum.de',
		'Connection: keep-alive',
		'Referer: https://www.david-forum.de/wiki/entry-add/?categoryID=5',
		'Cookie: '.$cookieValue,
		'Upgrade-Insecure-Requests: 1',
		'Sec-Fetch-Dest: document',
		'Sec-Fetch-Mode: navigate',
		'Sec-Fetch-Site: same-origin',
		'Sec-Fetch-User: ?1',
		'Priority: u=0, i',
		'Pragma: no-cache',
		'Cache-Control: no-cache'
	]);

	// Execute cURL request
	$response = curl_exec($ch);

	// Check for errors
	if (curl_errno($ch)) {
		echo 'Curl error: ' . curl_error($ch);
	} else {
		echo 'Response: ' . $response;
	}

	// Close cURL session
	curl_close($ch);

	preg_match('/name="t" value="(.*?)"/', $response, $matches);
	$xsrfToken = $matches[1] ?? null;

	file_put_contents(dirname(__FILE__).'/response.html', $response);
}

function convert_file_typeA($file) {
	$kb = array();
	$fdata = file_get_contents($file);

	// $fdata = mb_convert_encoding($fdata, "ISO-8859-1", "UTF-8");

	$what = array(chr(195).chr(182));
	$with = array('ö');

	$pfind = '<div class="title">';
	$pstart = strpos($fdata, $pfind) + strlen($pfind);
	$pend = strpos($fdata, '</div>', $pstart + 1);
	$plen = $pend - $pstart;
	// echo $pstart,"-",$pend,"=",$plen,"\n";
	$kb['kbid'] = trim(substr($fdata, $pstart, $plen));

	if (substr($kb['kbid'], 0, 3) == 'Q-1') {
		// OLD Format

		$link_id = str_replace('.','',substr($kb['kbid'], 3));
		$kb['link'] = 'https://club.tobit.com/login/freekbarticle.asp?lang=ger&ArticleID='.$link_id;

		$pfind = '<b class=tabletext style="position:relative; top:-5px;">';
		$pstart = strpos($fdata, $pfind) + strlen($pfind);
		$pend = strpos($fdata, '</b>', $pstart + 1);
		$plen = $pend - $pstart;
		// echo $pstart,"-",$pend,"=",$plen,"\n";
		$kb['title'] = str_replace($what, $with, utf8_encode(substr($fdata, $pstart, $plen)));
	
		$pfind = '<b>Datum</b>';
		$pstart = strpos($fdata, $pfind) + strlen($pfind);
		$pstart = strpos($fdata, '<td class="tabletext">', $pstart + 1) + 22;
		$pend = strpos($fdata, '</td>', $pstart + 1);
		$plen = $pend - $pstart;
		// echo $pstart,"-",$pend,"=",$plen,"\n";
		$kb['date'] = trim(substr($fdata, $pstart, $plen));
	
		$pfind = '<b>Produkt</b>';
		$pstart = strpos($fdata, $pfind) + strlen($pfind);
		$pstart = strpos($fdata, '<td class="tabletext">', $pstart + 1) + 22;
		$pend = strpos($fdata, '</td>', $pstart + 1);
		$plen = $pend - $pstart;
		// echo $pstart,"-",$pend,"=",$plen,"\n";
		$kb['product'] = trim(substr($fdata, $pstart, $plen));
		// $kb['product'] = trim(str_replace('David','',substr($fdata, $pstart, $plen)));
		// if (empty($kb['product']))
		// 	$kb['product'] = 'David';
	
		$pfind = '<b>Problem</b>';
		$pstart = strpos($fdata, $pfind) + strlen($pfind);
		$pstart = strpos($fdata, '<td class="tabletext" style="padding-right:10px;">', $pstart + 1) + 50;
		$pend = strpos($fdata, '</td>', $pstart + 1);
		$plen = $pend - $pstart;
		// echo $pstart,"-",$pend,"=",$plen,"\n";
		$kb['problem'] = str_replace($what, $with, utf8_encode(substr($fdata, $pstart, $plen)));
	
		$pfind = '<b>Antwort</b>';
		$pstart = strpos($fdata, $pfind) + strlen($pfind);
		$pstart = strpos($fdata, '<td class="tabletext" style="padding-left:10px; padding-right:10px;">', $pstart + 1) + 69;
		$pend = strpos($fdata, '</td>', $pstart + 1);
		$plen = $pend - $pstart;
		// echo $pstart,"-",$pend,"=",$plen,"\n";
		$kb['answer'] = str_replace($what, $with, utf8_encode(substr($fdata, $pstart, $plen)));

		$kb['answer'] = strip_tags($kb['answer'], '<br><p><b><i><em><hr><table><tr><td>');
	}
	else {
		die('NEW');
	}



	// for ($i=0; $i<1680; $i++) {
	// 	$x = substr($kb['answer'], $i, 1);
	// 	echo $x," = ",ord($x),"\n";
	// }

	// $x = substr($kb['answer'], 5, 1);
	// echo $x," = ",ord($x);

	// echo $file_data,"\n";
	echo print_r($kb, true),"\n";

	return $kb;
}

// $files = @glob(dirname(__FILE__).'/kbase/*.html');
$files = @glob(dirname(__FILE__).'/kbase/Q-100035.html');
// $files = @glob(dirname(__FILE__).'/kbase/Q-10003*.html');
// $files = @glob(dirname(__FILE__).'/kbase/Q-11000*.html');

foreach ($files as $file) {
	$kb = convert_file_typeA($file);
	create_wiki_page($kb);
}