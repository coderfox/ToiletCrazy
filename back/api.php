<?php
header( 'Content-type: application/json;charset=utf8' );
include_once __DIR__ . '/inc.php';
$db = new MongoClient( $config[ 'db' ][ 'conn' ][ 'uri' ], $config[ 'db' ][ 'conn' ][ 'opt' ] );
$coll = array (
        'users' => $db->selectCollection( $config[ 'db' ][ 'db' ], $config[ 'db' ][ 'coll' ][ 'users' ] ),
        'posts' => $db->selectCollection( $config[ 'db' ][ 'db' ], $config[ 'db' ][ 'coll' ][ 'posts' ] ) 
);
global $coll;
unset($db);

// security check
foreach ( $_REQUEST as $v ) {
    if (gettype( $v ) == 'array' || gettype( $v ) == 'object') {
        API::error( new Exception( 'security check failed', 4 ) );
    }
}
if (isset( $_REQUEST[ 'call' ] )) {
    $call = $_REQUEST[ 'call' ];
} elseif (isset( $_REQUEST[ 'c' ] )) {
    $call = $_REQUEST[ 'c' ];
} else {
    API::error( new ApiEx( 'invaid call', 3 ) );
}
switch ($call) {
    case 'timeline/public' :
        {
            $api=new API('GET', function(){
                if (isset( $_REQUEST[ 'page' ] ) && is_numeric( $_REQUEST[ 'page' ] )) {
                    $page = $_REQUEST[ 'page' ];
                } else {
                    $page = 1;
                }
                if (isset( $_REQUEST[ 'count' ] ) && is_numeric( $_REQUEST[ 'count' ] ) && $_REQUEST[ 'count' ] <= 50 && $_REQUEST[ 'count' ] > 0) {
                    $count = $_REQUEST[ 'count' ];
                } else {
                    $count = 20;
                }
                $result = $coll[ 'posts' ]->find()->sort( array (
                        'time' => - 1
                ) )->skip( $count * ($page - 1) )->limit( $count );
                return array(
                	'posts'=>Post::getArrayBatch($result),
                        'pages'=>array (
                                'count' => (int) ($result->count()) / $count,
                                'current' => (int) $page 
                        ) 
                );
            });
            $api->execute();
            break;
        }
    case 'auth/auth' :
        {
            $api=new API('POST',function(){
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
                    $u=new User($res);
                    $r2 = $u->getArray();
                    return $r2 + $r1;
                }
            },array('nick','pass','key'));
            $api->execute();
            break;
        }
    case 'post/show' :
        {
            $api=new API( 'GET',function($id){
            	return Post::byId($id)->getArray();
            }, array('id'));
            $api->execute($_REQUEST[ 'id' ]);
            break;
        }
    case 'post/post' :
        {
            $api=new API( 'POST',function () use($coll){
                $author = $coll[ 'users' ]->find( array (
                        'token' => $_REQUEST[ 'token' ] 
                ) )->getNext();
                if ($author) {
                    $ins = array (
                            'title' => $_REQUEST[ 'title' ],
                            'text' => $_REQUEST[ 'text' ],
                            'author' => $author[ '_id' ],
                            'time' => time() 
                    );
                    $result = $coll[ 'posts' ]->insert( $ins );
                    if ($result) {
                        $id = $ins[ '_id' ];
                        return Post::byId($id)->getArray();
                    } else {
                        throw new ApiEx( 'server error', 0 );
                    }
                } else {
                    throw new ApiEx( 'invaid token', 5 );
                }
            } , array('token','title','text'));
            $api->execute();
            break;
        }
    case 'user/reg' :
        {
            $api=new API( 'POST',function () use($coll){
                if ($coll[ 'users' ]->find( array (
                        'nick' => $_REQUEST[ 'nick' ] 
                ) )->count() > 0) {
                    throw new ApiEx( 'nickname already used', 6 );
                } else {
                    $ins = array (
                            'nick' => $_REQUEST[ 'nick' ],
                            'pass' => md5( $_REQUEST[ 'pass' ] ),
                    );
                    $result = $coll[ 'users' ]->insert( $ins );
                    if ($result) {
                        return array (
                                'id' => $ins[ '_id' ],
                                'nick' => $_REQUEST[ 'nick' ],
                        );
                    } else {
                        throw new ApiEx( 'server error', 0 );
                    }
                }
            } , array('nick','pass'));
            $api->execute();
            break;
        }
    default :
        {
            API::error( new ApiEx( 'invaid call', 3 ) );
            break;
        }
}