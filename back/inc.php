<?php
// markdown
include_once __DIR__ . '/md/Michelf/Markdown.inc.php';
include_once __DIR__ . '/md/Michelf/MarkdownExtra.inc.php';
// classes
include_once __DIR__ . '/classes/apiex.php';
include_once __DIR__ . '/classes/phpex.php';
include_once __DIR__ . '/classes/api.php';
include_once __DIR__ . '/classes/post.php';
include_once __DIR__ . '/classes/user.php';
// functions
include_once __DIR__ . '/functions/extend.php';
// config
if (file_exists( __DIR__ . '/config.json' )) {
    $config = fopen( __DIR__ . '/config.json', 'r' );
    $config = fread( $config, filesize( __DIR__ . '/config.json' ) );
    $config = json_decode( $config, true );
} else {
    api_server_error( new Exception( 'error with config' ) );
}