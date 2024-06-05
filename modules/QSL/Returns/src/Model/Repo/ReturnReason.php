<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Model\Repo;

/**
 * Return reason repository
 *
 */
class ReturnReason extends \XLite\Model\Repo\ARepo
{
    public const SEARCH_ORDER_BY = 'orderBy';
    public const SEARCH_LIMIT   = 'limit';

    /**
     * Current condition
     *
     * @var \XLite\Core\CommonCell
     */
    protected $currentSearchCnd = null;

    // {{{ Search

    // }}}
}
