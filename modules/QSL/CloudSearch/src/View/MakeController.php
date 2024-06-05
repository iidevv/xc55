<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\View;

use QSL\CloudSearch\Model\Repo\Product as CloudSearchProduct;
use QSL\Make\Model\Repo\Product as MakeProduct;
use QSL\Make\View\FormField\Select\FilteringMode;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Core\Database;
use XLite\Model\Product;

/**
 * @Extender\Mixin
 * @Extender\Depend ("QSL\Make")
 */
class MakeController extends Controller
{
    /**
     * Get CloudSearch initialization data to pass to the JS code
     */
    protected function getCloudSearchInitData(): array
    {
        $data = parent::getCloudSearchInitData();

        /** @var CloudSearchProduct $repo */
        $repo = Database::getRepo(Product::class);

        $conditions = $repo->getCloudSearchConditions();

        if ($conditions->{MakeProduct::P_LEVEL_PRODUCT}) {
            $data['cloudSearch']['requestData']['conditions'] += [
                'mmy' => ['level_' . $conditions->{MakeProduct::P_LEVEL_PRODUCT}],
            ];

            $mapping = [
                FilteringMode::OPTION_SPECIFIC                           => 1000,
                FilteringMode::OPTION_SPECIFIC_AND_UNIVERSAL             => 100,
                FilteringMode::OPTION_SPECIFIC_AND_UNIVERSAL_AND_REGULAR => 10,
            ];
            $data['cloudSearch']['requestData']['mmy_filtering_mode'] = $mapping[Config::getInstance()->QSL->Make->filtering_mode];
        }

        return $data;
    }
}
