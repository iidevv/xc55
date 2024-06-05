<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @ORM\HasLifecycleCallbacks
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Max. price discount for price notificationProduct weight
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=12, scale=2)
     */
    protected $max_price_discount_notify = 0.00;

    /**
     * Back 2 stock counts (cache)
     *
     * @var integer[]
     */
    protected $back2stockCounts;

    /**
     * Price drop counts (cache)
     *
     * @var integer[]
     */
    protected $priceDropCounts;

    /**
     * Count back in stock records (waiting)
     *
     * @param boolean $override Override flag OPTIONAL
     *
     * @return integer
     */
    public function countBack2StockWaitingRecords($override = false)
    {
        $counts = $this->calculateRecordsCounts($override);

        return $counts['waiting'];
    }

    /**
     * Count back in stock records (sent)
     *
     * @param boolean $override Override flag OPTIONAL
     *
     * @return integer
     */
    public function countBack2StockSentRecords($override = false)
    {
        $counts = $this->calculateRecordsCounts($override);

        return $counts['sent'];
    }

    /**
     * Calculate records counts
     *
     * @param boolean $override Override flag OPTIONAL
     *
     * @return integer[]
     */
    public function calculateRecordsCounts($override = false)
    {
        if (!isset($this->back2stockCounts) || $override) {
            $data = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record')
                ->createQueryBuilder('r')
                ->select('SUM(IFELSE(r.state != :sent1, 1, 0)) AS waiting')
                ->addSelect('SUM(IFELSE(r.state = :sent2, 1, 0)) AS sent')
                ->andWhere('r.product = :product')
                ->setParameter('product', $this)
                ->setParameter('sent1', \QSL\BackInStock\Model\Record::STATE_SENT)
                ->setParameter('sent2', \QSL\BackInStock\Model\Record::STATE_SENT)
                ->getArrayResult();
            $this->back2stockCounts = reset($data);
        }

        return $this->back2stockCounts;
    }

    /**
     * Count back in stock records (waiting)
     *
     * @param boolean $override Override flag OPTIONAL
     *
     * @return integer
     */
    public function countPriceDropWaitingRecords($override = false)
    {
        $counts = $this->calculatePriceRecordsCounts($override);

        return $counts['waiting'];
    }

    /**
     * Count back in stock records (sent)
     *
     * @param boolean $override Override flag OPTIONAL
     *
     * @return integer
     */
    public function countPriceDropSentRecords($override = false)
    {
        $counts = $this->calculatePriceRecordsCounts($override);

        return $counts['sent'];
    }

    /**
     * Calculate records counts
     *
     * @param boolean $override Override flag OPTIONAL
     *
     * @return integer[]
     */
    public function calculatePriceRecordsCounts($override = false)
    {
        if (!isset($this->priceDropCounts) || $override) {
            $data = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')
                ->createQueryBuilder('r')
                ->select('SUM(IFELSE(r.state != :sent1, 1, 0)) AS waiting')
                ->addSelect('SUM(IFELSE(r.state = :sent2, 1, 0)) AS sent')
                ->andWhere('r.product = :product')
                ->setParameter('product', $this)
                ->setParameter('sent1', \QSL\BackInStock\Model\ARecord::STATE_SENT)
                ->setParameter('sent2', \QSL\BackInStock\Model\ARecord::STATE_SENT)
                ->getArrayResult();
            $this->priceDropCounts = reset($data);
        }

        return $this->priceDropCounts;
    }

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
            \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record')->updateAsChanged($this);
        }

        if ($this->isPriceChanged($event)) {
            \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')->updateAsChanged($this);
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
            && ($config->allowSpecifyQuantity || $this->isOutOfStock() || !$this->availableInDate());
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
     * @return float
     */
    public function getMaxPriceDiscountNotify()
    {
        return $this->max_price_discount_notify;
    }

    /**
     * @param float $max_price_discount_notify
     *
     * @return static
     */
    public function setMaxPriceDiscountNotify($max_price_discount_notify)
    {
        $this->max_price_discount_notify = $max_price_discount_notify;

        return $this;
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
