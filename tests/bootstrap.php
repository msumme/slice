<?php

/* @var $autoloader \Composer\Autoload\ClassLoader */
$autoloader = include __DIR__. '/../vendor/autoload.php';

$autoloader->addPsr4('Tests\\Slice\\', __DIR__);

ini_set('display_errors', true);
error_reporting(E_ALL);
