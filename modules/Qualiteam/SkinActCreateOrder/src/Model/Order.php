<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Request;


/**
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * @var boolean
     *
     * @ORM\Column (type="boolean", options={"default":false} )
     */
    protected $manuallyCreated = false;

    /**
     * @var boolean
     *
     * @ORM\Column (type="boolean", options={"default":false} )
     */
    protected $orderCreatedNotificationSent = false;

    /**
     * @return bool
     */
    public function getOrderCreatedNotificationSent()
    {
        return $this->orderCreatedNotificationSent;
    }

    /**
     * @param bool $orderCreatedNotificationSent
     */
    public function setOrderCreatedNotificationSent($orderCreatedNotificationSent)
    {
        $this->orderCreatedNotificationSent = $orderCreatedNotificationSent;
    }


    /**
     * @return bool
     */
    public function getManuallyCreated()
    {
        return $this->manuallyCreated;
    }

    /**
     * @param bool $manuallyCreated
     */
    public function setManuallyCreated($manuallyCreated)
    {
        $this->manuallyCreated = $manuallyCreated;
    }

    public function recalculate()
    {
        if ($this->getManuallyCreated()
            && \XLite::isAdminZone()
            && in_array(\XLite::getController()->getAction(), ['recalculate', 'update'], true)
            && \XLite::getController()->getTarget() === 'order'
        ) {

            $isNeedToApply = true;
            $auto = Request::getInstance()->auto;

            if (isset($auto['surcharges']) && is_array($auto['surcharges'])) {
                foreach ($auto['surcharges'] as $name => $value) {
                    if (stripos($name, 'AVATAX') === 0 && (int)$value['value'] <= 0) {
                        $isNeedToApply = false;
                        break;
                    }
                }
            }

            if ($isNeedToApply) {

                $oldSurcharges = $this->resetSurcharges();

                foreach ($this->getModifiers() as $modifier) {
                    if ($modifier->getModifier()
                        && $modifier->canApply()
                    ) {
                        $modifier->calculate();
                    }
                }

                $this->mergeSurcharges($oldSurcharges);
            }


        }

        parent::recalculate();
    }

    protected function isPaymentMethodRequired()
    {
        if ($this->getManuallyCreated()) {
            return true;
        }

        return parent::isPaymentMethodRequired();
    }
}
