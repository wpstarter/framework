<?php

namespace WpStarter\Tests\Auth;

use WpStarter\Auth\Middleware\EnsureEmailIsVerified;
use PHPUnit\Framework\TestCase;

class EnsureEmailIsVerifiedTest extends TestCase
{
    public function testItCanGenerateDefinitionViaStaticMethod()
    {
        $signature = EnsureEmailIsVerified::redirectTo('route.name');
        $this->assertSame('WpStarter\Auth\Middleware\EnsureEmailIsVerified:route.name', $signature);
    }
}
