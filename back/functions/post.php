<?php
function parse_post($coll,$data){
    $post = current( iterator_to_array( $data ) );
    return array (
            'id' => (string) $post[ '_id' ],
            'title' => $post[ 'title' ],
            'author' => get_user_array( $coll, $post[ 'author' ] ),
            'text_html' => MarkdownExtra::defaultTransform( $post[ 'text' ] ),
            'text_md' => $post[ 'text' ],
            'time' => date( "Y-m-d H:i:s", $post[ 'time' ] ) 
    );
}
function parse_posts($coll,$datas){
    $return = array ();
    foreach ( $datas as $p ) {
        $return[ ] = parse_post( $coll,$p );
    }
    return $return;
}
function get_post_array($coll, $id){
    return parse_post( $coll[ 'posts' ]->find( array (
            '_id' => new MongoId( $_REQUEST[ 'id' ] ) 
    ) )->limit( 1 )->getNext() );
}