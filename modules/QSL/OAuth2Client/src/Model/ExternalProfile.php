<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * External profiles
 *
 * @ORM\Entity
 * @ORM\Table (name="qsl_oauth2_client_external_profiles")
 * @ORM\HasLifecycleCallbacks
 */
class ExternalProfile extends \XLite\Model\AEntity
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * External profile ID
     *
     * @var string
     *
     * @ORM\Column (type="string", length=64)
     */
    protected $external_id;

    /**
     * Token
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $token;

    /**
     * Refresh token
     *
     * @var string
     *
     * @ORM\Column (type="string", nullable=true)
     */
    protected $refreshToken;

    /**
     * Token expires
     *
     * @var string
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $expires;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $name;

    /**
     * Account link
     *
     * @var string
     *
     * @ORM\Column (type="string", nullable=true)
     */
    protected $link;

    /**
     * Create date
     *
     * @var integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $date;

    /**
     * Last login date
     *
     * @var integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true }, nullable=true)
     */
    protected $lastLoginDate;

    /**
     * Data
     *
     * @var array
     *
     * @ORM\Column (type="array")
     */
    protected $data = [];

    /**
     * Profile
     *
     * @var \XLite\Model\Profile
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Profile", inversedBy="external_profiles")
     * @ORM\JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")
     */
    protected $profile;

    /**
     * Provider
     *
     * @var \QSL\OAuth2Client\Model\Provider
     *
     * @ORM\ManyToOne  (targetEntity="QSL\OAuth2Client\Model\Provider")
     * @ORM\JoinColumn (name="provider_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $provider;

    /**
     * Create external profile
     *
     * @param \XLite\Model\Profile                               $profile  Profile
     * @param \QSL\OAuth2Client\Core\Transport\User $user     User data
     * @param \QSL\OAuth2Client\Model\Provider      $provider Provider
     *
     * @return static
     */
    public static function createByProfile(
        \XLite\Model\Profile $profile,
        \QSL\OAuth2Client\Core\Transport\User $user,
        \QSL\OAuth2Client\Model\Provider $provider
    ) {
        $eprofile = new static();
        $eprofile->setExternalId($user->id);
        $eprofile->setLink($user->accountURL);
        $eprofile->setName($user->getName());
        $eprofile->setToken($user->token);
        $eprofile->setRefreshToken($user->refreshToken);
        $eprofile->setExpires($user->expires);

        $eprofile->setProfile($profile);
        $eprofile->setProvider($provider);
        $profile->addExternalProfiles($eprofile);

        \XLite\Core\Database::getEM()->persist($eprofile);

        return $eprofile;
    }

    /**
     * Create profile and external profile
     *
     * @param \QSL\OAuth2Client\Core\Transport\User $user     User data
     * @param \QSL\OAuth2Client\Model\Provider      $provider Provider
     *
     * @return static
     */
    public static function createBoth(
        \QSL\OAuth2Client\Core\Transport\User $user,
        \QSL\OAuth2Client\Model\Provider $provider
    ) {
        $eprofile = null;

        $email = $user->email;

        if ($email) {
            $eprofile = new static();
            $eprofile->setExternalId($user->id);
            $eprofile->setLink($user->accountURL);
            $eprofile->setName($user->getName());
            $eprofile->setToken($user->token);
            $eprofile->setRefreshToken($user->refreshToken);
            $eprofile->setExpires($user->expires);

            $eprofile->setProvider($provider);

            \XLite\Core\Database::getEM()->persist($eprofile);

            $profile = new \XLite\Model\Profile();
            $profile->setLogin($email);
            $password = \XLite\Core\Database::getRepo('XLite\Model\Profile')->generatePassword();
            $profile->setPassword(\XLite\Core\Auth::encryptPassword($password));
            $profile->create();

            \XLite\Core\Database::getEM()->persist($profile);

            $eprofile->setProfile($profile);
            $profile->addExternalProfiles($eprofile);
        }

        return $eprofile;
    }

    /**
     * Prepare date
     *
     * @ORM\PrePersist
     */
    public function prepareDate()
    {
        if (!$this->getDate()) {
            $this->setDate(\XLite\Core\Converter::time());
        }
    }

    /**
     * Get external account link
     *
     * @return string
     */
    public function getAccountLink()
    {
        return $this->getLink();
    }

    /**
     * Request
     *
     * @param string $url     URL
     * @param string $method  Request method OPTIONAL
     * @param array  $options Options OPTIONAL
     *
     * @return mixed
     */
    public function request($url, $method = 'GET', array $options = [])
    {
        return $this->getProvider()->getWrapper()->requestAuthenticated($url, $this->getRoken(), $method, $options);
    }

    // {{{ Getters / setters

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getExternalId()
    {
        return $this->external_id;
    }

    /**
     * @param string $external_id
     *
     * @return static
     */
    public function setExternalId($external_id)
    {
        $this->external_id = $external_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return static
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     *
     * @return static
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @param string $expires
     *
     * @return static
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return static
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     *
     * @return static
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param int $date
     *
     * @return static
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getLastLoginDate()
    {
        return $this->lastLoginDate;
    }

    /**
     * @param int $lastLoginDate
     *
     * @return static
     */
    public function setLastLoginDate($lastLoginDate)
    {
        $this->lastLoginDate = $lastLoginDate;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param \XLite\Model\Profile $profile
     *
     * @return static
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param Provider $provider
     *
     * @return static
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    // }}}
}
