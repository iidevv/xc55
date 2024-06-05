<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FileAttachments\View\Button\Dropdown;

class ReUploadFileSelector extends \XLite\View\Button\Dropdown\DropdownFileSelector
{
    /**
     * Return CSS files list
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/FileAttachments/button/reupload-file-selector.css';

        return $list;
    }

    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $buttons = parent::defineAdditionalButtons() + [
                'title' => [
                    'class'    => '\XLite\View\FormField\Label\TranslationLabel',
                    'params'   => [
                        'label' => 'Re-upload file',
                    ],
                    'position' => 50,
                ],
            ];

        return $buttons;
    }

    /**
     * Get style
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' reupload-file-selector';
    }
}
