<?php

namespace CWP\ADS\AbstractApi\Tests\Factory;

class TestClass {
    public function __construct($params) {
        foreach($params as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
