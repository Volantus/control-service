<?php
use Dotenv\Dotenv;
use Symfony\Component\Console\Application;

require __DIR__.'/vendor/autoload.php';

$dotEnv = new Dotenv(__DIR__);
$dotEnv->load();

$application = new Application();
$application->add(new \Volantus\OrientationControlService\Src\CLI\ServiceCommand());
$application->run();