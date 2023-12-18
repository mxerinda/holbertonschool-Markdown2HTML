<?php
use Markdown2Html\Builder;
use Markdown2Html\Config;

function includeComposerAutoloader()
{
    $autoloaderFiles = [
        __DIR__ . '/../autoload.php',
        __DIR__ . '/vendor/autoload.php',
        __DIR__ . '/../../autoload.php',
        'autoload.php',
    ];

    foreach ($autoloaderFiles as $autoloader) {
        if (file_exists($autoloader)) {
            return require_once $autoloader;
        }
    }

    echo PHP_EOL . 'Error: Can not find composer autoloader. Do you have installed it correctly?' . PHP_EOL;
    exit(1);
}

$loader  = includeComposerAutoloader();
$execDir = getcwd();

try {
    $configFile = isset($argv[1]) ? realpath($argv[1]) : null;

    if (!$configFile) {
        $configFile = realpath($execDir . '/markdown2html.config.php');
    }

    if (!$configFile) {
        throw new InvalidArgumentException('Given config file does not exist');
    }

    $config = require $configFile;

    if (!$config instanceof Config) {
        throw new InvalidArgumentException('Config file must return instance of ' . Config::class);
    }

    $builder = new Builder();
    $builder->build($config->src, $config->dest, $config->theme);

    echo 'done!' . PHP_EOL;
} catch (Exception $ex) {
    echo 'Error: ' . $ex->getMessage() . PHP_EOL;

    exit(1);
}
