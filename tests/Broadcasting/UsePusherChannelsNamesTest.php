<?php

namespace WpStarter\Tests\Broadcasting;

use WpStarter\Broadcasting\Broadcasters\Broadcaster;
use WpStarter\Broadcasting\Broadcasters\UsePusherChannelConventions;
use PHPUnit\Framework\TestCase;

class UsePusherChannelsNamesTest extends TestCase
{
    /**
     * @dataProvider channelsProvider
     */
    public function testChannelNameNormalization($requestChannelName, $normalizedName)
    {
        $broadcaster = new FakeBroadcasterUsingPusherChannelsNames;

        $this->assertSame(
            $normalizedName,
            $broadcaster->normalizeChannelName($requestChannelName)
        );
    }

    public function testChannelNameNormalizationSpecialCase()
    {
        $broadcaster = new FakeBroadcasterUsingPusherChannelsNames;

        $this->assertSame(
            'private-123',
            $broadcaster->normalizeChannelName('private-encrypted-private-123')
        );
    }

    /**
     * @dataProvider channelsProvider
     */
    public function testIsGuardedChannel($requestChannelName, $_, $guarded)
    {
        $broadcaster = new FakeBroadcasterUsingPusherChannelsNames;

        $this->assertSame(
            $guarded,
            $broadcaster->isGuardedChannel($requestChannelName)
        );
    }

    public function channelsProvider()
    {
        $prefixesInfos = [
            ['prefix' => 'private-', 'guarded' => true],
            ['prefix' => 'private-encrypted-', 'guarded' => true],
            ['prefix' => 'presence-', 'guarded' => true],
            ['prefix' => '', 'guarded' => false],
        ];

        $channels = [
            'test',
            'test-channel',
            'test-private-channel',
            'test-presence-channel',
            'abcd.efgh',
            'abcd.efgh.ijkl',
            'test.{param}',
            'test-{param}',
            '{a}.{b}',
            '{a}-{b}',
            '{a}-{b}.{c}',
        ];

        $tests = [];
        foreach ($prefixesInfos as $prefixInfos) {
            foreach ($channels as $channel) {
                $tests[] = [
                    $prefixInfos['prefix'].$channel,
                    $channel,
                    $prefixInfos['guarded'],
                ];
            }
        }

        $tests[] = ['private-private-test', 'private-test', true];
        $tests[] = ['private-presence-test', 'presence-test', true];
        $tests[] = ['presence-private-test', 'private-test', true];
        $tests[] = ['presence-presence-test', 'presence-test', true];
        $tests[] = ['public-test', 'public-test', false];

        return $tests;
    }
}

class FakeBroadcasterUsingPusherChannelsNames extends Broadcaster
{
    use UsePusherChannelConventions;

    public function auth($request)
    {
        //
    }

    public function validAuthenticationResponse($request, $result)
    {
        //
    }

    public function broadcast(array $channels, $event, array $payload = [])
    {
        //
    }
}
