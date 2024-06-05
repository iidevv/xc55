<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Couriers\Command;

use Qualiteam\SkinActAftership\Model\AftershipCouriers;
use XLite\Core\Database;

/**
 * Class create courier item
 */
class Create extends ACommand
{
    /**
     * @param array $item
     *
     * @return void
     * @throws \Exception
     */
    public function courier(array $item): void
    {
        $this->create($item);
    }

    /**
     * Create and persist a new model
     *
     * @param array $item
     *
     * @return void
     */
    protected function create(array $item): void
    {
        $courier = new AftershipCouriers();
        $courier->setName($item['name'] . ' [' . $item['slug'] . ']');
        $courier->setSlug($item['slug']);

        Database::getEM()->persist($courier);
    }

    /**
     * Create all couriers
     *
     * @param array $items
     *
     * @return void
     * @throws \Exception
     */
    public function couriers(array $items): void
    {
        foreach ($items as $item) {
            $this->create($item);
        }

        $this->do();
    }
}
