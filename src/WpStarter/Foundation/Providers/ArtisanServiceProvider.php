<?php

namespace WpStarter\Foundation\Providers;

use WpStarter\Auth\Console\ClearResetsCommand;
use WpStarter\Cache\Console\CacheTableCommand;
use WpStarter\Cache\Console\ClearCommand as CacheClearCommand;
use WpStarter\Cache\Console\ForgetCommand as CacheForgetCommand;
use WpStarter\Cache\Console\PruneStaleTagsCommand;
use WpStarter\Concurrency\Console\InvokeSerializedClosureCommand;
use WpStarter\Console\Scheduling\ScheduleClearCacheCommand;
use WpStarter\Console\Scheduling\ScheduleFinishCommand;
use WpStarter\Console\Scheduling\ScheduleInterruptCommand;
use WpStarter\Console\Scheduling\ScheduleListCommand;
use WpStarter\Console\Scheduling\ScheduleRunCommand;
use WpStarter\Console\Scheduling\ScheduleTestCommand;
use WpStarter\Console\Scheduling\ScheduleWorkCommand;
use WpStarter\Console\Signals;
use WpStarter\Contracts\Support\DeferrableProvider;
use WpStarter\Database\Console\DbCommand;
use WpStarter\Database\Console\DumpCommand;
use WpStarter\Database\Console\Factories\FactoryMakeCommand;
use WpStarter\Database\Console\MonitorCommand as DatabaseMonitorCommand;
use WpStarter\Database\Console\PruneCommand;
use WpStarter\Database\Console\Seeds\SeedCommand;
use WpStarter\Database\Console\Seeds\SeederMakeCommand;
use WpStarter\Database\Console\ShowCommand;
use WpStarter\Database\Console\ShowModelCommand;
use WpStarter\Database\Console\TableCommand as DatabaseTableCommand;
use WpStarter\Database\Console\WipeCommand;
use WpStarter\Foundation\Console\AboutCommand;
use WpStarter\Foundation\Console\ApiInstallCommand;
use WpStarter\Foundation\Console\BroadcastingInstallCommand;
use WpStarter\Foundation\Console\CastMakeCommand;
use WpStarter\Foundation\Console\ChannelListCommand;
use WpStarter\Foundation\Console\ChannelMakeCommand;
use WpStarter\Foundation\Console\ClassMakeCommand;
use WpStarter\Foundation\Console\ClearCompiledCommand;
use WpStarter\Foundation\Console\ComponentMakeCommand;
use WpStarter\Foundation\Console\ConfigCacheCommand;
use WpStarter\Foundation\Console\ConfigClearCommand;
use WpStarter\Foundation\Console\ConfigMakeCommand;
use WpStarter\Foundation\Console\ConfigPublishCommand;
use WpStarter\Foundation\Console\ConfigShowCommand;
use WpStarter\Foundation\Console\ConsoleMakeCommand;
use WpStarter\Foundation\Console\DocsCommand;
use WpStarter\Foundation\Console\DownCommand;
use WpStarter\Foundation\Console\EnumMakeCommand;
use WpStarter\Foundation\Console\EnvironmentCommand;
use WpStarter\Foundation\Console\EnvironmentDecryptCommand;
use WpStarter\Foundation\Console\EnvironmentEncryptCommand;
use WpStarter\Foundation\Console\EventCacheCommand;
use WpStarter\Foundation\Console\EventClearCommand;
use WpStarter\Foundation\Console\EventGenerateCommand;
use WpStarter\Foundation\Console\EventListCommand;
use WpStarter\Foundation\Console\EventMakeCommand;
use WpStarter\Foundation\Console\ExceptionMakeCommand;
use WpStarter\Foundation\Console\InterfaceMakeCommand;
use WpStarter\Foundation\Console\JobMakeCommand;
use WpStarter\Foundation\Console\JobMiddlewareMakeCommand;
use WpStarter\Foundation\Console\KeyGenerateCommand;
use WpStarter\Foundation\Console\LangPublishCommand;
use WpStarter\Foundation\Console\ListenerMakeCommand;
use WpStarter\Foundation\Console\MailMakeCommand;
use WpStarter\Foundation\Console\ModelMakeCommand;
use WpStarter\Foundation\Console\NotificationMakeCommand;
use WpStarter\Foundation\Console\ObserverMakeCommand;
use WpStarter\Foundation\Console\OptimizeClearCommand;
use WpStarter\Foundation\Console\OptimizeCommand;
use WpStarter\Foundation\Console\PackageDiscoverCommand;
use WpStarter\Foundation\Console\PolicyMakeCommand;
use WpStarter\Foundation\Console\ProviderMakeCommand;
use WpStarter\Foundation\Console\ReloadCommand;
use WpStarter\Foundation\Console\RequestMakeCommand;
use WpStarter\Foundation\Console\ResourceMakeCommand;
use WpStarter\Foundation\Console\RouteCacheCommand;
use WpStarter\Foundation\Console\RouteClearCommand;
use WpStarter\Foundation\Console\RouteListCommand;
use WpStarter\Foundation\Console\RuleMakeCommand;
use WpStarter\Foundation\Console\ScopeMakeCommand;
use WpStarter\Foundation\Console\ServeCommand;
use WpStarter\Foundation\Console\StorageLinkCommand;
use WpStarter\Foundation\Console\StorageUnlinkCommand;
use WpStarter\Foundation\Console\StubPublishCommand;
use WpStarter\Foundation\Console\TestMakeCommand;
use WpStarter\Foundation\Console\TraitMakeCommand;
use WpStarter\Foundation\Console\UpCommand;
use WpStarter\Foundation\Console\VendorPublishCommand;
use WpStarter\Foundation\Console\ViewCacheCommand;
use WpStarter\Foundation\Console\ViewClearCommand;
use WpStarter\Foundation\Console\ViewMakeCommand;
use WpStarter\Notifications\Console\NotificationTableCommand;
use WpStarter\Queue\Console\BatchesTableCommand;
use WpStarter\Queue\Console\ClearCommand as QueueClearCommand;
use WpStarter\Queue\Console\FailedTableCommand;
use WpStarter\Queue\Console\FlushFailedCommand as FlushFailedQueueCommand;
use WpStarter\Queue\Console\ForgetFailedCommand as ForgetFailedQueueCommand;
use WpStarter\Queue\Console\ListenCommand as QueueListenCommand;
use WpStarter\Queue\Console\ListFailedCommand as ListFailedQueueCommand;
use WpStarter\Queue\Console\MonitorCommand as QueueMonitorCommand;
use WpStarter\Queue\Console\PauseCommand as QueuePauseCommand;
use WpStarter\Queue\Console\PruneBatchesCommand as QueuePruneBatchesCommand;
use WpStarter\Queue\Console\PruneFailedJobsCommand as QueuePruneFailedJobsCommand;
use WpStarter\Queue\Console\RestartCommand as QueueRestartCommand;
use WpStarter\Queue\Console\ResumeCommand as QueueResumeCommand;
use WpStarter\Queue\Console\RetryBatchCommand as QueueRetryBatchCommand;
use WpStarter\Queue\Console\RetryCommand as QueueRetryCommand;
use WpStarter\Queue\Console\TableCommand;
use WpStarter\Queue\Console\WorkCommand as QueueWorkCommand;
use WpStarter\Routing\Console\ControllerMakeCommand;
use WpStarter\Routing\Console\MiddlewareMakeCommand;
use WpStarter\Session\Console\SessionTableCommand;
use WpStarter\Support\ServiceProvider;

class ArtisanServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'About' => AboutCommand::class,
        'CacheClear' => CacheClearCommand::class,
        'CacheForget' => CacheForgetCommand::class,
        'ClearCompiled' => ClearCompiledCommand::class,
        'ClearResets' => ClearResetsCommand::class,
        'ConfigCache' => ConfigCacheCommand::class,
        'ConfigClear' => ConfigClearCommand::class,
        'ConfigShow' => ConfigShowCommand::class,
        'Db' => DbCommand::class,
        'DbMonitor' => DatabaseMonitorCommand::class,
        'DbPrune' => PruneCommand::class,
        'DbShow' => ShowCommand::class,
        'DbTable' => DatabaseTableCommand::class,
        'DbWipe' => WipeCommand::class,
        'Down' => DownCommand::class,
        'Environment' => EnvironmentCommand::class,
        'EnvironmentDecrypt' => EnvironmentDecryptCommand::class,
        'EnvironmentEncrypt' => EnvironmentEncryptCommand::class,
        'EventCache' => EventCacheCommand::class,
        'EventClear' => EventClearCommand::class,
        'EventList' => EventListCommand::class,
        'InvokeSerializedClosure' => InvokeSerializedClosureCommand::class,
        'KeyGenerate' => KeyGenerateCommand::class,
        'Optimize' => OptimizeCommand::class,
        'OptimizeClear' => OptimizeClearCommand::class,
        'PackageDiscover' => PackageDiscoverCommand::class,
        'PruneStaleTagsCommand' => PruneStaleTagsCommand::class,
        'QueueClear' => QueueClearCommand::class,
        'QueueFailed' => ListFailedQueueCommand::class,
        'QueueFlush' => FlushFailedQueueCommand::class,
        'QueueForget' => ForgetFailedQueueCommand::class,
        'QueueListen' => QueueListenCommand::class,
        'QueueMonitor' => QueueMonitorCommand::class,
        'QueuePause' => QueuePauseCommand::class,
        'QueuePruneBatches' => QueuePruneBatchesCommand::class,
        'QueuePruneFailedJobs' => QueuePruneFailedJobsCommand::class,
        'QueueRestart' => QueueRestartCommand::class,
        'QueueResume' => QueueResumeCommand::class,
        'QueueRetry' => QueueRetryCommand::class,
        'QueueRetryBatch' => QueueRetryBatchCommand::class,
        'QueueWork' => QueueWorkCommand::class,
        'Reload' => ReloadCommand::class,
        'RouteCache' => RouteCacheCommand::class,
        'RouteClear' => RouteClearCommand::class,
        'RouteList' => RouteListCommand::class,
        'SchemaDump' => DumpCommand::class,
        'Seed' => SeedCommand::class,
        'ScheduleFinish' => ScheduleFinishCommand::class,
        'ScheduleList' => ScheduleListCommand::class,
        'ScheduleRun' => ScheduleRunCommand::class,
        'ScheduleClearCache' => ScheduleClearCacheCommand::class,
        'ScheduleTest' => ScheduleTestCommand::class,
        'ScheduleWork' => ScheduleWorkCommand::class,
        'ScheduleInterrupt' => ScheduleInterruptCommand::class,
        'ShowModel' => ShowModelCommand::class,
        'StorageLink' => StorageLinkCommand::class,
        'StorageUnlink' => StorageUnlinkCommand::class,
        'Up' => UpCommand::class,
        'ViewCache' => ViewCacheCommand::class,
        'ViewClear' => ViewClearCommand::class,
    ];

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $devCommands = [
        'ApiInstall' => ApiInstallCommand::class,
        'BroadcastingInstall' => BroadcastingInstallCommand::class,
        'CacheTable' => CacheTableCommand::class,
        'CastMake' => CastMakeCommand::class,
        'ChannelList' => ChannelListCommand::class,
        'ChannelMake' => ChannelMakeCommand::class,
        'ClassMake' => ClassMakeCommand::class,
        'ComponentMake' => ComponentMakeCommand::class,
        'ConfigMake' => ConfigMakeCommand::class,
        'ConfigPublish' => ConfigPublishCommand::class,
        'ConsoleMake' => ConsoleMakeCommand::class,
        'ControllerMake' => ControllerMakeCommand::class,
        'Docs' => DocsCommand::class,
        'EnumMake' => EnumMakeCommand::class,
        'EventGenerate' => EventGenerateCommand::class,
        'EventMake' => EventMakeCommand::class,
        'ExceptionMake' => ExceptionMakeCommand::class,
        'FactoryMake' => FactoryMakeCommand::class,
        'InterfaceMake' => InterfaceMakeCommand::class,
        'JobMake' => JobMakeCommand::class,
        'JobMiddlewareMake' => JobMiddlewareMakeCommand::class,
        'LangPublish' => LangPublishCommand::class,
        'ListenerMake' => ListenerMakeCommand::class,
        'MailMake' => MailMakeCommand::class,
        'MiddlewareMake' => MiddlewareMakeCommand::class,
        'ModelMake' => ModelMakeCommand::class,
        'NotificationMake' => NotificationMakeCommand::class,
        'NotificationTable' => NotificationTableCommand::class,
        'ObserverMake' => ObserverMakeCommand::class,
        'PolicyMake' => PolicyMakeCommand::class,
        'ProviderMake' => ProviderMakeCommand::class,
        'QueueFailedTable' => FailedTableCommand::class,
        'QueueTable' => TableCommand::class,
        'QueueBatchesTable' => BatchesTableCommand::class,
        'RequestMake' => RequestMakeCommand::class,
        'ResourceMake' => ResourceMakeCommand::class,
        'RuleMake' => RuleMakeCommand::class,
        'ScopeMake' => ScopeMakeCommand::class,
        'SeederMake' => SeederMakeCommand::class,
        'SessionTable' => SessionTableCommand::class,
        'Serve' => ServeCommand::class,
        'StubPublish' => StubPublishCommand::class,
        'TestMake' => TestMakeCommand::class,
        'TraitMake' => TraitMakeCommand::class,
        'VendorPublish' => VendorPublishCommand::class,
        'ViewMake' => ViewMakeCommand::class,
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands(array_merge(
            $this->commands,
            $this->devCommands
        ));

        Signals::resolveAvailabilityUsing(function () {
            return $this->app->runningInConsole()
                && ! $this->app->runningUnitTests()
                && extension_loaded('pcntl');
        });
    }

    /**
     * Register the given commands.
     *
     * @return void
     */
    protected function registerCommands(array $commands)
    {
        foreach ($commands as $commandName => $command) {
            $method = "register{$commandName}Command";

            if (method_exists($this, $method)) {
                $this->{$method}();
            } else {
                $this->app->singleton($command);
            }
        }

        $this->commands(array_values($commands));
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerAboutCommand()
    {
        $this->app->singleton(AboutCommand::class, function ($app) {
            return new AboutCommand($app['composer']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerCacheClearCommand()
    {
        $this->app->singleton(CacheClearCommand::class, function ($app) {
            return new CacheClearCommand($app['cache'], $app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerCacheForgetCommand()
    {
        $this->app->singleton(CacheForgetCommand::class, function ($app) {
            return new CacheForgetCommand($app['cache']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerCacheTableCommand()
    {
        $this->app->singleton(CacheTableCommand::class, function ($app) {
            return new CacheTableCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerCastMakeCommand()
    {
        $this->app->singleton(CastMakeCommand::class, function ($app) {
            return new CastMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerChannelMakeCommand()
    {
        $this->app->singleton(ChannelMakeCommand::class, function ($app) {
            return new ChannelMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerClassMakeCommand()
    {
        $this->app->singleton(ClassMakeCommand::class, function ($app) {
            return new ClassMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerComponentMakeCommand()
    {
        $this->app->singleton(ComponentMakeCommand::class, function ($app) {
            return new ComponentMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerConfigCacheCommand()
    {
        $this->app->singleton(ConfigCacheCommand::class, function ($app) {
            return new ConfigCacheCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerConfigClearCommand()
    {
        $this->app->singleton(ConfigClearCommand::class, function ($app) {
            return new ConfigClearCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerConfigMakeCommand()
    {
        $this->app->singleton(ConfigMakeCommand::class, function ($app) {
            return new ConfigMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerConfigPublishCommand()
    {
        $this->app->singleton(ConfigPublishCommand::class, function () {
            return new ConfigPublishCommand;
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerConsoleMakeCommand()
    {
        $this->app->singleton(ConsoleMakeCommand::class, function ($app) {
            return new ConsoleMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerControllerMakeCommand()
    {
        $this->app->singleton(ControllerMakeCommand::class, function ($app) {
            return new ControllerMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerEnumMakeCommand()
    {
        $this->app->singleton(EnumMakeCommand::class, function ($app) {
            return new EnumMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerEventMakeCommand()
    {
        $this->app->singleton(EventMakeCommand::class, function ($app) {
            return new EventMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerExceptionMakeCommand()
    {
        $this->app->singleton(ExceptionMakeCommand::class, function ($app) {
            return new ExceptionMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerFactoryMakeCommand()
    {
        $this->app->singleton(FactoryMakeCommand::class, function ($app) {
            return new FactoryMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerEventClearCommand()
    {
        $this->app->singleton(EventClearCommand::class, function ($app) {
            return new EventClearCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerInterfaceMakeCommand()
    {
        $this->app->singleton(InterfaceMakeCommand::class, function ($app) {
            return new InterfaceMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerJobMakeCommand()
    {
        $this->app->singleton(JobMakeCommand::class, function ($app) {
            return new JobMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerJobMiddlewareMakeCommand()
    {
        $this->app->singleton(JobMiddlewareMakeCommand::class, function ($app) {
            return new JobMiddlewareMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerListenerMakeCommand()
    {
        $this->app->singleton(ListenerMakeCommand::class, function ($app) {
            return new ListenerMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMailMakeCommand()
    {
        $this->app->singleton(MailMakeCommand::class, function ($app) {
            return new MailMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMiddlewareMakeCommand()
    {
        $this->app->singleton(MiddlewareMakeCommand::class, function ($app) {
            return new MiddlewareMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerModelMakeCommand()
    {
        $this->app->singleton(ModelMakeCommand::class, function ($app) {
            return new ModelMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerNotificationMakeCommand()
    {
        $this->app->singleton(NotificationMakeCommand::class, function ($app) {
            return new NotificationMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerNotificationTableCommand()
    {
        $this->app->singleton(NotificationTableCommand::class, function ($app) {
            return new NotificationTableCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerObserverMakeCommand()
    {
        $this->app->singleton(ObserverMakeCommand::class, function ($app) {
            return new ObserverMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerPolicyMakeCommand()
    {
        $this->app->singleton(PolicyMakeCommand::class, function ($app) {
            return new PolicyMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerProviderMakeCommand()
    {
        $this->app->singleton(ProviderMakeCommand::class, function ($app) {
            return new ProviderMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerQueueForgetCommand()
    {
        $this->app->singleton(ForgetFailedQueueCommand::class);
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerQueueListenCommand()
    {
        $this->app->singleton(QueueListenCommand::class, function ($app) {
            return new QueueListenCommand($app['queue.listener']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerQueueMonitorCommand()
    {
        $this->app->singleton(QueueMonitorCommand::class, function ($app) {
            return new QueueMonitorCommand($app['queue'], $app['events']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerQueuePruneBatchesCommand()
    {
        $this->app->singleton(QueuePruneBatchesCommand::class, function () {
            return new QueuePruneBatchesCommand;
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerQueuePruneFailedJobsCommand()
    {
        $this->app->singleton(QueuePruneFailedJobsCommand::class, function () {
            return new QueuePruneFailedJobsCommand;
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerQueueRestartCommand()
    {
        $this->app->singleton(QueueRestartCommand::class, function ($app) {
            return new QueueRestartCommand($app['cache.store']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerQueueWorkCommand()
    {
        $this->app->singleton(QueueWorkCommand::class, function ($app) {
            return new QueueWorkCommand($app['queue.worker'], $app['cache.store']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerQueueFailedTableCommand()
    {
        $this->app->singleton(FailedTableCommand::class, function ($app) {
            return new FailedTableCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerQueueTableCommand()
    {
        $this->app->singleton(TableCommand::class, function ($app) {
            return new TableCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerQueueBatchesTableCommand()
    {
        $this->app->singleton(BatchesTableCommand::class, function ($app) {
            return new BatchesTableCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerRequestMakeCommand()
    {
        $this->app->singleton(RequestMakeCommand::class, function ($app) {
            return new RequestMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerResourceMakeCommand()
    {
        $this->app->singleton(ResourceMakeCommand::class, function ($app) {
            return new ResourceMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerRuleMakeCommand()
    {
        $this->app->singleton(RuleMakeCommand::class, function ($app) {
            return new RuleMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerScopeMakeCommand()
    {
        $this->app->singleton(ScopeMakeCommand::class, function ($app) {
            return new ScopeMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerSeederMakeCommand()
    {
        $this->app->singleton(SeederMakeCommand::class, function ($app) {
            return new SeederMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerSessionTableCommand()
    {
        $this->app->singleton(SessionTableCommand::class, function ($app) {
            return new SessionTableCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerRouteCacheCommand()
    {
        $this->app->singleton(RouteCacheCommand::class, function ($app) {
            return new RouteCacheCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerRouteClearCommand()
    {
        $this->app->singleton(RouteClearCommand::class, function ($app) {
            return new RouteClearCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerRouteListCommand()
    {
        $this->app->singleton(RouteListCommand::class, function ($app) {
            return new RouteListCommand($app['router']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerSeedCommand()
    {
        $this->app->singleton(SeedCommand::class, function ($app) {
            return new SeedCommand($app['db']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerTestMakeCommand()
    {
        $this->app->singleton(TestMakeCommand::class, function ($app) {
            return new TestMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerTraitMakeCommand()
    {
        $this->app->singleton(TraitMakeCommand::class, function ($app) {
            return new TraitMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerVendorPublishCommand()
    {
        $this->app->singleton(VendorPublishCommand::class, function ($app) {
            return new VendorPublishCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerViewClearCommand()
    {
        $this->app->singleton(ViewClearCommand::class, function ($app) {
            return new ViewClearCommand($app['files']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_merge(array_values($this->commands), array_values($this->devCommands));
    }
}
