<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Module\XC\ProductVariants\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
class ProductVariant extends \XC\ProductVariants\Model\ProductVariant
{
    /**
     * Perform some actions before inventory saved
     *
     * @param \Doctrine\ORM\Event\PreUpdateEventArgs $event Event
     *
     * @ORM\PreUpdate
     */
    public function proccessCheckRecordChange(\Doctrine\ORM\Event\PreUpdateEventArgs $event)
    {
        if ($this->isAmountChanged($event)) {
            \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record')
                ->updateAsChanged(
                    $this->getProduct()
                );
        }

        if ($this->isPriceChanged($event)) {
            \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')
                ->updateAsChanged(
                    $this->getProduct()
                );
        }
    }

    /**
     * Check - back in stock subscription is allowed or not
     *
     * @return boolean
     */
    public function isBackInStockAllowed()
    {
        $config = \XLite\Core\Config::getInstance()->QSL->BackInStock;

        return $config->allowStockNotification
            && ($config->allowSpecifyQuantity || $this->isOutOfStock());
    }

    /**
     * Check - price drop subscription is allowed or not
     *
     * @return boolean
     */
    public function isPriceDropAllowed()
    {
        return \XLite\Core\Config::getInstance()->QSL->BackInStock->allowPriceNotification;
    }

    /**
     * Check - amount is increased or not
     *
     * @param \Doctrine\ORM\Event\PreUpdateEventArgs $event Event
     *
     * @return boolean
     */
    protected function isAmountChanged(\Doctrine\ORM\Event\PreUpdateEventArgs $event)
    {
        return $event->hasChangedField('amount');
    }

    /**
     * Check - price is changed or not
     *
     * @param \Doctrine\ORM\Event\PreUpdateEventArgs $event Event
     *
     * @return boolean
     */
    protected function isPriceChanged(\Doctrine\ORM\Event\PreUpdateEventArgs $event)
    {
        return $event->hasChangedField('price') && $this->getPublicAmount() > 0;
    }
}
