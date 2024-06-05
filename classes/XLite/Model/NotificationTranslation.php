<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification translations
 *
 * @ORM\Entity
 * @ORM\Table (name="notification_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class NotificationTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Notification name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128)
     */
    protected $name = '';

    /**
     * Notification description
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $description = '';

    /**
     * Notification subject for customer
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $customerSubject = '';

    /**
     * Notification text for customer
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $customerText = '';

    /**
     * Notification subject for admin
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $adminSubject = '';

    /**
     * Notification text for admin
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $adminText = '';

    /**
     * @var \XLite\Model\Notification
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Notification", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="templatesDirectory", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set name
     *
     * @param string $name
     * @return NotificationTranslation
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return NotificationTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set customerSubject
     *
     * @param string $customerSubject
     * @return NotificationTranslation
     */
    public function setCustomerSubject($customerSubject)
    {
        $this->customerSubject = $customerSubject;
        return $this;
    }

    /**
     * Get customerSubject
     *
     * @return string
     */
    public function getCustomerSubject()
    {
        return $this->customerSubject;
    }

    /**
     * Set customerText
     *
     * @param string $customerText
     * @return NotificationTranslation
     */
    public function setCustomerText($customerText)
    {
        $this->customerText = $customerText;
        return $this;
    }

    /**
     * Get customerText
     *
     * @return string
     */
    public function getCustomerText()
    {
        return $this->customerText;
    }

    /**
     * Set adminSubject
     *
     * @param string $adminSubject
     * @return NotificationTranslation
     */
    public function setAdminSubject($adminSubject)
    {
        $this->adminSubject = $adminSubject;
        return $this;
    }

    /**
     * Get adminSubject
     *
     * @return string
     */
    public function getAdminSubject()
    {
        return $this->adminSubject;
    }

    /**
     * Set adminText
     *
     * @param string $adminText
     * @return NotificationTranslation
     */
    public function setAdminText($adminText)
    {
        $this->adminText = $adminText;
        return $this;
    }

    /**
     * Get adminText
     *
     * @return string
     */
    public function getAdminText()
    {
        return $this->adminText;
    }

    /**
     * Get label_id
     *
     * @return integer
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return NotificationTranslation
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
