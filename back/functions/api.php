<?php
function api_result($method, $function){
    try {
        if ($_SERVER[ 'REQUEST_METHOD' ] != $method) {
            throw new ApiEx( 'invaid method', 1 );
        }
        $args = func_get_args();
        array_shift($args);
        array_shift($args);
        $r = json_encode( callback($function, $args) );
        if (json_last_error()) {
            throw new Exception( json_last_error_msg(), json_last_error() );
        }
        echo $r;
    } catch ( ApiEx $e ) {
        $r = array (
                'error' => $e->getMessage(),
                'code' => $e->getCode() 
        );
        $r = json_encode( $r );
        if (json_last_error()) {
            throw new Exception( json_last_error_msg(), json_last_error() );
        }
        echo $r;
    } catch ( Exception $e ) {
        $r = array (
                'error' => $e->getMessage(),
                'code' => 0,
                'ex_code' => $e->getCode() 
        );
        $r = json_encode( $r );
        if (json_last_error()) {
            throw new Exception( json_last_error_msg(), json_last_error() );
        }
        echo $r;
    }
}

function callback($function,$args)
{
    if (is_callable($function)) {
        call_user_func_array($function, $args);
    }else{
        throw new Exception('invaid function param');
    }
}