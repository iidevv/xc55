<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View\ItemsList;

use XLite\Core\Converter;
use CDev\Sale\Model\SaleDiscount;

class AdminLink extends \XLite\View\AView
{
    public const PARAM_ENTITY = 'entity';

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ENTITY => new \XLite\Model\WidgetParam\TypeObject('Entity', null)
        ];
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/SimpleCMS/items_list/model/table/parts/admin_link.twig';
    }

    /**
     * @return \CDev\SimpleCMS\Model\Page
     */
    public function getEntity()
    {
        return $this->getParam(static::PARAM_ENTITY);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $entity = $this->getEntity();

        $isSalePage = ($entity instanceof SaleDiscount);
        $result = empty($entity->getAdminUrl())
            ? Converter::buildURL(($isSalePage ? 'sale_discount' : 'page'), '', ['id' => $entity->getId()])
            : \XLite::ADMIN_SELF . $entity->getAdminUrl();

        return \XLite::getInstance()->getShopURL($result);
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return static::t($this->getEntity()->getName());
    }
}
