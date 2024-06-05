<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Membership repository
 *
 * @see https://bt.x-cart.com/view.php?id=46811
 * @Extender\Mixin
 */
abstract class Membership extends \XLite\Model\Repo\Membership
{
    /**
     * Insert single entity
     *
     * @param \XLite\Model\AEntity|array $entity Data to insert OPTIONAL
     *
     * @return void
     */
    protected function performInsert($entity = null)
    {
        $model = ARepo::performInsert($entity);

        if (
            $model
            && (
                !\XLite\Core\Database::getRepo('XLite\Model\Product')->getBlockQuickDataFlag()
                && $model->getProducts() !== null
            )
        ) {
            \XLite\Core\QuickData::getInstance()->updateMembershipData($model);
        }

        return $model;
    }
}
