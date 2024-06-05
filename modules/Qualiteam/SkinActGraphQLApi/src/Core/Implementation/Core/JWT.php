<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Core;

use Firebase;
use Includes\Logger\LoggerFactory;
use XLite\Core\Cache\ExecuteCached;

/**
 * Class Output
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Core
 */
class JWT
{
    const OPENSSL_ALG = 'RS512';
    const FALLBACK_ALG = 'HS512';

    /**
     * Generate secure data for JWT tokens
     *
     * @return void
     */
    public static function generateSecureData()
    {
        if (static::checkOpenSSL()) {
            $keys = static::generateOpenSSLKeyPair();

            $configData = [
                'jwtPublicKey'  => $keys['public'],
                'jwtPrivateKey' => $keys['private'],
                'jwtAlgorithm'  => static::OPENSSL_ALG,
            ];
        } else {
            $configData = [
                'jwtPublicKey'  => static::generateSHA512Key(),
                'jwtPrivateKey' => '',
                'jwtAlgorithm'  => static::FALLBACK_ALG,
            ];
        }

        foreach ($configData as $name => $value) {
            $configEntity = \XLite\Core\Database::getRepo('XLite\Model\Config')->findOneBy(
                array(
                    'category'  => 'Qualiteam\SkinActGraphQLApi',
                    'name'      => $name,
                )
            );

            if ($configEntity) {
                $configEntity->setValue($value);
                \XLite\Core\Database::getEM()->persist($configEntity);
            }
        }

        try {
            \XLite\Core\Database::getEM()->flush();
            \XLite\Core\Database::getEM()->clear();
            \XLite\Core\Config::updateInstance();
        } catch (\Doctrine\ORM\OptimisticLockException $error) {
            // TODO: decide how to handle this type of exception
        }
    }

    /**
     * @param array $token
     *
     * @return string
     */
    public static function encode($token)
    {
        $config = static::getJWTConfig();

        if ($config['alg'] === static::OPENSSL_ALG) {
            return static::encodeOpenSSL($token, $config['private']);
        }

        return static::encodeSHA512($token, $config['public']);
    }

    /**
     * @param string $jwt
     *
     * @return array
     *
     * @throws \UnexpectedValueException
     * @throws \DomainException
     */
    public static function decode($jwt)
    {
        return ExecuteCached::executeCachedRuntime(function () use ($jwt) {
            $config = static::getJWTConfig();

            if ($config['alg'] === static::OPENSSL_ALG) {
                return static::decodeOpenSSL($jwt, $config['public']);
            }

            return static::decodeSHA512($jwt, $config['public']);
        }, ['jwt' => md5($jwt)]);
    }

    /**
     * @param string $jwt
     *
     * @return array
     *
     * @throws \UnexpectedValueException
     */
    public static function decompose($jwt)
    {
        $tks = explode('.', $jwt);

        if (count($tks) !== 3) {
            throw new \UnexpectedValueException('Wrong number of segments');
        }

        list($header64, $body64, $sig64) = $tks;

        try {
            if (null === ($header = Firebase\JWT\JWT::jsonDecode(Firebase\JWT\JWT::urlsafeB64Decode($header64)))) {
                throw new \UnexpectedValueException('Invalid header encoding');
            }

            if (null === ($body = Firebase\JWT\JWT::jsonDecode(Firebase\JWT\JWT::urlsafeB64Decode($body64)))) {
                throw new \UnexpectedValueException('Invalid claims encoding');
            }

            if (false === ($sig = Firebase\JWT\JWT::urlsafeB64Decode($sig64))) {
                throw new \UnexpectedValueException('Invalid signature encoding');
            }
        } catch (\UnexpectedValueException $error) {
            throw new \UnexpectedValueException('Invalid token', 0, $error);
        } catch (\DomainException $error) {
            throw new \UnexpectedValueException('Invalid token', 0, $error);
        }

        return [
            'header'    => (array) $header,
            'header64'  => $header64,
            'body'      => (array) $body,
            'body64'    => $body64,
            'sig'       => $sig,
            'sig64'     => $sig64,
        ];
    }

    /**
     * @param string $jwt
     *
     * @return array
     *
     * @throws \UnexpectedValueException
     */
    public static function extract($jwt)
    {
        $parts = static::decompose($jwt);

        return $parts['body'];
    }

    /**
     * @param string $jwt
     *
     * @return boolean
     */
    public static function verify($jwt)
    {
        try {
            static::decode($jwt);

        } catch (\UnexpectedValueException $error) {
            return false;
        } catch (\DomainException $error) {
            return false;
        }

        return true;
    }

    /**
     * @return boolean
     */
    protected static function checkOpenSSL()
    {
        // Perform dry run of OpenSSL key generation because sometimes
        // extension cant function properly even if it is loaded
        if (extension_loaded('openssl')) {
            $config = array(
                'digest_alg' => 'sha512',
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            );

            // Create the private and public key
            $key = openssl_pkey_new($config);

            if ($key === false) {
// @TODO: [FIXME]. Change logCustom according : https://www.notion.so/xc-eng/Logging-958173acf98a4644ab4d6a09e544c347#1917beaee2a64dcebf165e053b2cffdf
               // \XLite\Logger::logCustom('openssl', openssl_error_string());

                LoggerFactory::getLogger(['name' => 'xlite'])->log(333, openssl_error_string());
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    protected static function generateOpenSSLKeyPair()
    {
        $config = array(
            'digest_alg'        => 'sha512',
            'private_key_bits'  => 2048,
            'private_key_type'  => OPENSSL_KEYTYPE_RSA,
        );

        // Create the private and public key
        $key = openssl_pkey_new($config);

        $keyData = openssl_pkey_get_details($key);
        openssl_pkey_export($key, $pKey);

        return [
            'public'    => $keyData['key'],
            'private'   => $pKey,
        ];
    }

    /**
     * @return string
     */
    protected static function generateSHA512Key()
    {
        $symbols = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';

        $count = strlen($symbols) - 1;
        $str = '';

        for ($i = 0; $i < 64; $i++) {
            $str .= $symbols[mt_rand(0, $count)];
        }

        return hash('sha256', $str);
    }

    /**
     * @return array
     */
    protected static function getJWTConfig()
    {
        if (!in_array(
            \XLite\Core\Config::getInstance()->Qualiteam->SkinActGraphQLApi->jwtAlgorithm,
            [ static::OPENSSL_ALG, static::FALLBACK_ALG ]
        )) {
            static::generateSecureData();
        }

        return [
            'public'    => \XLite\Core\Config::getInstance()->Qualiteam->SkinActGraphQLApi->jwtPublicKey,
            'private'   => \XLite\Core\Config::getInstance()->Qualiteam->SkinActGraphQLApi->jwtPrivateKey,
            'alg'       => \XLite\Core\Config::getInstance()->Qualiteam->SkinActGraphQLApi->jwtAlgorithm,
        ];
    }

    /**
     * @param array  $token
     * @param string $privateKey
     *
     * @return string
     */
    protected static function encodeOpenSSL($token, $privateKey)
    {
        return Firebase\JWT\JWT::encode($token, $privateKey, static::OPENSSL_ALG);
    }

    /**
     * @param string $jwt
     * @param string $publicKey
     *
     * @return array
     *
     * @throws \UnexpectedValueException
     * @throws \DomainException
     */
    protected static function decodeOpenSSL($jwt, $publicKey)
    {
        return (array) Firebase\JWT\JWT::decode($jwt, $publicKey, [ static::OPENSSL_ALG ]);
    }

    /**
     * @param array  $token
     * @param string $shaKey
     *
     * @return string
     */
    protected static function encodeSHA512($token, $shaKey)
    {
        return Firebase\JWT\JWT::encode($token, $shaKey, static::FALLBACK_ALG);
    }

    /**
     * @param string $jwt
     * @param string $shaKey
     *
     * @return array
     *
     * @throws \UnexpectedValueException
     * @throws \DomainException
     */
    protected static function decodeSHA512($jwt, $shaKey)
    {
        return (array) Firebase\JWT\JWT::decode($jwt, $shaKey, [ static::FALLBACK_ALG ]);
    }
}
