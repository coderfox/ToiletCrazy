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
                                        'text' => Markdown::defaultTransform( $v[ 'text' ] ),
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
                                $token=$coll[ 'users' ]->find( array (
                                        'nick' => $_REQUEST[ 'nick' ],
                                        'pass' => md5( $_REQUEST[ 'pass' ] )
                                ) )->getNext()['token'];
                                $return=array(
                                	'token'=>$token
                                );
                                echo json_encode($return);
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
                            break;
                        }
                    case 'post' :
                        {
                            if ($_SERVER[ 'REQUEST_METHOD' ] != 'POST') {
                                throw new Exception( 'invaid method', 1 );
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
                            break;
                        }
                    case 'reg' :
                        {
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