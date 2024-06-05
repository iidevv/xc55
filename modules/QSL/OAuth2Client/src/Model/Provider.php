<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Provider
 *
 * @ORM\Entity
 * @ORM\Table (name="qsl_oauth2_client_providers")
 */
class Provider extends \XLite\Model\Base\I18n
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
     * Service name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32)
     */
    protected $service_name;

    /**
     * Class name
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $class_name;

    /**
     * Display in header
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $display_in_header = false;

    /**
     * Display in checkout
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $display_in_checkout = false;

    /**
     * Enabled
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = false;

    /**
     * Position
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * Link to documentation
     *
     * @var string
     *
     * @ORM\Column (type="string", nullable=true)
     */
    protected $documentationLink;

    /**
     * Link to application create page
     *
     * @var string
     *
     * @ORM\Column (type="string", nullable=true)
     */
    protected $applicationCreateLink;

    /**
     * Icon short path
     *
     * @var string
     *
     * @ORM\Column (type="string", nullable=true)
     */
    protected $iconPath;

    /**
     * Settings
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\OAuth2Client\Model\ProviderSetting", mappedBy="provider", cascade={"all"})
     */
    protected $settings;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\OAuth2Client\Model\ProviderTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * @inheritdoc
     */
    public function __construct(array $data = [])
    {
        $this->settings = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get wrapper
     *
     * @return \QSL\OAuth2Client\Core\Wrapper\AWrapper
     */
    public function getWrapper()
    {
        $class = $this->getClassName();

        return $class::getInstance()->assignProvider($this);
    }

    /**
     * Get setting
     *
     * @param string $name Setting name
     *
     * @return mixed
     */
    public function getSetting($name)
    {
        $result = null;
        foreach ($this->getSettings() as $setting) {
            if ($setting->getName() == $name) {
                $result = $setting->getValue();
                break;
            }
        }

        return $result;
    }

    /**
     * Set setting
     *
     * @param string $name  Setting name
     * @param mixed  $value Value
     *
     * @return boolean
     */
    public function setSetting($name, $value)
    {
        $result = false;
        foreach ($this->getSettings() as $setting) {
            if ($setting->getName() == $name) {
                $setting->setValue($value);
                $result = true;
                break;
            }
        }

        if (!$result && $this->getWrapper()->isSetting($name)) {
            $setting = new \QSL\OAuth2Client\Model\ProviderSetting();
            $setting->setName($name);
            $setting->setValue($value);
            $this->addSettings($setting);
            $setting->setProvider($this);
            \XLite\Core\Database::getEM()->persist($setting);
        }

        return $result;
    }

    /**
     * Get external profile by user's profile
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return \QSL\OAuth2Client\Model\ExternalProfile
     */
    public function getExternalProfileByProfile(\XLite\Model\Profile $profile)
    {
        $result = null;
        foreach ($profile->getExternalProfiles() as $e) {
            if ($e->getProvider()->getServiceName() == $this->getServiceName()) {
                $result = $e;
                break;
            }
        }

        return $result;
    }

    /**
     * Check - widget is visible or not
     *
     * @param string $placement Placement
     *
     * @return boolean
     */
    public function isWidgetVisible($placement)
    {
        $result = $this->getWrapper()->isVisible();
        if ($result) {
            switch ($placement) {
                case 'header':
                    $result = $this->getDisplayInHeader();
                    break;

                case 'checkout':
                    $result = $this->getDisplayInCheckout();
                    break;

                default:
            }
        }

        return $result;
    }

    /**
     * Get authorization callback URL
     *
     * @return string
     */
    public function getAuthCallbackURL()
    {
        return $this->getWrapper()->getRedirectURL();
    }

    /**
     * Get authorization callback URL (default)
     *
     * @return string
     */
    public function getRedirectURL()
    {
        return \XLite\Core\URLManager::getShopURL(
            \XLite\Core\Converter::buildURL('oauth2return', null, ['provider' => $this->getServiceName()], \XLite::CART_SELF),
            \XLite\Core\Config::getInstance()->Security->customer_security,
            [],
            null,
            false
        );
    }

    /**
     * Get public icon URL
     *
     * @return null|string
     */
    public function getPublicIcon()
    {
        return $this->getIconPath()
            ? \XLite\Core\Layout::getInstance()->getResourceWebPath($this->getIconPath(), \XLite\Core\Layout::WEB_PATH_OUTPUT_URL)
            : null;
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
    public function getServiceName()
    {
        return $this->service_name;
    }

    /**
     * @param string $service_name
     *
     * @return static
     */
    public function setServiceName($service_name)
    {
        $this->service_name = $service_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * @param string $class_name
     *
     * @return static
     */
    public function setClassName($class_name)
    {
        $this->class_name = $class_name;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getDisplayInHeader()
    {
        return $this->display_in_header;
    }

    /**
     * @param boolean $display_in_header
     *
     * @return static
     */
    public function setDisplayInHeader($display_in_header)
    {
        $this->display_in_header = $display_in_header;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getDisplayInCheckout()
    {
        return $this->display_in_checkout;
    }

    /**
     * @param boolean $display_in_checkout
     *
     * @return static
     */
    public function setDisplayInCheckout($display_in_checkout)
    {
        $this->display_in_checkout = $display_in_checkout;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     *
     * @return static
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return static
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return string
     */
    public function getDocumentationLink()
    {
        return $this->documentationLink;
    }

    /**
     * @param string $documentationLink
     *
     * @return static
     */
    public function setDocumentationLink($documentationLink)
    {
        $this->documentationLink = $documentationLink;

        return $this;
    }

    /**
     * @return string
     */
    public function getApplicationCreateLink()
    {
        return $this->applicationCreateLink;
    }

    /**
     * @param string $applicationCreateLink
     *
     * @return static
     */
    public function setApplicationCreateLink($applicationCreateLink)
    {
        $this->applicationCreateLink = $applicationCreateLink;

        return $this;
    }

    /**
     * @return string
     */
    public function getIconPath()
    {
        return $this->iconPath;
    }

    /**
     * @param string $iconPath
     *
     * @return static
     */
    public function setIconPath($iconPath)
    {
        $this->iconPath = $iconPath;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param \QSL\OAuth2Client\Model\ProviderSetting $setting
     *
     * @return static
     */
    public function addSettings(\QSL\OAuth2Client\Model\ProviderSetting $setting)
    {
        $this->settings[] = $setting;

        return $this;
    }

    // }}}
}
