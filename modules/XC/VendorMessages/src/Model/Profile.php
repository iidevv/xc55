<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Profile
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Profile
{
    /**
     * Coupons
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="XC\VendorMessages\Model\Conversation", mappedBy="members")
     */
    protected $conversations;

    /**
     * Add coupons
     *
     * @param \XC\VendorMessages\Model\Conversation $conversation
     *
     * @return $this
     */
    public function addConversation($conversation)
    {
        $this->conversations[] = $conversation;
        return $this;
    }

    /**
     * Get coupons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConversations()
    {
        return $this->conversations;
    }

    /**
     * Count unread messages
     *
     * @return integer
     */
    public function countUnreadMessages()
    {
        return \XLite\Core\Database::getRepo('XC\VendorMessages\Model\Message')->countUnread($this);
    }

    /**
     * Count unread messages for own orders
     *
     * @return integer
     */
    public function countOwnUnreadMessages()
    {
        return \XLite\Core\Database::getRepo('XC\VendorMessages\Model\Message')->countOwnUnread($this);
    }

    /**
     * Get vendor name for Order messages module
     *
     * @return string
     */
    public function getNameForMessages()
    {
        if (\XC\VendorMessages\Main::isMultivendor() && $this->getVendor()) {
            return $this->getVendorNameForMessages();
        }

        return $this->isAdmin()
            ? \XLite\Core\Config::getInstance()->Company->company_name
            : $this->getName();
    }

    /**
     * Get vendor name for Order messages module
     *
     * @return string
     */
    public function getVendorNameForMessages()
    {
        return $this->getVendor()->getCompanyName() ?: $this->getName();
    }
}
