<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View\Model;

use XCart\Extender\Mapping\Extender;
use XLite\View\FormField\AFormField;
use XLite\View\FormField\Input\ColorPicker;
use Qualiteam\SkinActSkin\View\FormField\Select\TitleColor;
use XLite\View\FormField\Input\Checkbox\OnOff;

/**
 * Category view model
 *
 * @Extender\Mixin
 */
class Category extends \XLite\View\Model\Category
{

    public function __construct(array $params = array(), array $sections = array())
    {
        parent::__construct($params, $sections);

        foreach ($this->schemaDefault as $name => $value) {

            $schema[$name] = $value;

            if ('show_title' === $name) {

                if ($this->getCategory()->getDepth() === 0) {
                    $schema['color'] = [
                        self::SCHEMA_CLASS      => TitleColor::class,
                        self::SCHEMA_LABEL      => '[SkinActSkin] Color',
                        self::SCHEMA_REQUIRED   => false,
                        self::SCHEMA_HELP       => '[SkinActSkin] Color title in the main menu',
                    ];
                }
                $schema['bgColor'] = [
                    self::SCHEMA_CLASS              => ColorPicker::class,
                    self::SCHEMA_LABEL              => '[SkinActSkin] Background color',
                    self::SCHEMA_REQUIRED           => false,
                    AFormField::PARAM_FIELD_ONLY    => false
                ];

                $schema['showOnHomePage'] = [
                    self::SCHEMA_CLASS      => OnOff::class,
                    self::SCHEMA_LABEL      => '[SkinActSkin] Show on home page',
                    self::SCHEMA_REQUIRED   => false,
                ];

            }

            if ('image' === $name) {
                $schema['image2'] = [
                    self::SCHEMA_CLASS => 'XLite\View\FormField\FileUploader\Image',
                    self::SCHEMA_LABEL => '[SkinActSkin] Additional icon',
                    self::SCHEMA_REQUIRED => false,
                ];
            }

            $this->schemaDefault = $schema;
        }
    }

}
