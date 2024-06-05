<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\Model\Order\Status;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Extender\Mixin
 */
class Shipping extends \XLite\Model\Order\Status\Shipping
{
    /**
     * Select which shipping status should be shown in shipping status bar.
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", nullable=true, options={ "default": "0" })
     */
    protected $showInStatusesBar = false;

    /**
     * @var int
     *
     * @ORM\Column (type="integer")
     */
    protected $newPosition = 0;

    /**
     * Updates the flag that determines if shipping status should be shown in shipping status bar.
     *
     * @param boolean $value New value
     *
     * @return $this
     */
    public function setShowInStatusesBar($value)
    {
        $this->showInStatusesBar = $value;

        return $this;
    }

    /**
     * Check if shipping status should be shown in shipping status bar.
     *
     * @return boolean
     */
    public function getShowInStatusesBar()
    {
        return $this->showInStatusesBar;
    }

    /**
     * Updates the flag that determines if shipping status should be shown in shipping status bar.
     *
     * @param boolean $value New value
     *
     * @return $this
     */
    public function setNewPosition($value)
    {
        $this->newPosition = $value;

        return $this;
    }

    /**
     * Check if shipping status should be shown in shipping status bar.
     *
     * @return boolean
     */
    public function getNewPosition()
    {
        return $this->newPosition;
    }

    public static function getStatusHandlers()
    {
        $handlers = parent::getStatusHandlers();

        foreach ($handlers as $fromStatus => &$toStatuses) {
            if (!isset($toStatuses[self::STATUS_DELIVERED])) {
                $toStatuses[self::STATUS_DELIVERED] = [];
            }

            $toStatuses[self::STATUS_DELIVERED][] = 'skinactDelivered';
        }

        return $handlers;
    }
}
