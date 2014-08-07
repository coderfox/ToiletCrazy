<?php
class Post{
    /**
     * get: getData
     * set: (private)setData
     * 
     * @var MongoCursor
     */
    private $data;
    /**
     * construct by data
     * 
     * @param MongoCursor $data            
     */
    public function __construct($data){
        $this->setData( $data );
    }
    /**
     * construct by id
     * 
     * @param
     *            mixed $id string/ MongoId
     * @return Post
     */
    public static function byId($id){
        if (get_type( $id ) == 'MongoId') {
            $query[ '_id' ] = $id;
        } elseif (get_type( $id ) == 'string') {
            $query[ '_id' ] = new MongoId( $id );
        } else {
            API::error( new Exception( 'invaid value' ) );
        }
        return new Post( $coll[ 'posts' ]->find( $query )->limit( 1 ) );
    }
    /**
     * get: data
     * 
     * @return MongoCursor
     */
    public function getData(){
        return $this -> data;
    }
    /**
     * set: data
     * 
     * @param MongoCursor $data            
     */
    private function setData($data){
        if (get_type( $data ) != 'MongoCursor') {
            API::error( new PHPEx( 'invaid value' ) );
        } else {
            if ($data->count() == 1) {
                $this -> data = $data;
            } else {
                $this -> data = $data->limit( 1 );
            }
        }
    }
    /**
     * get iterator_to_array of the data
     * 
     * @return array
     */
    private function getRawArray(){
        return current( iterator_to_array( $this -> data ) );
    }
    /**
     * get array
     * 
     * @return array
     */
    public function getArray(){
        $post = $this->getRawArray();
        return array (
                'id' => (string) $post[ '_id' ],
                'title' => $post[ 'title' ],
                'author' => User::byId( $post[ 'author' ] )->getArray(),
                'text' => array (
                        'html' => uhtml( MarkdownExtra::defaultTransform( $post[ 'text' ] ) ),
                        'md' => $post[ 'text' ],
                        'plain' => strip_tags( MarkdownExtra::defaultTransform( $post[ 'text' ] ) ),
                        'preview' => mb_substr( strip_tags( MarkdownExtra::defaultTransform( $post[ 'text' ] ) ), 0, 100, "utf8" ) 
                ),
                'time' => array (
                        'year' => date( "Y", $post[ 'time' ] ),
                        'month' => date( "m", $post[ 'time' ] ),
                        'day' => date( "d", $post[ 'time' ] ),
                        'hour' => date( "H", $post[ 'time' ] ),
                        'minute' => date( "i", $post[ 'time' ] ),
                        'second' => date( "s", $post[ 'time' ] ),
                        'formatted' => date( "Y-m-d H:i:s", $post[ 'time' ] ) 
                ) 
        );
    }
    /**
     * batch get array by MongoCursor
     * 
     * @param MongoCursor $datas            
     * @return array
     */
    public static function getArrayBatch($datas){
        $return = array ();
        for($i = 0; $i = $datas->count() - 1; $i ++) {
            $p = new Post( $datas->skip( $i ) );
            $return[ ] = $p->getArray();
        }
        return $return;
    }
}