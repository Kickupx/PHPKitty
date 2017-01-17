<?php

/**
 * TODO:
 * Add you own routes
 */

$r->get('index.html', '/index', [
    'namespace' => $m->repeater(42)
]);