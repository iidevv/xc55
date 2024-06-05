<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Couriers;

use Qualiteam\SkinActAftership\Couriers\Command\Create;
use Qualiteam\SkinActAftership\Couriers\Command\Update;
use XLite\Core\Database;
use XLite\Model\TmpVar;

/**
 * Class couriers
 */
class Couriers implements ICouriers
{
    /**
     * @var array
     */
    protected array $couriers;

    /**
     * @var string
     */
    protected string $total;

    /**
     * Constructor
     *
     * @param array  $couriers
     * @param string $total
     */
    public function __construct(array $couriers, string $total)
    {
        $this->couriers = $couriers;
        $this->total    = $total;
    }

    /**
     * Create courier item
     *
     * @return void
     * @throws \Exception
     */
    public function create(): void
    {
        $create = new Create();
        $create->couriers($this->couriers);

        $this->updateInfo();
    }

    /**
     * Update help block info after create/update action
     *
     * @return void
     */
    protected function updateInfo(): void
    {
        Database::getRepo(TmpVar::class)
            ->setVar('aftershipCollectCouriers', [
                'couriers_count' => $this->total,
                'last_update'    => time(),
            ]);
    }

    /**
     * Update help block info after create/update action
     *
     * @return void
     * @throws \Exception
     */
    public function update(): void
    {
        $update = new Update();
        $update->couriers($this->couriers);

        $this->updateInfo();
    }
}
