<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActBrandCoupon\View\Model;

use QSL\ShopByBrand\Model\Brand;
use Qualiteam\SkinActBrandCoupon\View\FormField\Select\Select2\Brands;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\Core\Database;
use XLite\Core\Translation;

/**
 * @Extender\Mixin
 */
class Coupon extends \CDev\Coupons\View\Model\Coupon
{
    /**
     * Save current form reference and sections list, and initialize the cache
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        $this->schemaDefault += [
            'brands' => [
                self::SCHEMA_CLASS    => Brands::class,
                self::SCHEMA_LABEL    => Translation::t('SkinActBrandCoupon brands'),
                self::SCHEMA_REQUIRED => false,
                self::SCHEMA_HELP     => Translation::t('SkinActBrandCoupon if you want the coupon discount to be applied only to products from specific brands'),
            ],
        ];
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data): void
    {
        $entity = $this->getModelObject();

        $brands = $data['brands'] ?? null;

        unset($data['brands']);

        foreach ($entity->getBrands() as $m) {
            $m->getCoupons()->removeElement($entity);
        }

        $entity->clearBrands();

        if (is_array($brands)) {
            foreach ($brands as $id) {
                $m = Database::getRepo(Brand::class)->find($id);
                if ($m) {
                    $entity->addBrands($m);
                    $m->addCoupons($entity);
                }
            }
        }

        parent::setModelProperties($data);
    }
}