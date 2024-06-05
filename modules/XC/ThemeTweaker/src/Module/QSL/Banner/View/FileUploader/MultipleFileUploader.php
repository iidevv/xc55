<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Module\QSL\Banner\View\FileUploader;

use XCart\Extender\Mapping\Extender;

/**
 * Multiple Image file uploader
 *
 * @Extender\Depend ("QSL\Banner")
 */
class MultipleFileUploader extends \XLite\View\FormField\FileUploader\AFileUploader
{
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/XC/ThemeTweaker/themetweaker/layout_editor/panel_parts/banners/file_uploader/multiple.js',
            'modules/XC/ThemeTweaker/themetweaker/layout_editor/panel_parts/banners/file_uploader/popper.min.js',
            'modules/XC/ThemeTweaker/themetweaker/layout_editor/panel_parts/banners/file_uploader/tippy-bundle.umd.min.js',
        ]);
    }

    /**
     * Return 'isImage' flag
     *
     * @return boolean
     */
    protected function isImage()
    {
        return true;
    }

    protected function getDir()
    {
        return 'modules/XC/ThemeTweaker/themetweaker/layout_editor/panel_parts/banners';
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();

        $list['data-is_image'] = true;

        return $list;
    }
}
