<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Order\Status;

use Doctrine\ORM\Mapping as ORM;

/**
 * Abstract order status
 *
 * @ORM\MappedSuperclass
 */
abstract class AStatus extends \XLite\Model\Base\I18n
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", options={"unsigned": true})
     */
    protected $id;

    /**
     * Status code
     *
     * @var string
     *
     * @ORM\Column (type="string", options={"fixed": true}, length=4, unique=true, nullable=true)
     */
    protected $code;

    /**
     * @var int
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * Status is allowed to set manually
     *
     * @return boolean
     */
    abstract public function isAllowedToSetManually();

    /**
     * List of change status handlers;
     * top index - old status, second index - new one
     * (<old_status> ----> <new_status>: $statusHandlers[$old][$new])
     *
     * @return array
     */
    public static function getStatusHandlers()
    {
        return [];
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $customerName
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setCustomerName($customerName)
    {
        return $this->setTranslationField(__FUNCTION__, $customerName);
    }

    // }}}
}
