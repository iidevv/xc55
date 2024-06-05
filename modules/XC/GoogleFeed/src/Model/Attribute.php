<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Extender\Mixin
 */
class Attribute extends \XLite\Model\Attribute
{
    /**
     * Shopping group key
     *
     * @var string
     *
     * @ORM\Column (type="string", nullable=true)
     */
    protected $googleShoppingGroup;

    /**
     * @return array
     */
    public static function getGoogleShoppingGroups()
    {
        $defaultOptions = [
            'brand', 'color', 'pattern', 'material', 'size', 'size_type', 'size_system', 'age_group', 'gender', 'google_product_category'
        ];

        $config = \Includes\Utils\ConfigParser::getOptions(['modules', 'XC-GoogleFeed']);
        $configOptions = $config['additional_options'] ?: [];

        return array_merge($defaultOptions, $configOptions);
    }

    /**
     * @return string
     */
    public function getGoogleShoppingGroup()
    {
        return $this->googleShoppingGroup;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setGoogleShoppingGroup($key)
    {
        if ($key === '' || in_array($key, static::getGoogleShoppingGroups(), true)) {
            $this->googleShoppingGroup = $key;
        }

        return $this;
    }
}
