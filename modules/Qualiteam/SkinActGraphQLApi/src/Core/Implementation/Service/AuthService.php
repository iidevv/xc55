<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Service;

use Exception;
use XLite\Core\Database;
use XLite\Model\Cart;
use XLite\Model\Profile;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Core\JWT;

class AuthService
{
    const ACCESS_ADMIN      = 'admin';
    const ACCESS_CUSTOMER   = 'customer';
    const ACCESS_ANONYMOUS  = 'anonymous';

    /**
     * @param string $login
     * @param string $password
     *
     * @return Profile
     */
    public function login($login, $password)
    {
        return \XLite\Core\Auth::getInstance()->login($login, $password);
    }

    /**
     * @param Profile $profile
     *
     * @return bool
     */
    public function loginProfile($profile)
    {
        return \XLite\Core\Auth::getInstance()->loginProfileById($profile->getProfileId());
    }

    /**
     * @param $id
     *
     * @return Profile
     */
    public function findProfileById($id)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile')->findOneBy(['profile_id' => $id]);
    }

    /**
     * @param Profile $result
     * @param Profile $merged
     *
     * @return bool
     * @throws Exception
     */
    public function mergeProfiles($result, $merged)
    {
        if (!$result || !$merged) {
            throw new Exception('Null profile to be merged');
        }

        /** @var Cart $cart */
        $cart = \XLite\Core\Database::getRepo('XLite\Model\Cart')->findOneByProfile($merged);

        if ($cart) {
            $cart->login($result);
            Database::getEM()->flush();
            return true;
        }

        return false;
    }

    /**
     * @param string $login
     *
     * @return bool
     */
    public function recoverPassword($login)
    {
        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($login);

        if (isset($profile) && !$profile->isAdmin()) {
            if (
                '' == $profile->getPasswordResetKey()
                || 0 == $profile->getPasswordResetKeyDate()
                || \XLite\Core\Converter::time() > $profile->getPasswordResetKeyDate()
            ) {
                // Generate new 'password reset key'
                $profile->setPasswordResetKey($this->generatePasswordResetKey());
                $profile->setPasswordResetKeyDate(\XLite\Core\Converter::time()
                    + \XLite\Controller\Customer\RecoverPassword::PASSWORD_RESET_KEY_EXP_TIME);

                $profile->update();
            }

            \XLite\Core\Mailer::sendRecoverPasswordRequest($profile, $profile->getPasswordResetKey());

            return true;
        }

        return false;
    }

    /**
     * @param Profile                                   $profile
     * @param \Qualiteam\SkinActGraphQLApi\Model\Device $device
     *
     * @return string
     */
    public function generateToken($profile = null, $device = null)
    {
        return JWT::encode($this->generateTokenPayload($profile, $device));
    }

    /**
     * @param Profile                                   $profile
     * @param \Qualiteam\SkinActGraphQLApi\Model\Device $device
     *
     * @return array
     */
    public function generateTokenPayload($profile = null, $device = null)
    {
        if (is_null($profile)) {
            $profile = \XLite\Core\Auth::getInstance()->getProfile();
        }

        if (is_null($profile)) {
            $token = [
                'access' => static::ACCESS_ANONYMOUS,
            ];
        } else {
            $token = [
                'user_id' => $profile->getProfileId(),
                'access' => $this->getAccessLevelForProfile($profile),
            ];
        }

        if (!is_null($device)) {
            $token['device_id'] = $device->getDeviceId();
        }

        return $this->sanitize($token);
    }

    /**
     * @param string $jwt
     *
     * @return bool
     */
    public function verifyToken($jwt)
    {
        if (!empty($jwt)) {
            return JWT::verify($jwt);
        }

        return false;
    }

    /**
     * @param string $jwt
     *
     * @return array
     *
     * @throws \UnexpectedValueException
     */
    public function getTokenPayload($jwt) {
        return JWT::extract($jwt);
    }

    /**
     * @param Profile $profile
     *
     * @return string
     */
    public function getAccessLevelForProfile(Profile $profile)
    {
        if ($profile->isAdmin()) {
            return static::ACCESS_ADMIN;
        } elseif (!$profile->getAnonymous()) {
            return static::ACCESS_CUSTOMER;
        }

        return static::ACCESS_ANONYMOUS;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function sanitize($data)
    {
        $data['expire'] = date('c');

        foreach ($data as $name => $value) {
            if (!in_array($name, static::getAvailableTokenFields())) {
                unset($data[$name]);
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function getAvailableTokenFields()
    {
        return [
            'user_id',
            'access',
            'device_id',
            'expire',
        ];
    }

    /**
     * Generates password reset key
     *
     * @return string
     */
    protected function generatePasswordResetKey()
    {
        $result = \XLite\Core\Auth::encryptPassword(microtime(), \XLite\Core\Auth::DEFAULT_HASH_ALGO);

        if (
            !empty($result)
            && 0 === strpos($result, \XLite\Core\Auth::DEFAULT_HASH_ALGO)
        ) {
            $result = substr($result, 7);
        }

        return $result;
    }
}
