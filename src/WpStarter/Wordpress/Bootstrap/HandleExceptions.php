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
        if ($prior_error) {//We cannot handle error now
            $app->booted(function () use ($prior_error) {
                $this->handleError($prior_error['type'],
                    $prior_error['message'],
                    $prior_error['file'],
                    $prior_error['line']);
            });
        }
    }

    /**
     * Report PHP deprecations or convert PHP errors to ErrorException instances.
     *
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array $context
     * @return void
     *
     * @throws \ErrorException
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if ($this->isExternalPath($file)) {
            if (!$this->isSuppressed()) {
                $this->handleExternalError(new ErrorException($message, 0, $level, $file, $line));
            }
        } else {
            parent::handleError($level, $message, $file, $line, $context);
        }
    }

    protected function isSuppressed()
    {
        $report = error_reporting();
        return $report === 0 || $report === 4437;
    }

    /**
     * @param ErrorException $error
     * @return void|null
     * @throws ErrorException
     */
    public function handleExternalError(ErrorException $error)
    {
        self::$reservedMemory = null;
        $config = static::$app['config'];
        $level = $error->getSeverity();
        $message = $error->getMessage();
        $file = $error->getFile();
        $line = $error->getLine();
        try {
            if ($this->isDeprecation($level)) {
                $this->handleDeprecationError($message, $file, $line);
                return ;
            }
            $this->reportExternal($error);
        } catch (Exception $e) {
            //
        }
        if ($config->get('app.debug_external')) {
            if (static::$app->runningInConsole()) {
                $this->renderForConsole($error);
            } else {
                $this->renderHttpResponse($error);
            }
            exit;
        }
    }

    protected function reportExternal(ErrorException $error)
    {
        if (!class_exists(LogManager::class)
            || static::$app->runningUnitTests()
        ) {
            return;
        }
        try {
            $logger = static::$app->make(LogManager::class);
        } catch (Exception $e) {
            return;
        }

        $this->ensureExternalLoggerIsConfigured();

        ws_with($logger->channel('external'), function ($log) use ($error) {
            $log->warning(sprintf('%s in %s on line %s',
                $error->getMessage(), $error->getFile(), $error->getLine()
            ), ['exception' => $error]);
        });
    }


    /**
     * Ensure the "external" logger is configured.
     *
     * @return void
     */
    protected function ensureExternalLoggerIsConfigured()
    {
        ws_with(static::$app['config'], function ($config) {
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
     * @param \Throwable $e
     * @return void
     */
    public function handleException(Throwable $e)
    {
        if (!static::$app->runningInConsole() && !static::$app->hasBeenBootstrapped()) {
            //App isn't bootstrapped, force debug
            static::$app['config']->set('app.debug', true);
        }
        parent::handleException($e);

    }

    protected function isExternalPath($path)
    {
        return !str_contains($path, static::$app->basePath());
    }

}
