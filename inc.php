<?php
// markdown
include_once __DIR__ . DIRECTORY_SEPARATOR . 'markdown/Michelf/Markdown.inc.php';
include_once __DIR__ . DIRECTORY_SEPARATOR . 'markdown/Michelf/MarkdownExtra.inc.php';
// config
if(file_exists('config.json')){
$config = fopen( 'config.json', 'r' );
$config = fread( $config, filesize( 'config.json' ) );
$config = json_decode( $config, true );
}else{
    throw new Exception('error with config');
}