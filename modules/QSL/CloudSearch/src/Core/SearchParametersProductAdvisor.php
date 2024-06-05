<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Core;

use QSL\CloudSearch\Model\Repo\Product;
use XCart\Extender\Mapping\Extender;

/**
 * Produces CloudSearch search parameters from CommonCell conditions
 *
 * @Extender\Mixin
 * @Extender\Depend ({"CDev\ProductAdvisor"})
 */
class SearchParametersProductAdvisor extends \QSL\CloudSearch\Core\SearchParameters
{
    /**
     * Get search parameters
     *
     * @return array
     */
    public function getParameters()
    {
        $data = parent::getParameters();

        if ($this->cnd->{Product::P_ARRIVAL_DATE}
            && $this->cnd->{Product::P_ARRIVAL_DATE}[1] !== null
        ) {
            $data['conditions']['type'] = ['new'];
        }

        if ($this->cnd->{Product::P_ARRIVAL_DATE}
            && $this->cnd->{Product::P_ARRIVAL_DATE}[1] === null
        ) {
            $data['conditions']['type'] = ['upcoming'];
        }

        return $data;
    }
}
