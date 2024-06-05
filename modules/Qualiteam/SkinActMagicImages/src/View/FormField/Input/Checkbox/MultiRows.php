<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\View\FormField\Input\Checkbox;

use Qualiteam\SkinActMagicImages\Traits\MagicImagesTrait;

/**
 * MultiRows switcher
 */
class MultiRows extends \XLite\View\FormField\Input\Checkbox\OnOff
{
    use MagicImagesTrait;

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getModulePath() . '/form_field/input/checkbox/multi_rows.js';

        return $list;
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);

        $classes[] = 'multi-rows-switcher';

        return $classes;
    }
}
