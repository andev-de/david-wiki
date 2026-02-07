<?php
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '1');

/*
 * originale KB Artikel von Tobit im HTML format einlesen und
 * im JSON-Format für die weitere Verarbeitung speichern
*/

function read_knowledgebase_file($file) {
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

    if (strpos($kb['answer'], 'kbarticleCLUB') !== false)
        $kb['kblink'] = '1';
    else
        $kb['kblink'] = '0';

    $kb['problem'] = strip_tags($kb['problem'], '<br><p><b><strong><i><em><hr><table><tr><td><th><ol><ul><li><a>');
    $kb['answer'] = strip_tags($kb['answer'], '<br><p><b><strong><i><em><hr><table><tr><td><th><ol><ul><li><a>');

    $pattern = '/kbarticleCLUB\.asp\?ArticleID=(\d+)/i';
    $replacement = 'search/?q='.$kb['kbid'].'&type=com.viecode.lexicon.entry&sortField=time&sortOrder=DESC';
    $kb['answer'] = preg_replace($pattern, $replacement, $kb['answer']);

    return $kb;
}

function write_kb_to_json_file($kb, $kbid) {
    $output_dir = dirname(__FILE__) . '/json-kb-files';
    if (!is_dir($output_dir)) {
        mkdir($output_dir, 0777, true);
    }
    $file_path = $output_dir . '/' . $kbid . '.json';
    file_put_contents($file_path, json_encode($kb, JSON_PRETTY_PRINT));
}

$files = @glob(dirname(__FILE__).'/tobit-kb-files/*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-10003*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-10004*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-10005*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-10006*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-10007*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-10008*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-10009*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-100*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-101*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-102*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-103*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-104*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-105*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-106*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-107*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-108*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-109*.html');
// $files = @glob(dirname(__FILE__).'/tobit-kb-files/Q-110*.html');

echo "files found: " . count($files) . "\n";

$cnt = 1;
$max = count($files);
// $max = 10000;
// $max = 1;

foreach ($files as $file) {
    echo "process [".$cnt."/".$max."]: " . $file . "\n";

    $kb = read_knowledgebase_file($file);

    // echo print_r($kb, true),"\n";

    write_kb_to_json_file($kb, $kb['kbid']);

    $cnt++;

    if ($cnt >= $max)
        exit;
}
