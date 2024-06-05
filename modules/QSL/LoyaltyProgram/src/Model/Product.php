<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Product
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Number of points that customers are rewarded after purchasing this product.
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $rewardPoints = 0;

    /**
     * Whether to calculate reward points from the price automatically, or use the specified number.
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", nullable=true)
     */
    protected $fixedRewardPoints = false;

    // We had to name it fixedRewardPoints=false instead of autoRewardPoints=true because
    // the module upload function ignores the default value and always drops the value to false.
    // But we need the autoRewardPoints setting enabled for all existing products.

    /**
     * Get the number of points that customers are rewarded after purchasing this product.
     *
     * @return int|null
     */
    public function getRewardPoints()
    {
        return $this->rewardPoints;
    }

    /**
     * Set the number of points that customers are rewarded after purchasing this product.
     *
     * @param integer $points Number of points.
     *
     * @return integer
     */
    public function setRewardPoints($points)
    {
        $this->rewardPoints = $points;

        return $points;
    }

    /**
     * Check if the product has reward points specified by the store administrator.
     *
     * @return boolean
     */
    public function hasDefinedRewardPoints()
    {
        return !$this->getAutoRewardPoints();
    }

    /**
     * Check whether to calculate reward points from the price automatically, or not.
     *
     * @return boolean
     */
    public function getAutoRewardPoints()
    {
        return !$this->fixedRewardPoints;
    }

    /**
     * Set whether to calculate reward points from the price automatically, or not.
     *
     * @param boolean $flag The flag
     */
    public function setAutoRewardPoints($flag)
    {
        $this->fixedRewardPoints = !$flag;
    }
}
