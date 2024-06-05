<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\FormField\Select\Model;


class ProfileSelector extends \XLite\View\FormField\Select\Model\ProfileSelector
{
    protected function getTextValue()
    {
        return $this->getValue();
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $key = array_search('form_field/model_selector/profile/controller.js', $list, true);

        if ($key !== false) {
            $list[$key] = 'modules/Qualiteam/SkinActCreateOrder/ProfileSelector.js';
        }

        return $list;
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_IS_MODEL_REQUIRED] = new \XLite\Model\WidgetParam\TypeBool(
            'Flag if the model required to be selected',
            false,
            false
        );
    }

    protected function defineCSSClasses()
    {
        $classes = parent::defineCSSClasses();

        //$classes[] = 'not-affect-recalculate';
        //$classes[] = 'validate[maxSize[255],custom[email]]';

        return $classes;
    }


}