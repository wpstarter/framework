<?php

namespace WpStarter\Wordpress\Themes;

use WpStarter\Support\Fluent;

/**
 * @property string $Name
 * @property string $ThemeURI
 * @property string $Description
 * @property string $Author
 * @property string $AuthorURI
 * @property string $Version
 * @property string $Template The parent theme declared in style.css, or an empty string.
 * @property string $Status
 * @property array $Tags
 * @property string $TextDomain
 * @property string $DomainPath
 * @property string $RequiresWP
 * @property string $RequiresPHP
 * @property string $UpdateURI
 * @property string $Stylesheet The theme directory relative to the theme root.
 * @property string $NewVersion
 * @property string $UpdatePackage
 */
class Theme extends Fluent
{
}
