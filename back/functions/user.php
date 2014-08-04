<?php
function get_user_array($coll, $id){
    return parse_user($coll[ 'users' ]->find( array (
            '_id' => new MongoId( $id ) 
    ) ) );
}

function parse_user($data)
{
    $author=current(iterator_to_array($data));
    return array(
    	'id'=>(string)$author['_id'],
            'nick'=>$author['nick']
    );
}