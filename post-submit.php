<?php
include_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
// template init
$template = array (
        'page' => 'blog',
        'title' => $tc_config ['site'] ['title'] 
);
// database
if ($tc_config ['db'] ['model'] == 'mongodb') {
    $tc_db = new MongoClient( $tc_config ['db'] ['connection'] ['uri'], $tc_config ['db'] ['connection'] ['options'] );
    $tc_coll = array (
            'users' => $tc_db->selectCollection( $tc_config ['db'] ['connection'] ['db'], $tc_config ['db'] ['collection'] ['users'] ),
            'blogs' => $tc_db->selectCollection( $tc_config ['db'] ['connection'] ['db'], $tc_config ['db'] ['collection'] ['blogs'] ) 
    );
}
$tc_user = $tc_coll ['users']->find( array (
        'nick' => $_POST ['user'],
        'pass' => md5($_POST ['pass']) 
) );
if ($tc_user->count() > 0) {
    $tc_coll ['blogs']->insert( array (
            'title' => $_POST ['title'],
            'text' => $_POST ['text'],
            'author' => ( string ) ($tc_user->getNext()['_id']),
            'time' => time() 
    ) );
    header( 'Location: index.php' );
} else {
?>
U&P error.<a href="index.php">Index</a>
<?php
}