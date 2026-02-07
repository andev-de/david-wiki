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

function create_kb_page($config, $kb) {
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

    print_r($post_data);
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
