<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core\View;

use Twig\Markup;
use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker\Core\Translation;

/**
 * WidgetParamsSerializer provides serialization support for widget params.
 * Widget param values are serialized when dynamic widget placeholder is generated and unserialized when placeholder is reified into a rendered widget.
 *
 * @Extender\Mixin
 */
class WidgetParamsSerializer extends \XLite\Core\View\WidgetParamsSerializer
{
    /**
     * Serialize widget params into a string
     *
     * @param array $widgetParams
     *
     * @return string
     * @throws \XLite\Core\View\WidgetParamsSerializationException
     */
    public function serialize(array $widgetParams)
    {
        if (Translation::getInstance()->isInlineEditingEnabled()) {
            foreach ($widgetParams as $key => $param) {
                if ($param->value instanceof Markup) {
                    $param->setValue((string) $param->value);
                }
            }
        }

        return parent::serialize($widgetParams);
    }
}
