<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleFeedAdvanced\Model;

use Doctrine\ORM\Mapping as ORM;
use Qualiteam\SkinActGoogleFeedAdvanced\Main;
use Symfony\Component\VarDumper\VarDumper;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Model\Attribute;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * @var boolean
     *
     * @ORM\Column (type="boolean", nullable=true, options={"default" : 1})
     */
    protected $add_to_google_feed;

    /**
     * @return bool|null
     */
    public function getAddToGoogleFeed(): ?bool
    {
        return $this->add_to_google_feed;
    }

    /**
     * @param bool $value
     */
    public function setAddToGoogleFeed($value)
    {
        $this->add_to_google_feed = $value;
        return $this;
    }

    protected function prepareGetGoogleFeedAttribute(string $attributeName): ?string
    {
        $result = [];

        $attribute = Main::getAttributeByName($attributeName);

        if ($attribute) {
            $result = $attribute->getAttributeValue($this, true);
        }

        return $result[0] ?? null;
    }

    protected function prepareSetGoogleFeedAttribute(string $attributeName, $value = null): void
    {
        if ($value !== $this->prepareGetGoogleFeedAttribute($attributeName)) {
            $attribute = Main::getAttributeByName($attributeName);

            if ($attribute) {
                if ($attribute->getType() === 'S') {
                    $data['value'] = [$value];
                } else {
                    $data['value'] = $value;
                }

                $attribute->setAttributeValue($this, $data);
            }
        }
    }

    public function getGoogleshopConditionField()
    {
        return $this->prepareGetGoogleFeedAttribute('googleshop_condition_field');
    }

    public function getGoogleshopGtinField()
    {
        return $this->prepareGetGoogleFeedAttribute('googleshop_gtin_field');
    }

    public function getGoogleshopBrandField()
    {
        return $this->prepareGetGoogleFeedAttribute('googleshop_brand_field');
    }

    public function getGoogleshopGooglecategoryField()
    {
        return $this->prepareGetGoogleFeedAttribute('googleshop_googlecategory_field');
    }

    public function getGoogleshopProducttypeField()
    {
        return $this->prepareGetGoogleFeedAttribute('googleshop_producttype_field');
    }

    public function setGoogleshopConditionField($value)
    {
        return $this->prepareSetGoogleFeedAttribute('googleshop_condition_field', $value);
    }

    public function setGoogleshopGtinField($value)
    {
        return $this->prepareSetGoogleFeedAttribute('googleshop_gtin_field', $value);
    }

    public function setGoogleshopBrandField($value)
    {
        return $this->prepareSetGoogleFeedAttribute('googleshop_brand_field', $value);
    }

    public function setGoogleshopGooglecategoryField($value)
    {
        return $this->prepareSetGoogleFeedAttribute('googleshop_googlecategory_field', $value);
    }

    public function setGoogleshopProducttypeField($value)
    {
        return $this->prepareSetGoogleFeedAttribute('googleshop_producttype_field', $value);
    }
}
