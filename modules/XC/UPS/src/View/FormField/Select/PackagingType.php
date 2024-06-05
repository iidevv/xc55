<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UPS\View\FormField\Select;

/**
 * Packaging type selector for settings page
 */
class PackagingType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Packages parameters: weight (lbs), dimensions (inches)
     *
     * @var array
     */
    protected static $upsPackages = [
        '00' => [
            'name' => 'Unknown',
            'limits' => [
                'weight' => 150,
                'length' => 108,
                'width' => 108,
                'height' => 108
            ]
        ],
        '01' => [
            'name' => 'UPS Letter / UPS Express Envelope',
            'limits' => [
                'weight' => 1,
                'length' => 9.5,
                'width' => 12.5,
                'height' => 0.25
            ]
        ],
        '02' => [
            'name' => 'Package'
        ],
        '03' => [
            'name' => 'UPS Tube',
            'limits' => [
                'length' => 6,
                'width' => 38,
                'height' => 6
            ]
        ],
        '04' => [
            'name' => 'UPS Pak',
            'limits' => [
                'length' => 12.75,
                'width' => 16,
                'height' => 2
            ]
        ],
        '21' => [
            'name' => 'UPS Express Box',
            'limits' => [
                'length' => 13,
                'width' => 18,
                'height' => 3,
                'weight' => 30
            ]
        ],
        '24' => [
            'name' => 'UPS 25 Kg Box&#174;',
            'limits' => [
                'length' => 17.375,
                'width' => 19.375,
                'height' => 14,
                'weight' => 55.1
            ]
        ],
        '25' => [
            'name' => 'UPS 10 Kg Box&#174;',
            'limits' => [
                'length' => 13.25,
                'width' => 16.5,
                'height' => 10.75,
                'weight' => 22
            ]
        ],
        '30' => [
            'name' => 'Pallet (for GB or PL domestic shipments only)'
        ],
        '2a' => [
            'name' => 'Small Express Box'
        ],
        '2b' => [
            'name' => 'Medium Express Box'
        ],
        '2c' => [
            'name' => 'Large Express Box'
        ]
    ];

    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = [];
        foreach (static::$upsPackages as $key => $option) {
            $list[$key] = static::t($option['name']);
        }

        return $list;
    }
}
