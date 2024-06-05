<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View\FormField\Select\Select2;

use Qualiteam\SkinActAftership\Model\AftershipCouriers;
use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Model\Order;
use XLite\View\FormField\Select\Select2Trait;

/**
 * Select current shipping method couriers
 */
class ShippingMethodCouriers extends \XLite\View\FormField\Select\ASelect
{
    use AftershipTrait, Select2Trait {
        Select2Trait::getCommentedData as getSelect2CommentedData;
        Select2Trait::getValueContainerClass as getSelect2ContainerClass;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles(): array
    {
        $list                         = parent::getCommonFiles();
        $list[static::RESOURCE_JS][]  = 'select2/dist/js/select2.min.js';
        $list[static::RESOURCE_CSS][] = 'select2/dist/css/select2.min.css';

        return $list;
    }

    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = $this->getModulePath() . '/form_field/input/select/shipping_method_couriers.js';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles(): array
    {
        $list   = parent::getCSSFiles();
        $list[] = 'form_field/input/text/autocomplete.css';
        $list[] = $this->getModulePath() . '/form_field/input/select/style.less';

        return $list;
    }

    /**
     * Default options
     */
    protected function getDefaultOptions(): array
    {
        $list = [];

        $list += $this->getShippingMethodCouriersList();

        return ['None' => static::t('SkinActAftership none')] + $list;
    }

    /**
     * Get order shipping methods
     */
    protected function getOrderShippingMethods(): ?array
    {
        $orderNumber = Request::getInstance()->order_number;

        return $orderNumber
            ? Database::getRepo(Order::class)->findOrderShippingMethodCouriers($orderNumber)
            : [];
    }

    /**
     * Get shipping method couriers in DB
     *
     * @return array|object[]
     */
    protected function getShippingMethodCouriers(): array
    {
        return Database::getRepo(AftershipCouriers::class)
            ->findAll();
    }

    /**
     * Get shipping method couriers list
     *
     * @return array
     */
    protected function getShippingMethodCouriersList(): array
    {
        $values = $this->getShippingMethodCouriers();
        $result = [];

        /** @var AftershipCouriers $value */
        foreach ($values as $value) {
            $result[$value->getName()] = $value->getName();
        }

        return $result;
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass(): string
    {
        return parent::getValueContainerClass() . ' shippingmethodcouriers';
    }

    /**
     * This data will be accessible using JS core.getCommentedData() method.
     *
     * @return array
     */
    protected function getCommentedData(): array
    {
        $data = $this->getSelect2CommentedData();
        $data['ajaxUrl'] = $this->buildURL('select_shipstation_method_couriers');
        $data['short-lbl'] = static::t('SkinActAftership please enter 3 or more characters');
        return $data;
    }
}