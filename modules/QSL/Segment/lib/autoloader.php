<?php

$mapping = [
    'Segment' => __DIR__ . '/Segment.php'
];

spl_autoload_register(function ($class) use ($mapping) {
    if (isset($mapping[$class])) {
        require $mapping[$class];
    }
}, true);
