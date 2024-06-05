<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\View\FormField;

class RawLabel extends \XLite\View\FormField\Label
{
    protected function getDir()
    {
        return 'modules/XC/MigrationWizard/form_field';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'raw_label.twig';
    }
}
