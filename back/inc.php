<?php
// markdown
include_once __DIR__ .'/md/Michelf/Markdown.inc.php';
include_once __DIR__ .'/md/Michelf/MarkdownExtra.inc.php';
// config
if(file_exists('config.json')){
$config = fopen( 'config.json', 'r' );
$config = fread( $config, filesize( 'config.json' ) );
$config = json_decode( $config, true );
}else{
    throw new Exception('error with config');
}