<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\FormField\Select;

/**
 * Google Shopping category selector
 */
class NextagCategory extends \QSL\ProductFeeds\View\FormField\Select\Select2\AFeedCategory
{
    /**
     * Get repository class for the Nextag category model.
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('\QSL\ProductFeeds\Model\NextagCategory');
    }

    /**
     * Shorten the category name.
     *
     * @param string $name Original category name.
     *
     * @return string
     */
    protected function shortenCategoryName($name)
    {
        return strtr(
            parent::shortenCategoryName($name),
            [
                'More Categories' => 'More',
                '\' Sweaters & Sweatshirts' => '',
                '\'s Sweaters & Sweatshirts' => '',
                '\' Undergarments' => '',
                '\'s Undergarments' => '',
                '\' Blazers & Suits' => '',
                '\'s Blazers & Suits' => '',
                '\' Socks & Hosiery' => '',
                '\'s Socks & Hosiery' => '',
                'Girls\' Dresses & Skirts' => 'Girls',
                'Women\'s Dresses & Skirts' => 'Women',
                'Boys\' Clothing' => 'Boys',
                'Girls\' Clothing' => 'Girls',
                'Handbag & Wallet Accessories' => 'Accessories',
                'Major Appliance Accessories' => 'Accessories',
                'Small Appliance Accessories' => 'Accessories',
                'Climate Control Appliances' => 'Climate Control',
                'Air Conditioner Accessories' => 'Air Conditioner',
                'Vacuum Accessories' => 'Vacuum',
                'Cooktop Accessories' => 'Cooktop',
                'Dishwasher Accessories' => 'Dishwasher',
                'Microwave Oven Accessories' => 'Microwave Oven',
                'Oven Accessories' => 'Oven',
                'Range Accessories' => 'Range',
                'Air Purifier Accessories' => 'Air Purifier',
                'Dehumidifier Accessories' => 'Dehumidifier',
                'Humidifier Accessories' => 'Humidifier',
                'Laundry Appliance Accessories' => 'Laundry Appliance',
                'Patio Heater Accessories' => 'Patio Heater',
                'Sewing Machine Accessories' => 'Sewing Machine',
                'Water Heater Accessories' => 'Water Heater',
                'Blender Accessories' => 'Blender',
                'Coffee Maker & Espresso Machine Accessories' => 'Coffee Maker & Espresso Machine',
                'Deep Fryer Accessories' => 'Deep Fryer',
                'Fondue Set Accessories' => 'Fondue Set',
                'Food Dehydrator Accessories' => 'Food Dehydrator',
                'Food Grinder Accessories' => 'Food Grinder',
                'Food Mixer Accessories' => 'Food Mixer',
                'Ice Cream Maker Accessories' => 'Ice Cream Maker',
                'Outdoor Grill Accessories' => 'Outdoor Grill',
                'Washing Machine Accessories' => 'Washing Machine',
                'Food Mixer Attachments' => 'Attachments',
            ]
        );
    }
}
