<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Translation;

/**
 * Language labels controller
 * @Extender\Mixin
 */
class Labels extends \XLite\Controller\Admin\Labels
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['searchItemsList', 'edit']);
    }

    /**
     * Is called when doActionEdit() has been performed successfully; sends the appropriate message to the user
     *
     * @param \XLite\Model\LanguageLabel $lbl Edited label entity
     */
    protected function onEditSuccess($lbl)
    {
        $requestData = \XLite\Core\Request::getInstance()->getNonFilteredData();
        $substitutions = $requestData['substitutions'] ?? [];
        $code = $requestData['code'] ?? null;

        \XLite\Core\Event::editedLabel([
            'name' => $lbl->getName(),
            'translation' => (string) Translation::getInstance()->translateAsEditable($lbl->getName(), $substitutions, $code),
        ]);

        parent::onEditSuccess($lbl);
    }
}
