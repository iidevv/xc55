<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Model\Repo;

/**
 * Repository class for the PricegrabberCategory model.
 */
class PricegrabberCategory extends \XLite\Model\Repo\ARepo
{
    /**
     * Get the entire list of available Pricegrabber categories.
     *
     * @return mixed
     */
    public function getAll()
    {
        return $this->findBy([], ['pricegrabber_id' => 'ASC']);
    }
}
