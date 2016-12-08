<?php

// Define the location of our source files
define("BASEPATH", dirname(dirname(__FILE__)));
define("SRC_DIR", join(DIRECTORY_SEPARATOR, [
    BASEPATH,
    'src'
]));

// Require all .php files in directory tree
$directory = new RecursiveDirectoryIterator(SRC_DIR);
foreach(new RecursiveIteratorIterator($directory) as $file) {
    if($file->getExtension() == 'php') {
        require_once($file);
    }
}


// Require our vendor files
require_once(join(DIRECTORY_SEPARATOR, [
    BASEPATH,
    'vendor',
    'autoload.php'
]));


// Require some base classes for testing purposes
require_once(join(DIRECTORY_SEPARATOR, [
    BASEPATH,
    'tests',
    'TestCase.php'
]));
