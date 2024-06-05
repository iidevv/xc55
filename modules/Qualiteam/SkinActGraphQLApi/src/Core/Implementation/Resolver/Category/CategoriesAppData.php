<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Category;


class CategoriesAppData extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Category\Categories
{

    protected function prepareSearchCaseBySearchParams($args)
    {
        $cnd = parent::prepareSearchCaseBySearchParams($args);
        $cnd->showInMobileApp = true;
        return $cnd;
    }

}
