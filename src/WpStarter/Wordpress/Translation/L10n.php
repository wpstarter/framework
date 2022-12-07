<?php

namespace WpStarter\Wordpress\Translation;

class L10n
{
    protected $defaultDomain;
    protected $locale;
    protected $fallbackLocale;
    public function __construct($config)
    {
        $this->locale=$config['app.locale'];
        $this->fallbackLocale=$config['app.fallback_locale'];
    }

    public function setLocale($locale){
        $this->locale=$locale;
        return $this;
    }
    public function getLocale(){
        if($this->locale){
            return $this->locale;
        }
        if(function_exists('determine_locale')) {
            return determine_locale();
        }
        return $this->fallbackLocale;
    }

    public function setDefaultDomain($defaultDomain){
        $this->defaultDomain=$defaultDomain;
        return $this;
    }
    function loadDomain($domain,$langDir){
        if(!$this->defaultDomain){
            $this->setDefaultDomain($domain);
        }
        $locale = $this->getLocale();

        $mofile = $domain . '-' . $locale . '.mo';

        // Try to load from the languages directory first.
        if ( load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $mofile ) ) {
            return true;
        }
        $mofile = $locale . '.mo';

        if ( load_textdomain( $domain, $langDir.'/'.$mofile ) ) {
            return true;
        }
    }
    /**
     * Retrieve the translation of $text.
     *
     * If there is no translation, or the text domain isn't loaded, the original text is returned.
     *
     *
     * @param string $text   Text to translate.
     * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
     *                       Default 'default'.
     * @return string Translated text.
     */
    function __( $text, $domain = null ) {
        return __( $text, $domain ?? $this->defaultDomain );
    }


    /**
     * Retrieve translated string with gettext context.
     *
     * Quite a few times, there will be collisions with similar translatable text
     * found in more than two places, but with different translated context.
     *
     * By including the context in the pot file, translators can translate the two
     * strings differently.
     *
     *
     * @param string $text    Text to translate.
     * @param string $context Context information for the translators.
     * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
     *                        Default 'default'.
     * @return string Translated context string without pipe.
     */
    function _x( $text, $context, $domain = null ) {
        return _x( $text, $context, $domain ?? $this->defaultDomain );
    }


    /**
     * Translates and retrieves the singular or plural form based on the supplied number.
     *
     * Used when you want to use the appropriate form of a string based on whether a
     * number is singular or plural.
     *
     * Example:
     *
     *     printf( _n( '%s person', '%s people', $count, 'text-domain' ), number_format_i18n( $count ) );
     *
     * @since 2.8.0
     * @since 5.5.0 Introduced ngettext-{$domain} filter.
     *
     * @param string $single The text to be used if the number is singular.
     * @param string $plural The text to be used if the number is plural.
     * @param int    $number The number to compare against to use either the singular or plural form.
     * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
     *                       Default 'default'.
     * @return string The translated singular or plural form.
     */
    function _n( $single, $plural, $number, $domain = null ) {
        return _n($single, $plural, $number, $domain ?? $this->defaultDomain);
    }

    /**
     * Translates and retrieves the singular or plural form based on the supplied number, with gettext context.
     *
     * This is a hybrid of _n() and _x(). It supports context and plurals.
     *
     * Used when you want to use the appropriate form of a string with context based on whether a
     * number is singular or plural.
     *
     * Example of a generic phrase which is disambiguated via the context parameter:
     *
     *     printf( _nx( '%s group', '%s groups', $people, 'group of people', 'text-domain' ), number_format_i18n( $people ) );
     *     printf( _nx( '%s group', '%s groups', $animals, 'group of animals', 'text-domain' ), number_format_i18n( $animals ) );
     *
     *
     * @param string $single  The text to be used if the number is singular.
     * @param string $plural  The text to be used if the number is plural.
     * @param int    $number  The number to compare against to use either the singular or plural form.
     * @param string $context Context information for the translators.
     * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
     *                        Default 'default'.
     * @return string The translated singular or plural form.
     */
    function _nx( $single, $plural, $number, $context, $domain = null ) {
        return _nx($single,$plural,$number,$context,$domain ?? $this->defaultDomain);
    }

    /**
     * Registers plural strings in POT file, but does not translate them.
     *
     * Used when you want to keep structures with translatable plural
     * strings and use them later when the number is known.
     *
     * Example:
     *
     *     $message = _n_noop( '%s post', '%s posts', 'text-domain' );
     *     ...
     *     printf( translate_nooped_plural( $message, $count, 'text-domain' ), number_format_i18n( $count ) );
     *
     *
     * @param string $singular Singular form to be localized.
     * @param string $plural   Plural form to be localized.
     * @param string $domain   Optional. Text domain. Unique identifier for retrieving translated strings.
     *                         Default null.
     * @return array {
     *     Array of translation information for the strings.
     *
     *     @type string $0        Singular form to be localized. No longer used.
     *     @type string $1        Plural form to be localized. No longer used.
     *     @type string $singular Singular form to be localized.
     *     @type string $plural   Plural form to be localized.
     *     @type null   $context  Context information for the translators.
     *     @type string $domain   Text domain.
     * }
     */
    function _n_noop( $singular, $plural, $domain = null ) {
        return _n_noop( $singular, $plural, $domain );
    }

    /**
     * Registers plural strings with gettext context in POT file, but does not translate them.
     *
     * Used when you want to keep structures with translatable plural
     * strings and use them later when the number is known.
     *
     * Example of a generic phrase which is disambiguated via the context parameter:
     *
     *     $messages = array(
     *          'people'  => _nx_noop( '%s group', '%s groups', 'people', 'text-domain' ),
     *          'animals' => _nx_noop( '%s group', '%s groups', 'animals', 'text-domain' ),
     *     );
     *     ...
     *     $message = $messages[ $type ];
     *     printf( translate_nooped_plural( $message, $count, 'text-domain' ), number_format_i18n( $count ) );
     *
     *
     * @param string $singular Singular form to be localized.
     * @param string $plural   Plural form to be localized.
     * @param string $context  Context information for the translators.
     * @param string $domain   Optional. Text domain. Unique identifier for retrieving translated strings.
     *                         Default null.
     * @return array {
     *     Array of translation information for the strings.
     *
     *     @type string      $0        Singular form to be localized. No longer used.
     *     @type string      $1        Plural form to be localized. No longer used.
     *     @type string      $2        Context information for the translators. No longer used.
     *     @type string      $singular Singular form to be localized.
     *     @type string      $plural   Plural form to be localized.
     *     @type string      $context  Context information for the translators.
     *     @type string|null $domain   Text domain.
     * }
     */
    function _nx_noop( $singular, $plural, $context, $domain = null ) {
        return _nx_noop($singular,$plural,$context,$domain);
    }
}