<?php
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
function get_type($var)
{
    if(is_object($var))
        return get_class($var);
    if(is_null($var))
        return 'null';
    if(is_string($var))
        return 'string';
    if(is_array($var))
        return 'array';
    if(is_int($var))
        return 'integer';
    if(is_bool($var))
        return 'boolean';
    if(is_float($var))
        return 'float';
    if(is_resource($var))
        return 'resource';
    //throw new NotImplementedException();
    return 'unknown';
}