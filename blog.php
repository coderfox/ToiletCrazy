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
$tc_cursor_posts = $tc_coll ['blogs']->find( array (
       '_id'=>new MongoId($_GET ['id'] )
) )->limit( 1 );
$template ['blog'] = iterator_to_array( $tc_cursor_posts )[$_GET['id']];
// output
include_once $tc_config ['template'] . 'header.php';
include_once $tc_config ['template'] . 'blog.php';
include_once $tc_config ['template'] . 'footer.php';