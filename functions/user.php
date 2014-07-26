<?php
function tcGetUser($coll,$uid){
    return iterator_to_array( $cursor_user = $coll ['users']->find( array (
            '_id' => new MongoId( $uid ) 
    ) )->limit( 1 ) )[$uid];
}