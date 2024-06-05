<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActBannerAdvanced\Model\Repo\OrderBy;

abstract class OrderByFactory
{
    abstract protected function getOrderByContracts();

    public function getSortByContract($sort): ?SortByContract
    {
        if ($sort instanceof SortByContract) {
            return $sort;
        }

        foreach ($this->getOrderByContracts() as $contract) {
            if ($sort === (string) ($contract = new $contract)) {
                return $contract;
            }
        }

        return null;
    }
}