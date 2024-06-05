<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\FormField;

use Qualiteam\SkinActXPaymentsSubscriptions\Model\Base\ASubscriptionPlan;
use XLite\View\FormField\AFormField;

/**
 * Use Custom Open Graph selector
 */
class Plan extends AFormField
{
    const FIELD_TYPE_XPS_PLAN = 'xps_plan';

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return static::FIELD_TYPE_XPS_PLAN;
    }

    /**
     * Return JS files for this widget
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . 'form_field/plan.js';

        return $list;
    }

    /**
     * Return CSS files for this widget
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . 'form_field/plan.css';

        return $list;
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/Qualiteam/SkinActXPaymentsSubscriptions/';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'form_field/plan.twig';
    }

    /**
     * check if plan type equals to TYPE_EACH
     *
     * @return boolean
     */
    protected function isPlanTypeEach()
    {
        $value = $this->getValue();

        return ASubscriptionPlan::TYPE_EACH == $value['type'];
    }
}
