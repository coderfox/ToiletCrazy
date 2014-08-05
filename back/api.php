<?php
header( 'Content-type: application/json;charset=utf8' );
include_once __DIR__ . '/inc.php';
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
            api_result( 'GET', function () use($coll){
                return array (
                        'posts' => call_user_func( 'parse_posts', $coll, $coll[ 'posts' ]->find()->sort( array (
                                'time' => - 1 
                        ) )->limit( 20 ) ) 
                );
            } );
            break;
        }
    case 'auth/auth' :
        {
            api_result( 'POST', function () use($_REQUEST, $coll){
                if ($coll[ 'users' ]->find( array (
                        'nick' => $_REQUEST[ 'nick' ],
                        'pass' => md5( $_REQUEST[ 'pass' ] ) 
                ) )->count() > 0) {
                    $res = $coll[ 'users' ]->find( array (
                            'nick' => $_REQUEST[ 'nick' ],
                            'pass' => md5( $_REQUEST[ 'pass' ] ) 
                    ) );
                    $r1 = array (
                            'token' => $res->getNext()['token'] 
                    );
                    $r2 = parse_user( $res );
                    return $r2 + $r1;
                }
            } );
            break;
        }
    case 'post/show' :
        {
            api_result( 'GET', 'get_post_array', $coll, $_REQUEST[ 'id' ] );
            break;
        }
    case 'post/post' :
        {
            api_result( 'POST', function () use($_REQUEST, $coll){
                $author = $coll[ 'users' ]->find( array (
                        'token' => $_REQUEST[ 'token' ] 
                ) )->getNext();
                if ($author) {
                    $ins = array (
                            'title' => $_REQUEST[ 'title' ],
                            'text' => $_REQUEST[ 'text' ],
                            'author' => (string) ($author[ '_id' ]),
                            'time' => time() 
                    );
                    $result = $coll[ 'posts' ]->insert( $ins );
                    if ($result) {
                        $id = (string) $ins[ '_id' ];
                        return get_post_array( $coll, $id );
                    } else {
                        throw new Exception( 'server error', 0 );
                    }
                } else {
                    throw new Exception( 'invaid token', 5 );
                }
            } );
            break;
        }
    case 'user/show' :
        {
            throw new Exception( 'not completed', 0 );
            break;
        }
    case 'user/reg' :
        {
            api_result( 'POST', function () use($coll, $_REQUEST){
                if ($coll[ 'users' ]->find( array (
                        'nick' => $_REQUEST[ 'nick' ] 
                ) )->count() > 0) {
                    throw new Exception( 'nickname already used', 3 );
                } else {
                    $ins = array (
                            'nick' => $_REQUEST[ 'nick' ],
                            'pass' => md5( $_REQUEST[ 'pass' ] ),
                            'token' => md5( $_REQUEST[ 'nick' ] . $_REQUEST[ 'pass' ] ) 
                    );
                    $result = $coll[ 'users' ]->insert( $ins );
                    if ($result) {
                        return array (
                                'id' => (string) $ins[ '_id' ],
                                'nick' => $_REQUEST[ 'nick' ],
                                'token' => md5( $_REQUEST[ 'nick' ] . $_REQUEST[ 'pass' ] ) 
                        );
                    } else {
                        throw new Exception( 'server error', 0 );
                    }
                }
            } );
            break;
        }
    default :
        {
            throw new ApiEx( 'invaid api call', 6 );
            break;
        }
}