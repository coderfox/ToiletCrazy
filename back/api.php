<?php
use Michelf\MarkdownExtra;
header( 'Content-type: application/json;charset=utf8' );
include_once 'inc.php';
$db = new MongoClient( $config[ 'db' ][ 'conn' ][ 'uri' ], $config[ 'db' ][ 'conn' ][ 'opt' ] );
$coll = array (
        'users' => $db->selectCollection( $config[ 'db' ][ 'db' ], $config[ 'db' ][ 'coll' ][ 'users' ] ),
        'posts' => $db->selectCollection( $config[ 'db' ][ 'db' ], $config[ 'db' ][ 'coll' ][ 'posts' ] ) 
);

if (isset( $_REQUEST[ 'mod' ] ) && isset( $_REQUEST[ 'api' ] )) {
    $call = $_REQUEST[ 'mod' ] . '/' . $_REQUEST[ 'api' ];
} elseif (isset( $_REQUEST[ 'm' ] ) && isset( $_REQUEST[ 'a' ] )) {
    $call = $_REQUEST[ 'm' ] . '/' . $_REQUEST[ 'a' ];
} else {
    throw new Exception( 'invaid api call', 6 );
}
switch ($call) {
    case 'timeline/public' :
        {
            break;
        }
    case 'auth/auth' :
        {
            break;
        }
    case 'post/show' :
        {
            break;
        }
    case 'post/post' :
        {
            break;
        }
    case 'user/show' :
        {
            break;
        }
    case 'user/reg' :
        {
            break;
        }
    default :
        {
            throw new ApiEx( 'invaid api call', 6 );
            break;
        }
}