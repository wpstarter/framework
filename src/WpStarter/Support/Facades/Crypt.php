<?php

namespace WpStarter\Support\Facades;

/**
 * @method static bool supported(string $key, string $cipher)
 * @method static string generateKey(string $cipher)
 * @method static string encrypt(mixed $value, bool $serialize = true)
 * @method static string encryptString(string $value)
 * @method static mixed decrypt(string $payload, bool $unserialize = true)
 * @method static string decryptString(string $payload)
 * @method static bool appearsEncrypted(mixed $value)
 * @method static string getKey()
 * @method static array getAllKeys()
 * @method static array getPreviousKeys()
 * @method static \WpStarter\Encryption\Encrypter previousKeys(array $keys)
 *
 * @see \WpStarter\Encryption\Encrypter
 */
class Crypt extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'encrypter';
    }
}
