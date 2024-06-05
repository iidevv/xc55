<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\Model\Repo\Product;

class CustomGlobalTab extends \XLite\Model\Repo\Base\I18n
{
    public function loadFixture(array $record, \XLite\Model\AEntity $parent = null, array $parentAssoc = [])
    {
        $entity = parent::loadFixture($record, $parent, $parentAssoc);

        if (
            $entity->getGlobalTab()
            && !$entity->getGlobalTab()->getLink()
        ) {
            $entity->getGlobalTab()->setLink(
                \XLite\Core\Database::getRepo('\XLite\Model\Product\GlobalTab')->generateTabLink($entity)
            );
        }

        return $entity;
    }
}
