<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\View\Form;

use Qualiteam\SkinActMagicImages\Traits\MagicImagesTrait;

/**
 * Settings dialog form
 */
class Settings extends \XLite\View\Form\AForm
{
    use MagicImagesTrait;

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = $this->getModulePath() . '/js/admin.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getModulePath() . '/css/style.css';

        return $list;
    }

    /**
     * Get default target
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'magic360_settings';
    }

    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * Ability to add the 'enctype="multipart/form-data"' form attribute
     *
     * @return boolean
     */
    protected function isMultipart()
    {
        return true;
    }

    /**
     * Get form parameters. Add hidden field for page
     *
     * @return array
     */
    protected function getFormParams()
    {
        $params = parent::getFormParams();
        $params += [
            'page'                         => \XLite\Core\Request::getInstance()->page,
            'form_changed_additional_flag' => 'false',
        ];

        return $params;
    }
}
