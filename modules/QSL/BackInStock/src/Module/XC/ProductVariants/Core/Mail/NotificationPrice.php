<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Module\XC\ProductVariants\Core\Mail;

use QSL\BackInStock\Model\RecordPrice;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
class NotificationPrice extends \QSL\BackInStock\Core\Mail\NotificationPrice
{
    /**
     * Constructor
     *
     * @param RecordPrice $recordPrice OPTIONAL
     */
    public function __construct(RecordPrice $recordPrice = null)
    {
        parent::__construct($recordPrice);

        if ($recordPrice) {
            if ($variant = $recordPrice->getVariant()) {
                $attrs = [];
                foreach ($variant->getValues() as $attributeValue) {
                    if ($name = $attributeValue->getAttribute()->getName()) {
                        $attrs[] = $name . ': ' . $attributeValue->asString();
                    }
                }

                $this->populateVariables([
                    'product_name'          => $recordPrice->getProduct()->getName() . ' - ' . implode(', ', $attrs),
                    'product_dropped_price' => $variant->getPrice()
                ]);
            }
        }
    }

    /**
     * @return array
     */
    protected function getHashData()
    {
        $record = $this->getData()['record'];

        return $record
            ? array_merge(parent::getHashData(), [$record->getId()])
            : parent::getHashData();
    }
}
