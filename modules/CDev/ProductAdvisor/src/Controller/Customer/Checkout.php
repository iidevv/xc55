<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    /**
     * Order placement is success
     *
     * @param boolean $doCloneProfile
     */
    public function processSucceed($doCloneProfile = true)
    {
        parent::processSucceed($doCloneProfile);

        $this->saveProductStats(\CDev\ProductAdvisor\Main::getProductIds());
    }

    /**
     * Order placement is success
     *
     * @param array $viewedProductIds
     */
    protected function saveProductStats($viewedProductIds)
    {
        $viewedProducts = [];

        if ($viewedProductIds) {
            $viewedProducts = (array) \XLite\Core\Database::getRepo('XLite\Model\Product')->search(
                new \XLite\Core\CommonCell([\XLite\Model\Repo\Product::P_PRODUCT_IDS => $viewedProductIds])
            );
        }

        if ($viewedProducts) {
            $orderItems      = $this->getCart()->getItems();
            $orderedProducts = [];

            foreach ($orderItems as $item) {
                if ($item->getProduct() && 0 < $item->getProduct()->getProductId()) {
                    $orderedProducts[$item->getProduct()->getProductId()] = $item->getProduct();
                }
            }

            // Find existing statistics records
            $foundStats = \XLite\Core\Database::getRepo('CDev\ProductAdvisor\Model\ProductStats')
                ->findStats($viewedProductIds, array_keys($orderedProducts));

            // Prepare array of pairs 'A-B', 'C-D',... where A,C - viewed product ID, B,D - ordered product ID
            // This will make comparison easy
            $foundStatsPairs = [];

            if ($foundStats) {
                foreach ($foundStats as $stats) {
                    $foundStatsPairs[] = sprintf(
                        '%d-%d',
                        $stats->getViewedProduct()->getProductId(),
                        $stats->getBoughtProduct()->getProductId()
                    );
                }
            }

            // Update exsisting statistics
            \XLite\Core\Database::getRepo('CDev\ProductAdvisor\Model\ProductStats')
                ->updateStats($foundStats);

            $statsCreated = false;

            foreach ($orderedProducts as $opid => $orderedProduct) {
                foreach ($viewedProducts as $viewedProduct) {
                    if (
                        !in_array(sprintf('%d-%d', $viewedProduct->getProductId(), $opid), $foundStatsPairs, true)
                        && $viewedProduct->getProductId() != $opid
                    ) {
                        // Create statistics record
                        $stats = new \CDev\ProductAdvisor\Model\ProductStats();
                        $stats->setViewedProduct($viewedProduct);
                        $stats->setBoughtProduct($orderedProduct);

                        \XLite\Core\Database::getEM()->persist($stats);

                        $statsCreated = true;
                    }
                }
            }

            if ($statsCreated) {
                \XLite\Core\Database::getEM()->flush();
            }
        }
    }
}
