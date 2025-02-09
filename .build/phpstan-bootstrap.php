<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Nettrine\ORM\EntityManagerDecorator;

require __DIR__ . '/vendor/autoload.php';

// Use an in-memory SQLite database
$config = ORMSetup::createAnnotationMetadataConfiguration([], true);
$connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true], $config);
$entityManager = new EntityManager($connection, $config);

// Override Nettrine EntityManager
$entityManagerDecorator = new EntityManagerDecorator($entityManager);

return $entityManagerDecorator;
