<?php

define('APP_DIR', realpath(__DIR__.'/../'));

require __DIR__ . '/../../vendor/autoload.php';

$configurator = new Nette\Configurator;

//$configurator->setDebugMode(false);  // debug mode MUST NOT be enabled on production server
$configurator->setDebugMode(array('192.168.50.1', '80.95.101.194', '188.175.125.165'));  // debug mode MUST NOT be enabled on production server
$configurator->detectDebugMode();
$configurator->enableDebugger(__DIR__ . '/../../log');

$configurator->setTempDirectory(__DIR__ . '/../../tmp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__ . '/..')
	->addDirectory(realpath(__DIR__ . '/../../vendor/other'))
	->addDirectory(realpath(__DIR__ . '/../../vendor/gedmo/doctrine-extensions/lib'))
	->register();

$configurator->addConfig(__DIR__ . '/../config/config.neon');
$configurator->addConfig(__DIR__ . '/../config/config.local.neon');

$container = $configurator->createContainer();

return $container;
