<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/** @noinspection PhpMissingParamTypeInspection */
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection ReturnTypeCanBeDeclaredInspection */

namespace CDev\GoogleAnalytics\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use CDev\GoogleAnalytics\Logic\Action;
use CDev\GoogleAnalytics\Logic\Action\Base\AAction;

/**
 * Something customer can put into his cart (sic!)
 *
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\OrderItem
{
    /**
     * Category added name
     *
     * @var string
     *
     * @ORM\Column (type="string", nullable=true)
     */
    protected $categoryAdded = '';

    /**
     * @return string
     */
    public function getCategoryAdded()
    {
        return $this->categoryAdded;
    }

    /**
     * @param string $categoryAdded
     */
    public function setCategoryAdded($categoryAdded)
    {
        $this->categoryAdded = $categoryAdded;
    }

    /**
     * Get event cell base information
     *
     * @return array
     */
    public function getEventCell()
    {
        $result = parent::getEventCell();

        $action = new Action\DataDriven\OrderItemEventCell($this);

        if ($actionData = $action->getActionData(AAction::RETURN_PART_DATA)) {
            $result['ga-data'] = $actionData;
        }

        return $result;
    }
}
