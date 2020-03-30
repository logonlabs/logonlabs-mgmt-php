<?php

namespace LogonLabs\Manage\API;


class ResponseObject {

    private $content;

    public function __construct($options) {
        $this->content = $options;
    }

    /* dynamic function server */
    function __call($method,$arguments) {
        $meth = $this->from_camel_case(substr($method,3,strlen($method)-3));
        return array_key_exists($meth,$this->content) ? $this->content[$meth] : NULL;
    }

    /* uncamelcaser: via http://www.paulferrett.com/2009/php-camel-case-functions/ */
    function from_camel_case($str) {
        $str[0] = strtolower($str[0]);
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');
        return preg_replace_callback('/([A-Z])/', $func, $str);
    }
}