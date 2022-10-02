<?php

namespace WpStarter\Queue;

use Aws\DynamoDb\DynamoDbClient;
use WpStarter\Contracts\Debug\ExceptionHandler;
use WpStarter\Contracts\Support\DeferrableProvider;
use WpStarter\Queue\Connectors\BeanstalkdConnector;
use WpStarter\Queue\Connectors\DatabaseConnector;
use WpStarter\Queue\Connectors\NullConnector;
use WpStarter\Queue\Connectors\RedisConnector;
use WpStarter\Queue\Connectors\SqsConnector;
use WpStarter\Queue\Connectors\SyncConnector;
use WpStarter\Queue\Failed\DatabaseFailedJobProvider;
use WpStarter\Queue\Failed\DatabaseUuidFailedJobProvider;
use WpStarter\Queue\Failed\DynamoDbFailedJobProvider;
use WpStarter\Queue\Failed\NullFailedJobProvider;
use WpStarter\Support\Arr;
use WpStarter\Support\Facades\Facade;
use WpStarter\Support\ServiceProvider;
use Laravel\SerializableClosure\SerializableClosure;

class QueueServiceProvider extends ServiceProvider implements DeferrableProvider
{
    use SerializesAndRestoresModelIdentifiers;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->configureSerializableClosureUses();

        $this->registerManager();
        $this->registerConnection();
        $this->registerWorker();
        $this->registerListener();
        $this->registerFailedJobServices();
    }

    /**
     * Configure serializable closures uses.
     *
     * @return void
     */
    protected function configureSerializableClosureUses()
    {
        SerializableClosure::transformUseVariablesUsing(function ($data) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->getSerializedPropertyValue($value);
            }

            return $data;
        });

        SerializableClosure::resolveUseVariablesUsing(function ($data) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->getRestoredPropertyValue($value);
            }

            return $data;
        });
    }

    /**
     * Register the queue manager.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->singleton('queue', function ($app) {
            // Once we have an instance of the queue manager, we will register the various
            // resolvers for the queue connectors. These connectors are responsible for
            // creating the classes that accept queue configs and instantiate queues.
            return ws_tap(new QueueManager($app), function ($manager) {
                $this->registerConnectors($manager);
            });
        });
    }

    /**
     * Register the default queue connection binding.
     *
     * @return void
     */
    protected function registerConnection()
    {
        $this->app->singleton('queue.connection', function ($app) {
            return $app['queue']->connection();
        });
    }

    /**
     * Register the connectors on the queue manager.
     *
     * @param  \WpStarter\Queue\QueueManager  $manager
     * @return void
     */
    public function registerConnectors($manager)
    {
        foreach (['Null', 'Sync', 'Database', 'Redis', 'Beanstalkd', 'Sqs'] as $connector) {
            $this->{"register{$connector}Connector"}($manager);
        }
    }

    /**
     * Register the Null queue connector.
     *
     * @param  \WpStarter\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerNullConnector($manager)
    {
        $manager->addConnector('null', function () {
            return new NullConnector;
        });
    }

    /**
     * Register the Sync queue connector.
     *
     * @param  \WpStarter\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerSyncConnector($manager)
    {
        $manager->addConnector('sync', function () {
            return new SyncConnector;
        });
    }

    /**
     * Register the database queue connector.
     *
     * @param  \WpStarter\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerDatabaseConnector($manager)
    {
        $manager->addConnector('database', function () {
            return new DatabaseConnector($this->app['db']);
        });
    }

    /**
     * Register the Redis queue connector.
     *
     * @param  \WpStarter\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerRedisConnector($manager)
    {
        $manager->addConnector('redis', function () {
            return new RedisConnector($this->app['redis']);
        });
    }

    /**
     * Register the Beanstalkd queue connector.
     *
     * @param  \WpStarter\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerBeanstalkdConnector($manager)
    {
        $manager->addConnector('beanstalkd', function () {
            return new BeanstalkdConnector;
        });
    }

    /**
     * Register the Amazon SQS queue connector.
     *
     * @param  \WpStarter\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerSqsConnector($manager)
    {
        $manager->addConnector('sqs', function () {
            return new SqsConnector;
        });
    }

    /**
     * Register the queue worker.
     *
     * @return void
     */
    protected function registerWorker()
    {
        $this->app->singleton('queue.worker', function ($app) {
            $isDownForMaintenance = function () {
                return $this->app->isDownForMaintenance();
            };

            $resetScope = function () use ($app) {
                if (method_exists($app['log']->driver(), 'withoutContext')) {
                    $app['log']->withoutContext();
                }

                $app->forgetScopedInstances();

                return Facade::clearResolvedInstances();
            };

            return new Worker(
                $app['queue'],
                $app['events'],
                $app[ExceptionHandler::class],
                $isDownForMaintenance,
                $resetScope
            );
        });
    }

    /**
     * Register the queue listener.
     *
     * @return void
     */
    protected function registerListener()
    {
        $this->app->singleton('queue.listener', function ($app) {
            return new Listener($app->basePath());
        });
    }

    /**
     * Register the failed job services.
     *
     * @return void
     */
    protected function registerFailedJobServices()
    {
        $this->app->singleton('queue.failer', function ($app) {
            $config = $app['config']['queue.failed'];

            if (array_key_exists('driver', $config) &&
                (is_null($config['driver']) || $config['driver'] === 'null')) {
                return new NullFailedJobProvider;
            }

            if (isset($config['driver']) && $config['driver'] === 'dynamodb') {
                return $this->dynamoFailedJobProvider($config);
            } elseif (isset($config['driver']) && $config['driver'] === 'database-uuids') {
                return $this->databaseUuidFailedJobProvider($config);
            } elseif (isset($config['table'])) {
                return $this->databaseFailedJobProvider($config);
            } else {
                return new NullFailedJobProvider;
            }
        });
    }

    /**
     * Create a new database failed job provider.
     *
     * @param  array  $config
     * @return \WpStarter\Queue\Failed\DatabaseFailedJobProvider
     */
    protected function databaseFailedJobProvider($config)
    {
        return new DatabaseFailedJobProvider(
            $this->app['db'], $config['database'], $config['table']
        );
    }

    /**
     * Create a new database failed job provider that uses UUIDs as IDs.
     *
     * @param  array  $config
     * @return \WpStarter\Queue\Failed\DatabaseUuidFailedJobProvider
     */
    protected function databaseUuidFailedJobProvider($config)
    {
        return new DatabaseUuidFailedJobProvider(
            $this->app['db'], $config['database'], $config['table']
        );
    }

    /**
     * Create a new DynamoDb failed job provider.
     *
     * @param  array  $config
     * @return \WpStarter\Queue\Failed\DynamoDbFailedJobProvider
     */
    protected function dynamoFailedJobProvider($config)
    {
        $dynamoConfig = [
            'region' => $config['region'],
            'version' => 'latest',
            'endpoint' => $config['endpoint'] ?? null,
        ];

        if (! empty($config['key']) && ! empty($config['secret'])) {
            $dynamoConfig['credentials'] = Arr::only(
                $config, ['key', 'secret', 'token']
            );
        }

        return new DynamoDbFailedJobProvider(
            new DynamoDbClient($dynamoConfig),
            $this->app['config']['app.name'],
            $config['table']
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'queue',
            'queue.connection',
            'queue.failer',
            'queue.listener',
            'queue.worker',
        ];
    }
}
