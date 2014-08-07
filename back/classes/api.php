<?php
class API{
    /**
     * GET/POST
     * get: getMethod()
     * set: setMethod($method)
     *
     * @var string
     */
    private $method;
    /**
     * get: getCall()
     * set: setCall()
     *
     * @var Closure
     */
    private $function;
    /**
     * construct function
     *
     * @param string $method
     *            GET/POST, case insensitive
     * @param Closure $function
     *            call function
     * @param array $params
     *            array of the params need to check
     */
    public function __construct($method, $function, $params = array()){
        $this->setMethod( $method );
        $this->setCallback( $function );
        $this->checkParams( $params );
    }
    /**
     * get: method
     *
     * @return string
     */
    public function getMethod(){
        return $this -> method;
    }
    /**
     * set: method
     *
     * @param string $method
     *            GET/POST, case insensitive
     * @throws PHPEx
     */
    public function setMethod($method){
        $method = strtoupper( $method );
        if ($method == 'GET' || $method == 'POST') {
            $this -> method = $method;
        } else {
            API::error(new PHPEx( 'invaid value' ));
        }
    }
    /**
     * checkParams
     *
     * @param array $params            
     * @throws ApiEx
     */
    public function checkParams($params){
        foreach ( $params as $v ) {
            if (! isset( $_REQUEST[ $v ] )) {
                API::error(new ApiEx( 'invaid params', 1 ));
            }
        }
    }
    /**
     * get: function (call function)
     *
     * @return Closure
     */
    public function getCall(){
        return $this -> function;
    }
    /**
     * set: function (call function)
     *
     * @param Closure $function            
     * @throws PHPEx
     */
    public function setCall($function){
        if (is_callable( $function )) {
            $this -> function = $function;
        } else {
            API::error(new PHPEx( 'invaid value' ));
        }
    }
    /**
     * execute
     *
     * @throws ApiEx
     * @throws Exception
     */
    public function execute(){
        try {
            if ($_SERVER[ 'REQUEST_METHOD' ] != $this->getMethod()) {
                throw new ApiEx( 'invaid method', 2 );
            }
            $args = func_get_args();
            $r = json_encode( $this->callback( $args ) );
            if (json_last_error()) {
                throw new Exception( json_last_error_msg(), json_last_error() );
            }
            die( $r );
        } catch ( Exception $e ) {
            API::error( $e );
        }
    }
    /**
     * call the function
     *
     * @param array $args            
     * @return mixed
     */
    private function call($args){
        return call_user_func_array( $this->getCall(), $args );
    }
    /**
     * result the error
     *
     * @param Exception $ex            
     */
    public static function error($ex){
        $r = array (
                'error' => $ex->getMessage()
        );
        if(get_type($ex)=='ApiEx'){
            $r['code']=$ex->getCode();
        }else{
            $r['code']=0;
            $r['ex_code']=$ex->getCode();
        }
        $r = json_encode( $r );
        if (json_last_error()) {
            API::error( new Exception( json_last_error_msg(), json_last_error() ) );
        } else {
            die( $r );
        }
    }
}