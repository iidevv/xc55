<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\FormField\Select;

/**
 * Google Shopping category selector
 */
class ShopzillaCategory extends \QSL\ProductFeeds\View\FormField\Select\Select2\AFeedCategory
{
    /**
     * Get repository class for the Shopzilla category model.
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('\QSL\ProductFeeds\Model\ShopzillaCategory');
    }
}
