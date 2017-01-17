<?php

require 'vendor/autoload.php';

use PHPKitty\IModule;
use PHPKitty\RouteDispatcher;
use PHPKitty\Template\FileSystemTemplate;
use PHPKitty\UserPermissions;
use PHPKitty\ModuleHelper;

class ModuleListBuilder {
    private $modules = [];

    public function add($name, IModule $module) {
        $this->modules[$name] = $module;
    }

    public function getAll() {
        return $this->modules;
    }
}

$m = new ModuleListBuilder();
require 'app/modules.php';

$p = [];
require 'app/permissions.php';

class RouteBuilder {
    private $module_stack = [];
    private $route_dispatcher;

    public function __construct(UserPermissions $user_permissions, array $modules, $twig_factory = null) {
        $this->route_dispatcher = new RouteDispatcher($user_permissions, $modules, $twig_factory);
    }

    public function get($template, $url, $modules = []) {
        $this->route_dispatcher->get($url, $modules, self::makeTemplate($template));
    }

    public function post($template, $url, $modules = []) {
        $this->route_dispatcher->post($url, $modules, self::makeTemplate($template));
    }

    public function getDispatcher() {
        return $this->route_dispatcher;
    }

    private static function makeTemplate($template) {
        return new FileSystemTemplate(__DIR__ . '/app/templates', $template);
    }
}


$r = new RouteBuilder(new UserPermissions($p), $m->getAll());
$m = new ModuleHelper();
require 'app/routes.php';

$dispatcher = $r->getDispatcher();
$dispatch = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

switch($dispatch[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        throw new Exception("Not found");
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        throw new Exception("Not allowed");
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $dispatch[1];
        $vars = $dispatch[2];
        
        //TODO: Change handler to also take the vars
        $handler();
        break;
}