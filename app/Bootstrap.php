<?php declare(strict_types = 1);

namespace App;

use Contributte\Bootstrap\ExtraConfigurator;
use Dotenv\Dotenv;
use Nette\DI\Compiler;
use Tracy\Debugger;

class Bootstrap
{

	public static function boot(): ExtraConfigurator
	{
		date_default_timezone_set('Europe/Prague');

		$dotenv = Dotenv::createImmutable(realpath(__DIR__ . '/..'), '.env');
		$dotenv->safeLoad();

		$configurator = new ExtraConfigurator();
		$configurator->setTempDirectory(__DIR__ . '/../var/tmp');

		unset($configurator->defaultExtensions['security']);

		$configurator->onCompile[] = function (ExtraConfigurator $configurator, Compiler $compiler): void {
			$compiler->addConfig(['parameters' => $configurator->getEnvironmentParameters()]);
		};

		$configurator->setEnvDebugMode();
//		$configurator->setDebugMode(false);
		Debugger::$showBar = false;

		$configurator->addStaticParameters([
			'rootDir' => realpath(__DIR__ . '/..'),
			'appDir' => __DIR__,
			'wwwDir' => realpath(__DIR__ . '/../www'),
		]);

		if (isset($_ENV['env']) && $_ENV['env'] == 'test') {
			$configurator->addConfig([__DIR__ . '/../config/env/test.neon']);
		}

		if (getenv('NETTE_ENV', true) === 'dev') {
			$configurator->addConfig(__DIR__ . '/../config/env/dev.neon');
		} else {
			$configurator->addConfig(__DIR__ . '/../config/env/prod.neon');
		}

		$configurator->addConfig(__DIR__ . '/../config/local.neon');

		$configurator->addDynamicParameters([
			'env' => $_ENV
		]);

		return $configurator;
	}

}
