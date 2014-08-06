<?php
use Michelf\MarkdownExtra;
function parse_post_return_array($coll, $post){
    return array (
            'id' => (string) $post[ '_id' ],
            'title' => $post[ 'title' ],
            'author' => get_user_array( $coll, $post[ 'author' ] ),
            // <-- api v0.1- v0.2
            // 'text_html' => uhtml( MarkdownExtra::defaultTransform( $post[ 'text' ] ) ),
            // 'text_md' => $post[ 'text' ],
            // 'text_plain' => strip_tags( MarkdownExtra::defaultTransform( $post[ 'text' ] ) ),
            // 'text_preview' => mb_substr( strip_tags( MarkdownExtra::defaultTransform( $post[ 'text' ] ) ), 0, 100, "utf8" ),
            // --> <-- api v0.3
            'text' => array (
                    'html' => uhtml( MarkdownExtra::defaultTransform( $post[ 'text' ] ) ),
                    'md' => $post[ 'text' ],
                    'plain' => strip_tags( MarkdownExtra::defaultTransform( $post[ 'text' ] ) ),
                    'preview' => mb_substr( strip_tags( MarkdownExtra::defaultTransform( $post[ 'text' ] ) ), 0, 100, "utf8" ) 
            ),
            // --> <-- api v0.1
            // 'time' => date( "Y-m-d H:i:s", $post[ 'time' ] ) ,
            // --> <-- api v0.3
            'time' => array (
                    'year' => date( "Y", $post[ 'time' ] ),
                    'month' => date( "m", $post[ 'time' ] ),
                    'day' => date( "d", $post[ 'time' ] ),
                    'hour' => date( "H", $post[ 'time' ] ),
                    'minute' => date( "i", $post[ 'time' ] ),
                    'second' => date( "s", $post[ 'time' ] ),
                    'formatted' => date( "Y-m-d H:i:s", $post[ 'time' ] ) 
            ) 
    // -->
        );
}
function parse_post_mongo($coll, $data){
    $post = current( iterator_to_array( $data ) );
    return parse_post_return_array( $coll, $post );
}
function parse_post_array($coll, $data){
    return parse_post_return_array( $coll, $data );
}
function parse_posts($coll, $datas){
    $return = array ();
    $datas = iterator_to_array( $datas );
    foreach ( $datas as $p ) {
        $return[ ] = parse_post_array( $coll, $p );
    }
    return $return;
}
function get_post_array($coll, $id){
    return parse_post_array( $coll, $coll[ 'posts' ]->find( array (
            '_id' => new MongoId( $id ) 
    ) )->limit( 1 )->getNext() );
}