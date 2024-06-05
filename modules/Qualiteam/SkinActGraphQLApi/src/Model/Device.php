<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Model;

use Doctrine\ORM\Mapping as ORM;



/**
 * Device
 *
 * @ORM\Entity
 * @ORM\Table  (name="devices",
 *      indexes={
 *          @ORM\Index (name="unique_id", columns={"unique_id"}),
 *      },
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="device_id", columns={"app_id", "unique_id"})
 *      }
 * )
 */
class Device extends \XLite\Model\AEntity
{
    const OS_ANDROID    = 'android';
    const OS_IOS        = 'ios';

    /**
     * Device ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={"unsigned": true})
     */
    protected $device_id;

    /**
     * Unique device hash to use instead of ID
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $unique_id;

    /**
     * Push ID
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $push_id = '';

    /**
     * Application ID
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32)
     */
    protected $app_id;

    /**
     * Device manufacturer
     *
     * @var string
     *
     * @ORM\Column (type="string", length=8)
     */
    protected $app_version = '';

    /**
     * Platform
     *
     * @var string
     *
     * @ORM\Column (type="string", length=16)
     */
    protected $platform;

    /**
     * Device manufacturer
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32)
     */
    protected $system_name = '';

    /**
     * Device manufacturer
     *
     * @var string
     *
     * @ORM\Column (type="string", length=8)
     */
    protected $system_version = '';

    /**
     * Device manufacturer
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32)
     */
    protected $device_name = '';

    /**
     * Device manufacturer
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32)
     */
    protected $manufacturer = '';

    /**
     * Device manufacturer
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32)
     */
    protected $model = '';

    /**
     * Item order
     *
     * @var \XLite\Model\Profile
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Profile", inversedBy="devices")
     * @ORM\JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")
     */
    protected $profile;

    /**
     * Get device data for JSON API
     *
     * @return array
     */
    public function getDeviceData()
    {
        return array(
            'id'            => $this->getDeviceId(),
            'push_id'       => $this->getPushId(),
            'manufacturer'  => $this->getManufacturer(),
            'model'         => $this->getModel(),
            'device_name'   => $this->getDeviceName(),
            'system'        => $this->getSystemName(),
            'version'       => $this->getSystemVersion(),
        );
    }

    //<editor-fold desc="Getters/Setters">
    /**
     * Get device ID
     *
     * @return integer
     */
    public function getDeviceId()
    {
        return $this->device_id;
    }

    /**
     * Set device ID
     *
     * @param integer $device_id
     *
     * @return void
     */
    public function setDeviceId($device_id)
    {
        $this->device_id = $device_id;
    }

    /**
     * Get unique ID
     *
     * @return string
     */
    public function getUniqueId()
    {
        return $this->unique_id;
    }

    /**
     * Set unique ID
     *
     * @param string $unique_id
     *
     * @return void
     */
    public function setUniqueId($unique_id)
    {
        $this->unique_id = $unique_id;
    }

    /**
     * Get device push ID
     *
     * @return string
     */
    public function getPushId()
    {
        return $this->push_id;
    }

    /**
     * Set device push ID
     *
     * @param string $push_id
     *
     * @return void
     */
    public function setPushId($push_id)
    {
        $this->push_id = $push_id;
    }

    /**
     * Get application ID
     *
     * @return string
     */
    public function getAppId()
    {
        return $this->app_id;
    }

    /**
     * Set application ID
     *
     * @param string $app_id
     *
     * @return void
     */
    public function setAppId($app_id)
    {
        $this->app_id = $app_id;
    }

    /**
     * Get application version
     *
     * @return string
     */
    public function getAppVersion()
    {
        return $this->app_version;
    }

    /**
     * Set application version
     *
     * @param string $app_version
     *
     * @return void
     */
    public function setAppVersion($app_version)
    {
        $this->app_version = $app_version;
    }

    /**
     * Get device platform
     *
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Set device platform
     *
     * @param string $platform
     *
     * @return void
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;
    }

    /**
     * Get device system name
     *
     * @return string
     */
    public function getSystemName()
    {
        return $this->system_name;
    }

    /**
     * Set device system name
     *
     * @param string $system_name
     *
     * @return void
     */
    public function setSystemName($system_name)
    {
        $this->system_name = $system_name;
    }

    /**
     * Get device system version
     *
     * @return string
     */
    public function getSystemVersion()
    {
        return $this->system_version;
    }

    /**
     * Set device system version
     *
     * @param string $system_version
     *
     * @return void
     */
    public function setSystemVersion($system_version)
    {
        $this->system_version = $system_version;
    }

    /**
     * Get device name
     *
     * @return string
     */
    public function getDeviceName()
    {
        return $this->device_name;
    }

    /**
     * Set device name
     *
     * @param string $device_name
     *
     * @return void
     */
    public function setDeviceName($device_name)
    {
        $this->device_name = $device_name;
    }

    /**
     * Get device manufacturer
     *
     * @return string
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Set device manufacturer
     *
     * @param string $manufacturer
     *
     * @return void
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * Get device model
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set device model
     *
     * @param string $model
     *
     * @return void
     */
    public function setModel($model)
    {
        $this->model = $model;
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
     * @return self
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
        return $this;
    }
    //</editor-fold>
}
