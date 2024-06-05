<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Couriers\Command;

use Qualiteam\SkinActAftership\Model\AftershipCouriers;
use XLite\Core\Database;

/**
 * Class update courier item
 */
class Update extends ACommand
{
    /**
     * @throws \Exception
     */
    public function couriers(array $items): void
    {
        foreach ($items as $item) {

            /** @var AftershipCouriers $dbCourier */
            $dbCourier = Database::getRepo(AftershipCouriers::class)
                ->findOneBy(['slug' => $item['slug']]);

            if ($dbCourier) {
                $this->update($dbCourier, $item);
            } else {
                (new Create())->courier($item);
            }
        }

        $this->do();
    }

    /**
     * Update courier info
     *
     * @param AftershipCouriers $dbCourier
     * @param array             $courier
     *
     * @return void
     */
    public function update(AftershipCouriers $dbCourier, array $courier): void
    {
        if ($dbCourier->getName() !== $courier['name'] . ' [' . $courier['slug'] . ']') {
            $dbCourier->setName($courier['name'] . ' [' . $courier['slug'] . ']');

            Database::getEM()->persist($dbCourier);
        }
    }
}
