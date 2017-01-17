<?php

use PHPKitty\Module;

class Repeater extends Module {
    public function process(array $input) {
        return $input;
    }
}

$m->add('repeater', new Repeater());