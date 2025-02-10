<?php declare(strict_types = 1);

namespace App;

use Contributte\Bootstrap\ExtraConfigurator;
use Dotenv\Dotenv;
use Nette\Bridges\SecurityDI\SecurityExtension;
use Nette\DI\Compiler;
use Tracy\Debugger;
use Nette\Bootstrap\Configurator;

class Bootstrap
{

	public static function boot(): ExtraConfigurator
	{
		date_default_timezone_set('Europe/Prague');

		$dotenv = Dotenv::createImmutable((string)realpath(__DIR__ . '/..'), '.env');
		$dotenv->safeLoad();

		$configurator = new ExtraConfigurator();
		$configurator->setTempDirectory(__DIR__ . '/../var/tmp');

		unset($configurator->defaultExtensions['security']);

		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler): void {
			$compiler->addConfig(['parameters' => $_ENV]);
		};

		$configurator->setEnvDebugMode();
//		$configurator->setDebugMode(false);
		Debugger::$showBar = false;

		$configurator->addStaticParameters([
			'rootDir' => realpath(__DIR__ . '/..'),
			'appDir' => __DIR__,
			'wwwDir' => realpath(__DIR__ . '/../www'),
		]);

		if (getenv('NETTE_ENV', true) === 'dev') {
			$configurator->addConfig(__DIR__ . '/../config/env/dev.neon');
		} else {
			$configurator->addConfig(__DIR__ . '/../config/env/prod.neon');
		}

		$configurator->addConfig(__DIR__ . '/../config/local.neon');

		if (isset($_ENV['ENV']) && $_ENV['ENV'] == 'test') {
			$configurator->addConfig(__DIR__ . '/../config/env/test.neon');
		}

		$configurator->addDynamicParameters([
			'env' => $_ENV
		]);

		return $configurator;
	}

}
