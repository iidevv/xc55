<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Core\Task;

use Qualiteam\SkinActShipStationAdvanced\Helpers\ShipstationApiHelper;
use XC\ProductVariants\Model\ProductVariant;
use GuzzleHttp\Exception\GuzzleException;
use XCart\Container;
use XLite\Core\Database;

class CheckProductVariantToSyncShipStation extends \XLite\Core\Task\Base\Periodic
{
    protected ShipstationApiHelper|null $shipstationApiHelper;

    public function __construct()
    {
        parent::__construct();

        $this->shipstationApiHelper = Container::getContainer()->get('shipstation.helper');
    }

    public function getTitle()
    {
        return 'Check a product variant to sync shipstation';
    }

    /**
     * @throws GuzzleException
     */
    protected function runStep(): void
    {
        error_reporting(E_ALL & ~E_WARNING);

        $repo  = Database::getRepo(ProductVariant::class);
        $items = $repo->findBy(['prepareToSyncShipStation' => true]);

        if ($items) {

            /** @var ProductVariant $item */
            foreach ($items as $item) {
                $this->shipstationApiHelper::defineRunStep($item);
            }
        }
    }

    protected function getPeriod()
    {
        return static::INT_10_MIN;
    }
}
