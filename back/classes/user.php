<?php
class User{
    /**
     * get: getData
     * set: setData
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
     * @param mixed $id
     *            string/MongoId
     * @return User
     */
    public static function byId($id){
        global $coll;
        if (get_type( $id ) == 'MongoId') {
            $query[ '_id' ] = $id;
        } elseif (get_type( $id ) == 'string') {
            $query[ '_id' ] = new MongoId( $id );
        } else {
            API::error( new Exception( 'invaid value' ) );
        }
        return new User( $coll[ 'users' ]->find( $query )->limit( 1 ) );
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
            if ($data->count( true ) == 1) {
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
    public function getArray(){
        $user = $this->getRawArray();
        return array (
                'id' => (string) $user[ '_id' ],
                'nick' => $user[ 'nick' ] 
        );
    }
}