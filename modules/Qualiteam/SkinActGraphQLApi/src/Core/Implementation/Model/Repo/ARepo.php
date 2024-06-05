<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]

 */

class ARepo extends \XLite\Model\Repo\ARepo
{
    /**
     * Search types
     */
    const SEARCH_MODE_PREPARE_QUERY_BUILDER   = 'prepareQueryBuilder';

    /**
     * Get search modes handlers
     *
     * @return array
     */
    protected function getSearchModes()
    {
        return array_merge(
            parent::getSearchModes(),
            array(
                static::SEARCH_MODE_PREPARE_QUERY_BUILDER     => 'searchPrepareQueryBuilder',
            )
        );
    }

    /**
     * Search result routine.
     *
     * @return array
     */
    protected function searchPrepareQueryBuilder()
    {
        return $this->searchState['queryBuilder'];
    }
}
