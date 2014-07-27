<?php
use Michelf\Markdown;
include_once 'inc.php';
$db = new MongoClient( $config[ 'db' ][ 'conn' ][ 'uri' ], $config[ 'db' ][ 'conn' ][ 'opt' ] );
$coll = array (
        'users' => $db->selectCollection( $config[ 'db' ][ 'db' ], $config[ 'db' ][ 'coll' ][ 'users' ] ),
        'posts' => $db->selectCollection( $config[ 'db' ][ 'db' ], $config[ 'db' ][ 'coll' ][ 'posts' ] ) 
);
try {
    switch ($_REQUEST[ 'mod' ]) {
        case 'timeline' :
            {
                switch ($_REQUEST[ 'api' ]) {
                    case 'public' :
                        {
                            if ($_SERVER[ 'REQUEST_METHOD' ] != 'GET') {
                                throw new Exception( 'invaid method', 1 );
                            }
                            $result = iterator_to_array( $coll[ 'posts' ]->find()->sort( array (
                                    'time' => - 1 
                            ) )->limit( 20 ) );
                            $return[ 'posts' ] = array ();
                            foreach ( $result as $k => $v ) {
                                $author = iterator_to_array( $coll[ 'users' ]->find( array (
                                        '_id' => new MongoId( $v[ 'author' ] ) 
                                ) ) )[$v[ 'author' ]];
                                $Rauthor = array (
                                        'id' => $v[ 'author' ],
                                        'nick' => $author[ 'nick' ] 
                                );
                                $return[ 'posts' ][ ] = array (
                                        'id' => $k,
                                        'title' => $v[ 'title' ],
                                        'text_html' => Markdown::defaultTransform( $v[ 'text' ] ),
                                        'text_md' => $v[ 'text' ],
                                        'author' => $Rauthor,
                                        'time' => $v[ 'time' ] 
                                );
                            }
                            echo json_encode( $return );
                            break;
                        }
                }
                break;
            }
        case 'auth' :
            {
                switch ($_REQUEST[ 'api' ]) {
                    case 'auth' :
                        {
                            if ($_SERVER[ 'REQUEST_METHOD' ] != 'POST') {
                                throw new Exception( 'invaid method', 1 );
                            }
                            if ($coll[ 'users' ]->find( array (
                                    'nick' => $_REQUEST[ 'nick' ],
                                    'pass' => md5( $_REQUEST[ 'pass' ] ) 
                            ) )->count() > 0) {
                                $token = $coll[ 'users' ]->find( array (
                                        'nick' => $_REQUEST[ 'nick' ],
                                        'pass' => md5( $_REQUEST[ 'pass' ] ) 
                                ) )->getNext()['token'];
                                $return = array (
                                        'token' => $token 
                                );
                                echo json_encode( $return );
                            } else {
                                throw new Exception( 'nickname and password mismatch', 2 );
                            }
                            break;
                        }
                }
                break;
            }
        case 'post' :
            {
                switch ($_REQUEST[ 'api' ]) {
                    case 'show' :
                        {
                            if ($_SERVER[ 'REQUEST_METHOD' ] != 'GET') {
                                throw new Exception( 'invaid method', 1 );
                            }
                            $post = $coll[ 'posts' ]->find( array (
                                    '_id' => new MongoId( $_REQUEST[ 'id' ] ) 
                            ) )->limit( 1 )->getNext();
                            if (! $post) {
                                throw new Exception( 'invaid id', 4 );
                            }
                            $author = $coll[ 'users' ]->find( array (
                                    '_id' => new MongoId( $post[ 'author' ] ) 
                            ) )->getNext();
                            $return = array (
                                    'id' => $_REQUEST[ 'id' ],
                                    'title' => $post[ 'title' ],
                                    'author' => array (
                                            'id' => $post[ 'author' ],
                                            'nick' => $author[ 'nick' ] 
                                    ),
                                    'text_html' => Markdown::defaultTransform( $post[ 'text' ] ),
                                    'text_md' => $post[ 'text' ],
                                    'time' => $post[ 'time' ] 
                            );
                            echo json_encode( $return );
                            break;
                        }
                    case 'post' :
                        {
                            if ($_SERVER[ 'REQUEST_METHOD' ] != 'POST') {
                                throw new Exception( 'invaid method', 1 );
                            }
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
                                    // show
                                    $post = $coll[ 'posts' ]->find( array (
                                            '_id' => new MongoId( $id ) 
                                    ) )->limit( 1 )->getNext();
                                    if (! $post) {
                                        throw new Exception( 'invaid id', 4 );
                                    }
                                    $author = $coll[ 'users' ]->find( array (
                                            '_id' => new MongoId( $post[ 'author' ] ) 
                                    ) )->getNext();
                                    $return = array (
                                            'id' => $id,
                                            'title' => $post[ 'title' ],
                                            'author' => array (
                                                    'id' => $post[ 'author' ],
                                                    'nick' => $author[ 'nick' ] 
                                            ),
                                            'text_html' => Markdown::defaultTransform( $post[ 'text' ] ),
                                            'text_md' => $post[ 'text' ],
                                            'time' => $post[ 'time' ] 
                                    );
                                    echo json_encode( $return );
                                } else {
                                    throw new Exception( 'server error', 0 );
                                }
                            } else {
                                throw new Exception( 'invaid token', 5 );
                            }
                            break;
                        }
                }
                break;
            }
        case 'user' :
            {
                switch ($_REQUEST[ 'api' ]) {
                    case 'show' :
                        {
                            if ($_SERVER[ 'REQUEST_METHOD' ] != 'GET') {
                                throw new Exception( 'invaid method', 1 );
                            }
                            $result = iterator_to_array( $coll[ 'posts' ]->find( array (
                                    'author' => $_REQUEST[ 'id' ] 
                            ) )->sort( array (
                                    'time' => - 1 
                            ) )->limit( 20 ) );
                            $return[ 'posts' ] = array ();
                            $posts_c = 0;
                            foreach ( $result as $k => $v ) {
                                $author = iterator_to_array( $coll[ 'users' ]->find( array (
                                        '_id' => new MongoId( $v[ 'author' ] ) 
                                ) ) )[$v[ 'author' ]];
                                $Rauthor = array (
                                        'id' => $v[ 'author' ],
                                        'nick' => $author[ 'nick' ] 
                                );
                                $return[ 'posts' ][ ] = array (
                                        'id' => $k,
                                        'title' => $v[ 'title' ],
                                        'text_html' => Markdown::defaultTransform( $v[ 'text' ] ),
                                        'text_md' => $v[ 'text' ],
                                        'author' => $Rauthor,
                                        'time' => $v[ 'time' ] 
                                );
                                $posts_c ++;
                            }
                            $return[ 'id' ] = $_REQUEST[ 'id' ];
                            $return[ 'post_count' ] = $posts_c;
                            $return[ 'nick' ] = $Rauthor[ 'nick' ];
                            echo json_encode( $return );
                            break;
                        }
                    case 'reg' :
                        {
                            if ($_SERVER[ 'REQUEST_METHOD' ] != 'POST') {
                                throw new Exception( 'invaid method', 1 );
                            }
                            if ($coll[ 'users' ]->find( array (
                                    'nick' => $_REQUEST[ 'nick' ] 
                            ) )->count() > 0) {
                                throw new Exception( 'nickname already used', 3 );
                            } else {
                                $result = $coll[ 'users' ]->insert( array (
                                        'nick' => $_REQUEST[ 'nick' ],
                                        'pass' => md5( $_REQUEST[ 'pass' ] ),
                                        'token' => md5( $_REQUEST[ 'nick' ] . $_REQUEST[ 'pass' ] ) 
                                ) );
                                if ($result) {
                                    $return = array (
                                            'nick' => $_REQUEST[ 'nick' ],
                                            'token' => md5( $_REQUEST[ 'nick' ] . $_REQUEST[ 'pass' ] ) 
                                    );
                                    echo json_encode( $return );
                                } else {
                                    throw new Exception( 'server error', 0 );
                                }
                            }
                            break;
                        }
                }
                break;
            }
    }
} catch ( Exception $e ) {
    $return = array (
            'error' => $e->getMessage(),
            'code' => $e->getCode() 
    );
    echo json_encode( $return );
}