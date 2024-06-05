<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActBannerAdvanced\View\Model;

use XCart\Extender\Mapping\Extender as Extender;
use XLite\Core\Translation;

/**
 * @Extender\Mixin
 */
class Banner extends \QSL\Banner\View\Model\Banner
{
    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        $this->prepareMobilePosition();
    }

    protected function prepareMobilePosition()
    {
        $i = 0;
        foreach ($this->schemaDefault as $key => $item) {
            if ($key === 'position') {
                $before = array_slice($this->schemaDefault, 0, $i + 1);
                $after = array_slice($this->schemaDefault, $i + 1, -1);

                $before['position'][self::SCHEMA_LABEL] = Translation::lbl('SkinActBannerAdvanced web position');

                $before += [
                    'mobile_position'       => [
                        self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
                        self::SCHEMA_LABEL    => Translation::lbl('SkinActBannerAdvanced mobile position'),
                        self::SCHEMA_REQUIRED => false,
                    ],
                ];

                $this->schemaDefault = $before + $after;
                break;
            }

            $i++;
        }
    }
}