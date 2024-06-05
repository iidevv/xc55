<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API;

class OpenApiTagsRepository implements OpenApiTagsRepositoryInterface
{
    public function getTags(): array
    {
        return [
            'Products' => [
                'Product',
                'Product Image',
                'Tax Class',
            ],
            'Groups' => [
                'Category',
                'Category Banner',
                'Category Icon',
            ],
            'Product Attributes' => [
                'Attribute Group',
                'Attribute',
                'Attribute Option',
                'Attribute Property',
                'Yes/No Attribute Value',
                'Hidden Attribute Value',
                'Plain Field Attribute Value',
                'Textarea Attribute Value',
                'Product Class',
            ],
            'Orders & Carts' => [
                'Order',
                'Cart',
                'Detail',
                'History Event',
                'Transaction',
            ],
            'Profiles' => [
                'Address',
                'Profile',
                'Membership',
            ],
            'Discounts' => [],
        ];
    }
}
