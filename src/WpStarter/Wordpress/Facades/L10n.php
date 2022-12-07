<?php

namespace WpStarter\Wordpress\Facades;

use WpStarter\Support\Facades\Facade;

/**
 * @method static L10n setDefaultDomain($domain)
 * @method static L10n setLocale($locale)
 * @method static L10n loadDomain($domain,$moFile)
 * @method static string __( $text, $domain = null )
 * @method static string _x( $text, $context, $domain = null )
 * @method static string _n( $single, $plural, $number, $domain = null )
 * @method static string _nx( $single, $plural, $number, $context, $domain = null )
 */
class L10n extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'l10n';
    }
}