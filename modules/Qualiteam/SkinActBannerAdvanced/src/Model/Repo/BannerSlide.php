<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActBannerAdvanced\Model\Repo;

use Qualiteam\SkinActBannerAdvanced\Model\Repo\OrderBy\PositionOrderByFactory;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class BannerSlide extends \QSL\Banner\Model\Repo\BannerSlide
{
    public const SEARCH_PARAM_MOBILE_POSITION = 'mobile_position';
    public const SEARCH_PARAM_BANNER = 'banner';

    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        [$sort, $order] = $this->getSortOrderValue($value);
        $sortContract = (new PositionOrderByFactory())->getSortByContract($sort);

        if ($sortContract) {
            $queryBuilder->resetDQLPart('orderBy');
            $sortContract->addOrderBy($queryBuilder, $order);
        } else {
            parent::prepareCndOrderBy($queryBuilder, $value);
        }
    }
}