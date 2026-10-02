<?php

namespace WpStarter\Tests\Integration\Container;

use WpStarter\Container\Attributes\Config;
use WpStarter\Contracts\Container\SelfBuilding;
use WpStarter\Support\Facades\Validator;
use WpStarter\Validation\ValidationException;
use Orchestra\Testbench\TestCase;

class BuildableIntegrationTest extends TestCase
{
    public function test_build_method_can_resolve_itself_via_container(): void
    {
        ws_config([
            'aim' => [
                'api_key' => 'api-key',
                'user_name' => 'cosmastech',
                'away_message' => [
                    'duration' => 500,
                    'body' => 'sad emo lyrics',
                ],
            ],
        ]);

        $config = $this->app->make(AolInstantMessengerConfig::class);

        $this->assertEquals(500, $config->awayMessageDuration);
        $this->assertEquals('sad emo lyrics', $config->awayMessage);
        $this->assertEquals('api-key', $config->apiKey);
        $this->assertEquals('cosmastech', $config->userName);

        ws_config(['aim.away_message.duration' => 5]);

        try {
            $this->app->make(AolInstantMessengerConfig::class);
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('away_message.duration', $exception->errors());
            $this->assertStringContainsString('60', $exception->errors()['away_message.duration'][0]);
        }
    }
}

class AolInstantMessengerConfig implements SelfBuilding
{
    public function __construct(
        #[Config('aim.api_key')]
        public string $apiKey,
        #[Config('aim.user_name')]
        public string $userName,
        #[Config('aim.away_message.duration')]
        public int $awayMessageDuration,
        #[Config('aim.away_message.body')]
        public string $awayMessage
    ) {
    }

    public static function newInstance()
    {
        Validator::make(ws_config('aim'), [
            'api-key' => 'string',
            'user_name' => 'string',
            'away_message' => 'array',
            'away_message.duration' => ['integer', 'min:60', 'max:3600'],
            'away_message.body' => ['string', 'min:1'],
        ])->validate();

        return app()->build(static::class);
    }
}
