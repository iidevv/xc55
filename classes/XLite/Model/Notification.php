<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Membership
 *
 * @ORM\Entity
 * @ORM\Table (name="notifications")
 */
class Notification extends \XLite\Model\Base\I18n implements \XLite\Model\Base\IModuleRelatedEntity
{
    /**
     * Notification templates directory
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column (type="string", length=255, unique=true)
     */
    protected $templatesDirectory = '';

    /**
     * Is available for admin
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $availableForAdmin = false;

    /**
     * Is available for admin
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $availableForCustomer = false;

    /**
     * Is available for admin
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabledForAdmin = false;

    /**
     * Is available for admin
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabledForCustomer = false;

    /**
     * Is header enabled for admin
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $adminHeaderEnabled = true;

    /**
     * Is greeting enabled for admin
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $adminGreetingEnabled = true;

    /**
     * Is signature enabled for admin
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $adminSignatureEnabled = true;

    /**
     * Is header enabled for customer
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $customerHeaderEnabled = true;

    /**
     * Is greeting enabled for customer
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $customerGreetingEnabled = true;

    /**
     * Is signature enabled for customer
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $customerSignatureEnabled = true;

    /**
     * A position of the notification among other notifications
     *
     * @var integer
     *
     * @ORM\Column (type="integer", options={"default" : 0})
     */
    protected $position = 0;

    /**
     * Is signature editable
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $editable = false;

    /**
     * @ORM\Column (type="string", nullable=true)
     */
    protected ?string $module;

    /**
     * @var bool
     *
     * @ORM\Column (type="boolean")
     */
    protected $available = true;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\NotificationTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Set templatesDirectory
     *
     * @param string $templatesDirectory
     *
     * @return Notification
     */
    public function setTemplatesDirectory($templatesDirectory)
    {
        $this->templatesDirectory = $templatesDirectory;

        return $this;
    }

    /**
     * Get templatesDirectory
     *
     * @return string
     */
    public function getTemplatesDirectory()
    {
        return $this->templatesDirectory;
    }

    /**
     * Set availableForAdmin
     *
     * @param boolean $availableForAdmin
     *
     * @return Notification
     */
    public function setAvailableForAdmin($availableForAdmin)
    {
        $this->availableForAdmin = $availableForAdmin;

        return $this;
    }

    /**
     * Get availableForAdmin
     *
     * @return boolean
     */
    public function getAvailableForAdmin()
    {
        return $this->availableForAdmin;
    }

    /**
     * Set availableForCustomer
     *
     * @param boolean $availableForCustomer
     *
     * @return Notification
     */
    public function setAvailableForCustomer($availableForCustomer)
    {
        $this->availableForCustomer = $availableForCustomer;

        return $this;
    }

    /**
     * Get availableForCustomer
     *
     * @return boolean
     */
    public function getAvailableForCustomer()
    {
        return $this->availableForCustomer;
    }

    /**
     * Set enabledForAdmin
     *
     * @param boolean $enabledForAdmin
     *
     * @return Notification
     */
    public function setEnabledForAdmin($enabledForAdmin)
    {
        $this->enabledForAdmin = $enabledForAdmin;

        return $this;
    }

    /**
     * Get enabledForAdmin
     *
     * @return boolean
     */
    public function getEnabledForAdmin()
    {
        return $this->enabledForAdmin;
    }

    /**
     * Set enabledForCustomer
     *
     * @param boolean $enabledForCustomer
     *
     * @return Notification
     */
    public function setEnabledForCustomer($enabledForCustomer)
    {
        $this->enabledForCustomer = $enabledForCustomer;

        return $this;
    }

    /**
     * Get enabledForCustomer
     *
     * @return boolean
     */
    public function getEnabledForCustomer()
    {
        return $this->enabledForCustomer;
    }

    /**
     * @return boolean
     */
    public function getAdminHeaderEnabled()
    {
        return $this->adminHeaderEnabled;
    }

    /**
     * @param boolean $adminHeaderEnabled
     */
    public function setAdminHeaderEnabled($adminHeaderEnabled)
    {
        $this->adminHeaderEnabled = $adminHeaderEnabled;
    }

    /**
     * @return boolean
     */
    public function getAdminGreetingEnabled()
    {
        return $this->adminGreetingEnabled;
    }

    /**
     * @param boolean $adminGreetingEnabled
     */
    public function setAdminGreetingEnabled($adminGreetingEnabled)
    {
        $this->adminGreetingEnabled = $adminGreetingEnabled;
    }

    /**
     * @return boolean
     */
    public function getAdminSignatureEnabled()
    {
        return $this->adminSignatureEnabled;
    }

    /**
     * @param boolean $adminSignatureEnabled
     */
    public function setAdminSignatureEnabled($adminSignatureEnabled)
    {
        $this->adminSignatureEnabled = $adminSignatureEnabled;
    }

    /**
     * @return boolean
     */
    public function getCustomerHeaderEnabled()
    {
        return $this->customerHeaderEnabled;
    }

    /**
     * @param boolean $customerHeaderEnabled
     */
    public function setCustomerHeaderEnabled($customerHeaderEnabled)
    {
        $this->customerHeaderEnabled = $customerHeaderEnabled;
    }

    /**
     * @return boolean
     */
    public function getCustomerGreetingEnabled()
    {
        return $this->customerGreetingEnabled;
    }

    /**
     * @param boolean $customerGreetingEnabled
     */
    public function setCustomerGreetingEnabled($customerGreetingEnabled)
    {
        $this->customerGreetingEnabled = $customerGreetingEnabled;
    }

    /**
     * @return boolean
     */
    public function getCustomerSignatureEnabled()
    {
        return $this->customerSignatureEnabled;
    }

    /**
     * @param boolean $customerSignatureEnabled
     */
    public function setCustomerSignatureEnabled($customerSignatureEnabled)
    {
        $this->customerSignatureEnabled = $customerSignatureEnabled;
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
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Return Editable
     *
     * @return bool
     */
    public function isEditable()
    {
        return $this->editable;
    }

    /**
     * Set Editable
     *
     * @param bool $editable
     *
     * @return $this
     */
    public function setEditable($editable)
    {
        $this->editable = (bool)$editable;
        return $this;
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function setModule(string $module): void
    {
        $this->module = $module;
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->available;
    }

    /**
     * @param bool $available
     */
    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $description
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setDescription($description)
    {
        return $this->setTranslationField(__FUNCTION__, $description);
    }

    /**
     * @return string
     */
    public function getCustomerSubject()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $customerSubject
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setCustomerSubject($customerSubject)
    {
        return $this->setTranslationField(__FUNCTION__, $customerSubject);
    }

    /**
     * @return string
     */
    public function getCustomerText()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $customerText
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setCustomerText($customerText)
    {
        return $this->setTranslationField(__FUNCTION__, $customerText);
    }

    /**
     * @return string
     */
    public function getAdminSubject()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $adminSubject
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setAdminSubject($adminSubject)
    {
        return $this->setTranslationField(__FUNCTION__, $adminSubject);
    }

    /**
     * @return string
     */
    public function getAdminText()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $adminText
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setAdminText($adminText)
    {
        return $this->setTranslationField(__FUNCTION__, $adminText);
    }

    // }}}
}
