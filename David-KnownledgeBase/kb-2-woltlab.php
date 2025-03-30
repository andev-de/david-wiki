<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);

function create_wiki_page($kb) {
	$sessionNumber = 'wsc_583127_user_session';
	$sessionValue  = '0f2065ffe1a2e49c5b5872bda68a9b9a7748f628c6f7f0f58250aa70e269c971-AXlT19l%2F0JZfnDXFyJr5PsJJMPGi0Q%3D%3D';
	$xsrfToken = 'd90aaed67151a6eb8e16f7dc868c77313c5390c0923bbb667f8a05ab7ad94211-ZlXsRn634PD%2BuiAgu5gEzA%3D%3D';
	$cookieValue = $sessionNumber.'='.$sessionValue.'; XSRF-TOKEN='.$xsrfToken;
	parse_str('t='.$xsrfToken, $tokenArr);

	

	// $article  = "<h1>".$kb['kbid']." - ".$kb['title']."</h1>";
	$article = "aus Tobit KB importiert - vom ".$kb['date'];
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
	$post_data['t'] = $tokenArr['t'];

	// print_r($post_data); exit;

	$encodedData = http_build_query($post_data);

	// echo $encodedData; exit;

	echo "generating ",$kb['kbid'],"... ";

	// URL to send the POST request to
	$url = "https://www.david-forum.de/wiki/entry-add/";

	$cookieFile = __DIR__ . "/cookies.txt";

	// Initialize cURL session
	$ch = curl_init($url);

	// Set cURL options
	curl_setopt($ch, CURLOPT_POST, true); // Use POST method
	curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData); // Encode data
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
	// curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile); // Save cookies to file
	// curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile); // Load cookies from file

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

	// curl_setopt($ch, CURLOPT_VERBOSE, true);

	// Execute cURL request
	$response = curl_exec($ch);

	// Check for errors
	if (curl_errno($ch)) {
		echo 'Curl error: ' . curl_error($ch);
		curl_close($ch);
		exit;
	}

	// Close cURL session
	curl_close($ch);

	if (empty($response)) {
		$status = "Erfolgreich";
	}
	else {
		$pfind = '<small class="innerError">';
		$pstart = strpos($response, $pfind);
		if ($pstart !== false) {
			$pstart = $pstart + strlen($pfind);
			$pend = strpos($response, '</', $pstart + 1);
			$plen = $pend - $pstart;
			// echo $pstart,"-",$pend,"=",$plen,"\n";
			$status = trim(substr($response, $pstart, $plen));
		}
		else {
			$pfind = '<woltlab-core-notice type="error">';
			$pstart = strpos($response, $pfind);
			if ($pstart !== false) {
				$pstart = $pstart + strlen($pfind);
				$pend = strpos($response, '</', $pstart + 1);
				$plen = $pend - $pstart;
				// echo $pstart,"-",$pend,"=",$plen,"\n";
				$status = trim(substr($response, $pstart, $plen));
			}
			else {
				$pfind = '<p class="exceptionTitle">';
				$pstart = strpos($response, $pfind);
				if ($pstart !== false) {
					$pstart = $pstart + strlen($pfind);
					$pend = strpos($response, '</', $pstart + 1);
					$plen = $pend - $pstart;
					// echo $pstart,"-",$pend,"=",$plen,"\n";
					$status = trim(substr($response, $pstart, $plen));
				}
				else {
					file_put_contents(dirname(__FILE__).'/response.html', $response);
					die('xxxx');
				}
			}
		}
	}

	echo "[",$status,"]\n";

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

	$link_id = intval(str_replace('.','',substr($kb['kbid'], 3)));
	// echo $link_id,"\n";

	// if ($link_id <= 10764)

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
	if (strlen($kb['date']) == 9)
		$kb['date'] = '0'.$kb['date'];

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
	$pend = strpos($fdata, '</td>', $pstart - 1);
	$plen = $pend - $pstart;
	// echo $pstart,"-",$pend,"=",$plen,"\n";
	$kb['answer'] = str_replace($what, $with, utf8_encode(substr($fdata, $pstart, $plen)));

	$kb['problem'] = strip_tags($kb['problem'], '<br><p><b><i><em><hr><table><tr><td><th><ol><ul><li>');
	$kb['answer'] = strip_tags($kb['answer'], '<br><p><b><i><em><hr><table><tr><td><th><ol><ul><li>');

	// echo print_r($kb, true),"\n";

	return $kb;
}

// $files = @glob(dirname(__FILE__).'/kbase/*.html');
// $files = @glob(dirname(__FILE__).'/kbase/Q-100042.html');
// $files = @glob(dirname(__FILE__).'/kbase/Q-10003*.html');
// $files = @glob(dirname(__FILE__).'/kbase/Q-11000*.html');
$files = @glob(dirname(__FILE__).'/kbase/Q-106893.html');
// $files = @glob(dirname(__FILE__).'/kbase/Q-11076*.html');

$cnt = 0;
$max = 1;

foreach ($files as $file) {
	$kb = convert_file_typeA($file);
	create_wiki_page($kb);
	$cnt++;

	if ($cnt >= $max)
		exit;
}