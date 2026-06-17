<?php

use Symfony\Component\Dotenv\Dotenv;
use Doctrine\ORM\Tools\SchemaTool;
use App\Kernel;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

// Ensure the test database schema exists for sqlite so BrowserKit requests work.
if (($_SERVER['APP_ENV'] ?? null) === 'test') {
    $kernel = new Kernel('test', true);
    $kernel->boot();

    $container = $kernel->getContainer();
    $registry = $container->get('doctrine');
    $em = $registry->getManager();

    $metadata = $em->getMetadataFactory()->getAllMetadata();
    if ($metadata) {
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    $kernel->shutdown();
}
