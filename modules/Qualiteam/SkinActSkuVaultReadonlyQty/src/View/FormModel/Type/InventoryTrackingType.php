<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVaultReadonlyQty\View\FormModel\Type;

use Symfony\Component\Form\FormBuilderInterface;
use XCart\Extender\Mapping\Extender;
use XLite;
use XLite\Controller\Admin\Product;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 */
class InventoryTrackingType extends \XLite\View\FormModel\Type\InventoryTrackingType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $isProductPage = XLite::getController() instanceof Product;

        if ($isProductPage) {
            $product = $this->getProduct();
            if ($product && !$product->isSkippedFromSync()) {
                $builder->remove('quantity');

                $builder->add('quantity', 'XLite\View\FormModel\Type\PatternType', [
                    'disabled'          => true,
                    'label'             => static::t('Quantity in stock'),
                    'inputmask_pattern' => [
                        'alias'      => 'integer',
                        'rightAlign' => false,
                    ],
                    'show_when'         => [
                        'prices_and_inventory' => [
                            'inventory_tracking' => [
                                'inventory_tracking' => '1',
                            ],
                        ],
                    ],
                    'form_row_class'    => '',
                ]);
            }
        }

    }

    protected function getProduct()
    {
        $requestData = XLite\Core\Request::getInstance()->getData();
        $productId = $requestData['product_id'];

        return Database::getRepo(\XLite\Model\Product::class)->find($productId);
    }
}
