<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeatureWidget\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class EducationalVideo extends \Qualiteam\SkinActVideoFeature\Model\Repo\EducationalVideo
{
    protected function prepareCndCategoryId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value && $value !== 'show_all_videos') {
            parent::prepareCndCategoryId($queryBuilder, $value);
        }
    }
}