<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Transport;

/**
 * User
 */
class User extends \XLite\Base
{
    public const GENDER_MALE   = 'm';
    public const GENDER_FEMALE = 'f';

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $token;

    /**
     * @var string
     */
    public $refreshToken;

    /**
     * @var integer
     */
    public $expires;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string
     */
    public $avatarURL;

    /**
     * @var string
     */
    public $gender;

    /**
     * @var string
     */
    public $locale;

    /**
     * @var string
     */
    public $accountURL;

    /**
     * @var integer
     */
    public $timezone;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        $result = null;

        if ($this->name) {
            $result = $this->name;
        }

        if (!$result && ($this->firstName || $this->lastName)) {
            $result = trim($this->firstName . ' ' . $this->lastName);
        }

        if (!$result) {
            $result = $this->id;
        }

        return $result;
    }
}
