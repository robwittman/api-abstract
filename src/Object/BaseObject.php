<?php

namespace CWP\ADS\AbstractApi\Object;

class BaseObject {
    protected $attributes = array();

    public function __construct($params) {
        foreach($params as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    public function setAttribute($key, $value) {
        $this->attributes[$key] = $value;
    }

    public function getAttribute($key) {
        if(array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        return false;
    }

    public function __get($var) {
        if($attr = $this->getAttribute($var)) {
            return $attr;
        }
        throw new \InvalidArgumentException("Object does not have property '{$var}'");
    }

    public function __set($key, $value) {
        $this->setAttribute($key, $value);
    }
}
