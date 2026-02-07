<?php
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '1');

/*
 * Liest die JSON Dateien und erstellt bzw. bearbeitet die
 * Wiki Artikel im david-forum
 */

function read_json_file($file_path) {
    if (!file_exists($file_path)) {
        echo "Error: File not found at " . $file_path . "\n";
        return null;
    }
    $json_content = file_get_contents($file_path);
    if ($json_content === false) {
        echo "Error: Could not read file content from " . $file_path . "\n";
        return null;
    }
    $data = json_decode($json_content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error decoding JSON from " . $file_path . ": " . json_last_error_msg() . "\n";
        return null;
    }
    return $data;
}

function process_woltlab_request($url, $info, $encodedData, $cookieValue) {
    $cookieFile = __DIR__ . "/cookies.txt";

    echo $info;

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
    file_put_contents(dirname(__FILE__).'/response-wiki.log', $info.": ".$status."\n", FILE_APPEND);
}

function create_wiki_page($info, $post_data, &$config) {
    $cookieValue = $config['sessionNumber'].'='.$config['sessionValue'].'; XSRF-TOKEN='.$config['xsrfToken'];
    parse_str('t='.$config['xsrfToken'], $tokenArr);

    $post_data['t'] = $tokenArr['t'];

    // print_r($post_data); exit;

    $encodedData = http_build_query($post_data);

    // echo $encodedData; exit;

    $url = "https://www.david-forum.de/wiki/entry-add/";

    process_woltlab_request($url, $info, $encodedData, $cookieValue);
}

function update_wiki_page($info, $post_data, &$config) {
    $cookieValue = $config['sessionNumber'].'='.$config['sessionValue'].'; XSRF-TOKEN='.$config['xsrfToken'];
    parse_str('t='.$config['xsrfToken'], $tokenArr);

    $post_data['t'] = $tokenArr['t'];

    // print_r($post_data); exit;

    $encodedData = http_build_query($post_data);

    // echo $encodedData; exit;

    $url = "https://www.david-forum.de/wiki/entry-edit/".$page_key."/";

    process_woltlab_request($url, $info, $encodedData, $cookieValue);
}

function create_kb_page(&$config, $kb) {
    $article = "aus Tobit KB importiert - vom ".$kb['date'];
    if (!empty($kb['link']))
        $article .= " - <a href='".$kb['link']."'>Beitrag bei Tobit</a>";
    $article .= "<hr><br>";

    $article .= "<b>Problem</b><hr><p>".$kb['problem']."</p><br><br><br>";
    $article .= "<b>Antwort</b><hr><p>".$kb['answer']."</p>";

    $post_data['subject'] = $kb['kbid'].' - '.$kb['title'];
    $post_data['synonyms'][] = $kb['kbid'];
    $post_data['tags'][] = $kb['product'];
    if ($kb['kblink'] == 1)
        $post_data['tags'][] = 'CheckLink';
    $post_data['restrictedWriteAccess'] = '';
    $post_data['category'] = '5';
    $post_data['excerpt'] = $kb['kbid'].' - '.$kb['title'];
    $post_data['message'] = $article;

    // if ($kb['kblink'] == 1)
    //     echo $kb['kbid'],"\n";

    // print_r($post_data);

    create_wiki_page("generating ".$kb['kbid']."... ", $post_data, $config);
}



$config['sessionNumber'] = 'wsc_583127_user_session';
$config['sessionValue']  = '0f2065ffe1a2e49c5b5872bda68a9b9a7748f628c6f7f0f58250aa70e269c971-AXlT19l%2F0JZfnDXFyJr5PsJJMPGi0Q%3D%3D';
$config['xsrfToken']     = 'd90aaed67151a6eb8e16f7dc868c77313c5390c0923bbb667f8a05ab7ad94211-ZlXsRn634PD%2BuiAgu5gEzA%3D%3D';

$files = @glob(dirname(__FILE__).'/json-kb-files/*.json');

echo "files found: " . count($files) . "\n";

$cnt = 1;
$max = count($files);
// $max = 10000;
$max = 1;

foreach ($files as $file) {
    echo "process [".$cnt."/".$max."]: " . $file . "\n";

    $kb = read_json_file($file);

    // echo print_r($kb, true),"\n";

    create_kb_page($config, $kb);

    $cnt++;

    if ($cnt >= $max)
        exit;
}
