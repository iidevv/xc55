<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class RemoveData extends \XLite\View\ItemsList\Model\RemoveData
{
    public const TYPE_PAGES = 'pages';

    /**
     * Get plain data
     *
     * @return array
     */
    protected function getPlainData()
    {
        return parent::getPlainData() + [
            static::TYPE_PAGES => [
                'name' => static::t('Pages'),
            ],
        ];
    }

    /**
     * Build metod name
     *
     * @param \XLite\Model\AEntity $entity  Entity
     * @param string               $pattern Pattern
     *
     * @return string
     */
    protected function buildMethodName(\XLite\Model\AEntity $entity, $pattern)
    {
        return $entity->getId() == static::TYPE_PAGES
            ? sprintf($pattern, 'Pages')
            : parent::buildMethodName($entity, $pattern);
    }

    /**
     * Check - allow remove coupons or not
     *
     * @return boolean
     */
    protected function isAllowRemovePages()
    {
        return 0 < \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Page')->count();
    }
}
