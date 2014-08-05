<?php
// markdown
include_once __DIR__ . '/md/Michelf/Markdown.inc.php';
include_once __DIR__ . '/md/Michelf/MarkdownExtra.inc.php';
// classes
include_once __DIR__ . '/classes/apiex.php';
// functions
include_once __DIR__ . '/functions/user.php';
include_once __DIR__ . '/functions/post.php';
include_once __DIR__ . '/functions/api.php';
// config
if (file_exists( __DIR__ . '/config.json' )) {
    $config = fopen( __DIR__ . 'config.json', 'r' );
    $config = fread( $config, filesize( __DIR__ . 'config.json' ) );
    $config = json_decode( $config, true );
} else {
    throw new Exception( 'error with config' );
}