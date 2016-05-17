<?php
/**
 * Created by PhpStorm.
 * User: horacekp
 * Date: 28/07/15
 * Time: 02:02
 */

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

$configurator->setDebugMode(false);  // debug mode MUST NOT be enabled on production server
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../tmp');

$configurator->createRobotLoader()
->register();


$files = \Nette\Utils\Finder::find(__DIR__.'/../tmp');

$counter = 0;
foreach (Nette\Utils\Finder::find('*')->from(__DIR__.'/../tmp') as $entry)
{
	$path = (string) $entry;
	if ($entry->isDir()) { // collector: remove empty dirs
		@rmdir($path); // @ - removing dirs is not necessary
	}
	$file = $path;
	if (@unlink($file)) { // @ - file may not already exist
		$counter++;
	}
}

echo "Smazano {$counter} souboru z cache.";