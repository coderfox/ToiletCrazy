<?php
function api_result($method, $function){
    try {
        if ($_SERVER[ 'REQUEST_METHOD' ] != $method) {
            throw new ApiEx( 'invaid method', 1 );
        }
        $args = func_get_args();
        array_shift( $args );
        array_shift( $args );
        $r = json_encode( callback( $function, $args ) );
        if (json_last_error()) {
            throw new Exception( json_last_error_msg(), json_last_error() );
        }
        echo $r;
    } catch ( ApiEx $e ) {
        api_error( $e );
    } catch ( Exception $e ) {
        api_server_error( $e );
    }
}
function callback($function, $args){
    if (is_callable( $function )) {
        return call_user_func_array( $function, $args );
    } else {
        api_server_error( new Exception( 'invaid function param' ) );
    }
}
function api_error($ex){
    $r = array (
            'error' => $ex->getMessage(),
            'code' => $ex->getCode() 
    );
    $r = json_encode( $r );
    if (json_last_error()) {
        api_server_error( new Exception( json_last_error_msg(), json_last_error() ) );
    } else {
        echo $r;
    }
}
function api_server_error($ex){
    $r = array (
            'error' => $ex->getMessage(),
            'code' => 0,
            'ex_code' => $ex->getCode() 
    );
    $r = json_encode( $r );
    if (json_last_error()) {
        api_server_error( new Exception( json_last_error_msg(), json_last_error() ) );
    } else {
        echo $r;
    }
}
function uhtml($str){
    $farr = array (
            "/<(\/?)(object|script|i?frame|style|html|body|title|link|meta|\?|\%)([^>]*?)>/isU",
            "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU" 
    );
    $tarr = array (
            " ",
            "",
            "\1\2" 
    );
    $str = preg_replace( $farr, $tarr, $str );
    return $str;
}