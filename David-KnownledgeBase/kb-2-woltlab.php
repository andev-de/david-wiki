<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);

function create_wiki_page($info, $post_data) {
	$sessionNumber = 'wsc_583127_user_session';
	$sessionValue  = '0f2065ffe1a2e49c5b5872bda68a9b9a7748f628c6f7f0f58250aa70e269c971-AXlT19l%2F0JZfnDXFyJr5PsJJMPGi0Q%3D%3D';
	$xsrfToken = 'd90aaed67151a6eb8e16f7dc868c77313c5390c0923bbb667f8a05ab7ad94211-ZlXsRn634PD%2BuiAgu5gEzA%3D%3D';
	$cookieValue = $sessionNumber.'='.$sessionValue.'; XSRF-TOKEN='.$xsrfToken;
	parse_str('t='.$xsrfToken, $tokenArr);

	$post_data['t'] = $tokenArr['t'];

	// print_r($post_data); exit;

	$encodedData = http_build_query($post_data);

	// echo $encodedData; exit;

	echo $info;

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

function update_wiki_page($page_key, $info, $post_data) {
	$sessionNumber = 'wsc_583127_user_session';
	$sessionValue  = '0f2065ffe1a2e49c5b5872bda68a9b9a7748f628c6f7f0f58250aa70e269c971-AXlT19l%2F0JZfnDXFyJr5PsJJMPGi0Q%3D%3D';
	$xsrfToken = 'd90aaed67151a6eb8e16f7dc868c77313c5390c0923bbb667f8a05ab7ad94211-ZlXsRn634PD%2BuiAgu5gEzA%3D%3D';
	$cookieValue = $sessionNumber.'='.$sessionValue.'; XSRF-TOKEN='.$xsrfToken;
	parse_str('t='.$xsrfToken, $tokenArr);

	$post_data['t'] = $tokenArr['t'];

	// print_r($post_data); exit;

	$encodedData = http_build_query($post_data);

	// echo $encodedData; exit;

	echo $info;
	
	$url = "https://www.david-forum.de/wiki/entry-edit/".$page_key."/";
	
	// echo $url,"\n";

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

function create_kb_page($kb) {
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

	create_wiki_page("generating ".$kb['kbid']."... ", $post_data);
}

function create_rl_page($page) {
	$post_data['subject'] = 'Version '.$page['version'];
	$post_data['synonyms'] = '';
	$post_data['tags'][] = 'Version '.$page['version'];
	$post_data['restrictedWriteAccess'] = '';
	$post_data['category'] = '6';
	$post_data['excerpt'] = 'Release Notes zu Version '.$page['version'].' vom '.$page['date'];
	$post_data['message'] = $page['notes'];

	create_wiki_page("generating ReleaseNote ".$page['version']."... ", $post_data);
}

function update_rl_page($id, $page) {
	$post_data['subject'] = $page['rollout'];
	$post_data['synonyms'] = '';
	$post_data['tags'][] = $page['rollout'];
	$post_data['tags'][] = 'Version '.$page['version'];
	$post_data['restrictedWriteAccess'] = '';
	$post_data['category'] = '6';
	$post_data['excerpt'] = 'Release Notes zu '.$page['rollout'].' / Version '.$page['version'].' vom '.$page['date'];
	$post_data['message'] = $page['notes'];

	update_wiki_page($id.'-version-'.$page['version'], "updating ReleaseNote ".$page['version']." (".$id.")... ", $post_data);
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

function get_rollout_info($version, $release) {
	$infos[] = array('d' => '2013-07-30', 'r' => '224', 'v' => '2711');
	$infos[] = array('d' => '2013-11-25', 'r' => '225', 'v' => '2714');
	$infos[] = array('d' => '2013-12-14', 'r' => '226', 'v' => '2720');
	$infos[] = array('d' => '2014-01-24', 'r' => '227', 'v' => '2731');
	$infos[] = array('d' => '2014-04-11', 'r' => '228', 'v' => '2738');
	$infos[] = array('d' => '2014-07-11', 'r' => '229', 'v' => '2755');
	$infos[] = array('d' => '2014-08-22', 'r' => '230', 'v' => '2758');
	$infos[] = array('d' => '2014-10-31', 'r' => '231', 'v' => '2765');
	$infos[] = array('d' => '2014-11-07', 'r' => '232', 'v' => '2770');
	$infos[] = array('d' => '2014-12-01', 'r' => '233', 'v' => '2772');
	$infos[] = array('d' => '2014-12-04', 'r' => '234', 'v' => '2779');
	$infos[] = array('d' => '2014-10-31', 'r' => '235', 'v' => '2793');
	$infos[] = array('d' => '2015-04-15', 'r' => '236', 'v' => '2795');
	$infos[] = array('d' => '2015-04-10', 'r' => '237', 'v' => '2806');
	$infos[] = array('d' => '2015-05-27', 'r' => '238', 'v' => '2809');
	$infos[] = array('d' => '2015-06-25', 'r' => '239', 'v' => '2812');
	$infos[] = array('d' => '2015-07-29', 'r' => '240', 'v' => '2813');
	$infos[] = array('d' => '2015-08-04', 'r' => '241', 'v' => '2815');
	$infos[] = array('d' => '2015-08-14', 'r' => '242a', 'v' => '2818');
	$infos[] = array('d' => '2015-08-14', 'r' => '242b', 'v' => '2820');
	$infos[] = array('d' => '2015-08-14', 'r' => '242c', 'v' => '2832');
	$infos[] = array('d' => '2015-09-16', 'r' => '243', 'v' => '0000');
	$infos[] = array('d' => '2015-10-15', 'r' => '244', 'v' => '0000');
	$infos[] = array('d' => '2015-10-30', 'r' => '245', 'v' => '0000');
	$infos[] = array('d' => '2015-12-05', 'r' => '246', 'v' => '0000');
	$infos[] = array('d' => '2015-12-07', 'r' => '247', 'v' => '0000');
	$infos[] = array('d' => '2016-01-09', 'r' => '248', 'v' => '2844');
	$infos[] = array('d' => '2016-02-16', 'r' => '249', 'v' => '0000');
	$infos[] = array('d' => '2016-05-13', 'r' => '250', 'v' => '2864');
	$infos[] = array('d' => '2016-06-01', 'r' => '251', 'v' => '0000');
	$infos[] = array('d' => '2016-06-10', 'r' => '252', 'v' => '0000');
	$infos[] = array('d' => '2016-06-30', 'r' => '253', 'v' => '0000');
	$infos[] = array('d' => '2016-08-04', 'r' => '254', 'v' => '0000');
	$infos[] = array('d' => '2016-09-17', 'r' => '255', 'v' => '2884');
	$infos[] = array('d' => '2016-09-19', 'r' => '256', 'v' => '0000');
	$infos[] = array('d' => '2016-10-20', 'r' => '257', 'v' => '0000');
	$infos[] = array('d' => '2016-11-17', 'r' => '258', 'v' => '0000');
	$infos[] = array('d' => '2016-12-22', 'r' => '259', 'v' => '2909');
	$infos[] = array('d' => '2017-01-20', 'r' => '260', 'v' => '0000');
	$infos[] = array('d' => '2017-04-20', 'r' => '261', 'v' => '2924');
	$infos[] = array('d' => '2017-04-26', 'r' => '262', 'v' => '2925');
	$infos[] = array('d' => '2017-05-15', 'r' => '264', 'v' => '2927');
	$infos[] = array('d' => '2017-05-29', 'r' => '265', 'v' => '2931');
	$infos[] = array('d' => '2017-06-19', 'r' => '266', 'v' => '2936');
	$infos[] = array('d' => '2017-07-03', 'r' => '267', 'v' => '2940');
	$infos[] = array('d' => '2017-07-17', 'r' => '268', 'v' => '2941');
	$infos[] = array('d' => '2017-07-31', 'r' => '269', 'v' => '2947');
	$infos[] = array('d' => '2017-08-14', 'r' => '270', 'v' => '2952');
	$infos[] = array('d' => '2017-08-28', 'r' => '271', 'v' => '2956');
	$infos[] = array('d' => '2017-09-11', 'r' => '272', 'v' => '2958');
	$infos[] = array('d' => '2017-09-25', 'r' => '273', 'v' => '2961');
	$infos[] = array('d' => '2017-10-10', 'r' => '274', 'v' => '2963');
	$infos[] = array('d' => '2017-10-30', 'r' => '275', 'v' => '2965');
	$infos[] = array('d' => '2017-11-13', 'r' => '276', 'v' => '2967');
	$infos[] = array('d' => '2017-11-27', 'r' => '277', 'v' => '2970');
	$infos[] = array('d' => '2017-12-11', 'r' => '278', 'v' => '2971');
	$infos[] = array('d' => '2018-01-02', 'r' => '279', 'v' => '2972');
	$infos[] = array('d' => '2018-01-15', 'r' => '280', 'v' => '2974');
	$infos[] = array('d' => '2018-01-16', 'r' => '281', 'v' => '2975');
	$infos[] = array('d' => '2018-01-29', 'r' => '282', 'v' => '2976');
	$infos[] = array('d' => '2018-02-12', 'r' => '283', 'v' => '2979');
	$infos[] = array('d' => '2018-03-22', 'r' => '284', 'v' => '2980');
	$infos[] = array('d' => '2018-04-16', 'r' => '285', 'v' => '2981');
	$infos[] = array('d' => '2018-04-30', 'r' => '286', 'v' => '2983');
	$infos[] = array('d' => '2018-05-14', 'r' => '287', 'v' => '2987');
	$infos[] = array('d' => '2018-06-08', 'r' => '288', 'v' => '2993');
	$infos[] = array('d' => '2018-06-25', 'r' => '289', 'v' => '2998');
	$infos[] = array('d' => '2018-07-09', 'r' => '290', 'v' => '3002');
	$infos[] = array('d' => '2018-07-23', 'r' => '291', 'v' => '3003');
	$infos[] = array('d' => '2018-08-13', 'r' => '292', 'v' => '3006');
	$infos[] = array('d' => '2018-08-29', 'r' => '293', 'v' => '3007');
	$infos[] = array('d' => '2018-09-17', 'r' => '294', 'v' => '3018');
	$infos[] = array('d' => '2018-10-22', 'r' => '295', 'v' => '3025');
	$infos[] = array('d' => '2018-12-05', 'r' => '296', 'v' => '3041');
	$infos[] = array('d' => '2018-12-07', 'r' => '297', 'v' => '3042');
	$infos[] = array('d' => '2019-01-14', 'r' => '298', 'v' => '3045');
	$infos[] = array('d' => '2019-03-04', 'r' => '300', 'v' => '3050');
	$infos[] = array('d' => '2019-03-25', 'r' => '301', 'v' => '3053');
	$infos[] = array('d' => '2019-04-25', 'r' => '302', 'v' => '3054');
	$infos[] = array('d' => '2019-05-20', 'r' => '303', 'v' => '3061');
	$infos[] = array('d' => '2019-06-17', 'r' => '304', 'v' => '3065');
	$infos[] = array('d' => '2019-06-21', 'r' => '305', 'v' => '3066');
	$infos[] = array('d' => '2019-07-15', 'r' => '306', 'v' => '3072');
	$infos[] = array('d' => '2019-07-25', 'r' => '307', 'v' => '3076');
	$infos[] = array('d' => '2019-08-19', 'r' => '308', 'v' => '3081');
	$infos[] = array('d' => '2019-09-24', 'r' => '309', 'v' => '3092');
	$infos[] = array('d' => '2019-10-17', 'r' => '310', 'v' => '3101');
	$infos[] = array('d' => '2019-11-12', 'r' => '312', 'v' => '3112');
	$infos[] = array('d' => '2019-12-03', 'r' => '313', 'v' => '3119');
	$infos[] = array('d' => '2019-12-18', 'r' => '314', 'v' => '3132');
	$infos[] = array('d' => '2020-01-16', 'r' => '315', 'v' => '3139');
	$infos[] = array('d' => '2020-02-03', 'r' => '316', 'v' => '3148');
	$infos[] = array('d' => '2020-03-02', 'r' => '317', 'v' => '3159');
	$infos[] = array('d' => '2020-03-26', 'r' => '318', 'v' => '3164');
	$infos[] = array('d' => '2020-04-07', 'r' => '319', 'v' => '3167');
	$infos[] = array('d' => '2020-04-23', 'r' => '320', 'v' => '3174');
	$infos[] = array('d' => '2020-05-07', 'r' => '321', 'v' => '3177');
	$infos[] = array('d' => '2020-06-30', 'r' => '322', 'v' => '3194');
	$infos[] = array('d' => '2020-08-19', 'r' => '323', 'v' => '3224');
	$infos[] = array('d' => '2020-09-15', 'r' => '324', 'v' => '3254');
	$infos[] = array('d' => '2020-09-16', 'r' => '325', 'v' => '3255');
	$infos[] = array('d' => '2020-11-05', 'r' => '326', 'v' => '3272');
	$infos[] = array('d' => '2020-11-18', 'r' => '327', 'v' => '3274');
	$infos[] = array('d' => '2020-11-20', 'r' => '328', 'v' => '3275');
	$infos[] = array('d' => '2021-01-13', 'r' => '329', 'v' => '3300');
	$infos[] = array('d' => '2021-01-15', 'r' => '330', 'v' => '3301');
	$infos[] = array('d' => '2021-02-24', 'r' => '331', 'v' => '3315');
	$infos[] = array('d' => '2021-03-31', 'r' => '332', 'v' => '3325');
	$infos[] = array('d' => '2021-05-25', 'r' => '333', 'v' => '3328');
	$infos[] = array('d' => '2021-07-14', 'r' => '334', 'v' => '3344');
	$infos[] = array('d' => '2021-10-13', 'r' => '335', 'v' => '3387');
	$infos[] = array('d' => '2021-12-15', 'r' => '336', 'v' => '3399');
	$infos[] = array('d' => '2022-03-01', 'r' => '337', 'v' => '3414');
	$infos[] = array('d' => '2022-05-24', 'r' => '400', 'v' => '3441');
	$infos[] = array('d' => '2022-06-13', 'r' => '401', 'v' => '3448');
	$infos[] = array('d' => '2022-07-11', 'r' => '402', 'v' => '3466');
	$infos[] = array('d' => '2022-07-28', 'r' => '403', 'v' => '3474');
	$infos[] = array('d' => '2022-08-15', 'r' => '404', 'v' => '3483');
	$infos[] = array('d' => '2022-09-12', 'r' => '405', 'v' => '3488');
	$infos[] = array('d' => '2022-10-13', 'r' => '406', 'v' => '3498');
	$infos[] = array('d' => '2023-01-23', 'r' => '407', 'v' => '3515');
	$infos[] = array('d' => '2023-06-13', 'r' => '408', 'v' => '3532');
	$infos[] = array('d' => '2023-07-12', 'r' => '409', 'v' => '3533');
	$infos[] = array('d' => '2023-07-26', 'r' => '410', 'v' => '3535');
	$infos[] = array('d' => '2023-08-23', 'r' => '411', 'v' => '3548');
	$infos[] = array('d' => '2023-09-14', 'r' => '412', 'v' => '3567');
	$infos[] = array('d' => '2023-09-25', 'r' => '413', 'v' => '3570');
	$infos[] = array('d' => '2023-10-31', 'r' => '414', 'v' => '3587');
	$infos[] = array('d' => '2023-12-19', 'r' => '415', 'v' => '3602');
	$infos[] = array('d' => '2024-02-07', 'r' => '416', 'v' => '3613');
	$infos[] = array('d' => '2024-07-10', 'r' => '500', 'v' => '3654');
	$infos[] = array('d' => '2024-08-07', 'r' => '501', 'v' => '3660');
	$infos[] = array('d' => '2024-09-04', 'r' => '502', 'v' => '3685');
	$infos[] = array('d' => '2024-09-25', 'r' => '503', 'v' => '3691');
	$infos[] = array('d' => '2024-10-16', 'r' => '504', 'v' => '3697');
	$infos[] = array('d' => '2024-11-12', 'r' => '505', 'v' => '3717');
	$infos[] = array('d' => '2024-12-10', 'r' => '506', 'v' => '3725');
	$infos[] = array('d' => '2025-01-13', 'r' => '507', 'v' => '3732');
	$infos[] = array('d' => '2025-02-05', 'r' => '508', 'v' => '3739');
	$infos[] = array('d' => '2025-03-12', 'r' => '509', 'v' => '3749');

	foreach ($infos as $i => $info) {
		if ($info['v'] == $version) {
			// print_r($info);
			return $info;
		}
	}

	foreach ($infos as $i => $info) {
		if ($info['d'] == $release) {
			// print_r($info);
			return $info;
		}
	}

	// echo print_r(array('d' => $release, 'r' => ' of Version '.$version, 'v' => $version), true),"\n";
	return array('d' => $release, 'r' => 'of Version '.$version, 'v' => $version);
}

function read_releasenotes_file($file) {
	$page = array();
	$fdata = file_get_contents($file);

	$pfind = '====== Version';
	$pstart = strpos($fdata, $pfind) + strlen($pfind);
	$pend = strpos($fdata, ' vom', $pstart - 1);
	$plen = $pend - $pstart;
	$page['version'] = trim(substr($fdata, $pstart, $plen));

	$pfind = ' vom ';
	$pstart = strpos($fdata, $pfind) + strlen($pfind);
	$pend = strpos($fdata, ' ======', $pstart - 1);
	$plen = $pend - $pstart;
	$page['date'] = trim(substr($fdata, $pstart, $plen));

	$pfind = 'Release: ';
	$pstart = strpos($fdata, $pfind) + strlen($pfind);
	$pend = strpos($fdata, "\n", $pstart - 1);
	$plen = $pend - $pstart;
	$page['release'] = trim(substr($fdata, $pstart, $plen));

	$tmp = get_rollout_info($page['version'], substr($page['release'], 0, 10));
	$page['rollout'] = 'Rollout '.$tmp['r'];

	$page['notes'] = trim(substr($fdata, 73));

	$page['notes'] = str_replace('\\\\ ', '<br>', $page['notes']);
	$page['notes'] = str_replace("=====  ", "</ul>\n</p>\n\n<p><br><hr><br>\n<b>", $page['notes']);
	$page['notes'] = str_replace(" =====\n", "</b>\n<br><br>\n", $page['notes']);
	$page['notes'] = str_replace("Feature ====\n\n", "<b>Feature</b>\n<ul>\n", $page['notes']);
	$page['notes'] = str_replace("Bugfix ====\n\n", "<b>Bugfix</b>\n<ul>\n", $page['notes']);
	$page['notes'] = str_replace("==== ", "</ul>\n\n", $page['notes']);
	$page['notes'] = str_replace("xxxxxxxx", "§§§§", $page['notes']);
	$page['notes'] = str_replace("xxxxxxxx", "§§§§", $page['notes']);
	$page['notes'] = str_replace("xxxxxxxx", "§§§§", $page['notes']);
	$page['notes'] = str_replace('  * ', "</li>\n<li>", $page['notes']);
	$page['notes'] = str_replace("\n</li>", "</li>", $page['notes']);
	$page['notes'] = str_replace("\n\n</ul>", "</li>\n</ul>", $page['notes']);
	$page['notes'] = str_replace("<br><br></li>\n</ul>", "<br><br>", $page['notes']);
	$page['notes'] .= "</li>\n</ul>\n</p>";
	$page['notes'] = '<p>'.substr($page['notes'], 28);

	file_put_contents(dirname(__FILE__).'/notes.txt', $page['notes']);
	file_put_contents(dirname(__FILE__).'/notes.html', $page['notes']);

	// echo print_r($page,  true),"\n";
	return $page;
}

// Import KB
if (1 == 0) {
	// $files = @glob(dirname(__FILE__).'/kbase/*.html');
	$files = @glob(dirname(__FILE__).'/kbase/Q-10003*.html');
	// $files = @glob(dirname(__FILE__).'/kbase/Q-10003*.html');
	// $files = @glob(dirname(__FILE__).'/kbase/Q-11000*.html');
	// $files = @glob(dirname(__FILE__).'/kbase/Q-106893.html');
	// $files = @glob(dirname(__FILE__).'/kbase/Q-11076*.html');
	
	$cnt = 0;
	$max = 10;
	
	foreach ($files as $file) {
		$kb = convert_file_typeA($file);
		create_kb_page($kb);
		$cnt++;
	
		if ($cnt >= $max)
			exit;
	}
}

// Import ReleaseNotes
if (1 == 1) {
	$files = @glob(dirname(__FILE__).'/releasenotes/*.txt');
	// $files = @glob(dirname(__FILE__).'/releasenotes/3725.txt');

	$cnt = 47;
	$max = 185;
	// $cnt = 180;
	// $max = 180;

	foreach ($files as $file) {
		$item = read_releasenotes_file($file);
		// create_rl_page($item);
		update_rl_page($cnt, $item);
		$cnt++;
	
		if ($cnt >= $max)
			exit;
	}
}