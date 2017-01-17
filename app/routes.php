<?php

/**
 * TODO:
 * Add you own routes
 * And add your own module instructions
 */

$r->get('index.html', '/index', [
    'namespace' => $m->repeater(['var' => 'repeater'])
]);