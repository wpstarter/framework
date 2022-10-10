<?php

namespace WpStarter\Wordpress\Bootstrap;

use ErrorException;
use Exception;
use Throwable;
use WpStarter\Contracts\Foundation\Application;
use WpStarter\Log\LogManager;

class HandleExceptions extends \WpStarter\Foundation\Bootstrap\HandleExceptions
{
    public function bootstrap(Application $app)
    {
        // Capture the last error that occurred before HandleExceptions loaded:
        $prior_error = error_get_last();
        parent::bootstrap($app);
        if($prior_error){//We cannot handle error now
            $this->app->booted(function()use($prior_error){
                $this->handleError($prior_error['type'],
                    $prior_error['message'],
                    $prior_error['file'],
                    $prior_error['line']);
            });
        }
    }

    /**
     * Report PHP deprecations, or convert PHP errors to ErrorException instances.
     *
     * @param  int  $level
     * @param  string  $message
     * @param  string  $file
     * @param  int  $line
     * @param  array  $context
     * @return void
     *
     * @throws \ErrorException
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if($this->isExternalPath($file)){
            $config=$this->app['config'];
            $debug=$config->get('app.debug_external');
            $debug_log=$config->get('app.debug_external_log',$debug);
            try {
                if($debug_log) {
                    if ($this->isDeprecation($level)) {
                        return $this->handleDeprecation($message, $file, $line);
                    }
                    $this->handleExternal($level, $message, $file , $line );
                }
            } catch (Exception $e) {
                //
            }
            if($debug) {
                throw new ErrorException($message, 0, $level, $file, $line);
            }
        }else {
            parent::handleError($level,$message,$file,$line,$context);
        }
    }

    public function handleExternal($level, $message, $file , $line )
    {
        if (! class_exists(LogManager::class)
            || $this->app->runningUnitTests()
        ) {
            return;
        }

        try {
            $logger = $this->app->make(LogManager::class);
        } catch (Exception $e) {
            return;
        }

        $this->ensureExternalLoggerIsConfigured();
        $log=$logger->channel('external');
        $log->warning(sprintf('%s in %s on line %s',
            $message, $file, $line
        ),['exception'=>new ErrorException($message, 0, $level, $file, $line)]);
    }


    /**
     * Ensure the "external" logger is configured.
     *
     * @return void
     */
    protected function ensureExternalLoggerIsConfigured()
    {
        ws_with($this->app['config'], function ($config) {
            if ($config->get('logging.channels.external')) {
                return;
            }

            $this->ensureNullLogDriverIsConfigured();

            $driver = $config->get('logging.external') ?? 'null';

            $config->set('logging.channels.external', $config->get("logging.channels.{$driver}"));
        });
    }

    /**
     * Handle an uncaught exception from the application.
     *
     * Note: Most exceptions can be handled via the try / catch block in
     * the HTTP and Console kernels. But, fatal error exceptions must
     * be handled differently since they are not normal exceptions.
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function handleException(Throwable $e)
    {
        if(!$this->app->runningInConsole() && !$this->app->hasBeenBootstrapped()){
            //App not bootstrapped, force debug
            $this->app['config']->set('app.debug',true);
        }
        parent::handleException($e);

    }
    protected function isExternalPath($path){
        return strpos($path,$this->app->basePath())===false;
    }

}
