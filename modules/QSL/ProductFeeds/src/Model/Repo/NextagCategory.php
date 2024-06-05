<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Model\Repo;

/**
 * Repository class for the NextagCategory model.
 */
class NextagCategory extends \XLite\Model\Repo\ARepo
{
    /**
     * Get the entire list of available NexTag categories.
     *
     * @return mixed
     */
    public function getAll()
    {
        return $this->findBy([], ['name' => 'ASC']);
    }
}
