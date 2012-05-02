<?php

/**
 * CI library that is both a wrapper and bootstrap for Doctrine 2 ORM.
 * @see http://docs.doctrine-project.org/en/latest/cookbook/integrating-with-codeigniter.html
 */

use Doctrine\Common\ClassLoader,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\Cache\ArrayCache,
    Doctrine\DBAL\Logging\EchoSQLLogger;

class Doctrine {

    public $em = NULL;

    public function __construct()
    {
        // Load configuration from CI - make sure to check for environment-specific config
        if (defined('ENVIRONMENT') AND file_exists(APPPATH.'config/'.ENVIRONMENT.'/database.php'))
        {
            require_once APPPATH.'config/'.ENVIRONMENT.'/database.php';
        }
        else
        {
            require_once APPPATH.'config/database.php';
        }

        // set up class loading. We don't have to use Doctrine's here
        require_once APPPATH.'libraries/Doctrine/Common/ClassLoader.php';

        $doctrineClassLoader = new ClassLoader('Doctrine', APPPATH.'libraries');
        $doctrineClassLoader->register();
        $entitiesClassLoader = new ClassLoader('models', rtrim(APPPATH, '/'));
        $entitiesClassLoader->register();
        $proxiesClassLoader = new ClassLoader('Proxies', APPPATH.'models/proxies');
        $proxiesClassLoader->register();

        // Set up caches
        $config = new Configuration();
        $cache = new ArrayCache();
        $config->setMetadataCacheImpl($cache);
        $driverImpl = $config->newDefaultAnnotationDriver(array(APPPATH.'models/Entities'));
        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($cache);
        // This line appears twice in documentationl not sure if needed?
        $config->setQueryCacheImpl($cache);

        // Proxy configuration
        $config->setProxyDir(APPPATH.'/models/proxies');
        $config->setProxyNamespace('Proxies');

        // Set up logger
        $logger = new EchoSQLLogger();
        $config->setSQLLogger($logger);

        $config->setAutoGenerateProxyClasses(TRUE);

        // DB Connection
        $connectionOptions = array(
            'driver' => 'pdo_mysql',
            'user' => $db['default']['username'],
            'password' => $db['default']['password'],
            'host' => $db['default']['hostname'],
            'dbname' => $db['default']['database'],
        );

        // Create EntityManager
        $this->em = EntityManager::create($connectionOptions, $config);
    }
}
