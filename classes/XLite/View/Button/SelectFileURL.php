<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * File selector popup button
 */
class SelectFileURL extends \XLite\View\Button\APopupButton
{
    /**
     * Name of object to link uploaded file (e.g. equal to 'product', 'category')
     */
    public const PARAM_OBJECT = 'object';

    /**
     * Identificator of linked object.
     */
    public const PARAM_OBJECT_ID = 'objectId';

    /**
     * Name of the uploaded file object (e.g. 'image', 'icon', 'file')
     */
    public const PARAM_FILE_OBJECT = 'fileObject';

    /**
     * Identificator of the uploaded file object. Used if file must be substituted (update action)
     */
    public const PARAM_FILE_OBJECT_ID = 'fileObjectId';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/select_file_url.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'file_selector/style.less';

        return $list;
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return [
            'target'       => 'select_file',
            'select_mode'  => 'url',
            'object'       => $this->getParam(static::PARAM_OBJECT),
            'objectId'     => $this->getParam(static::PARAM_OBJECT_ID),
            'fileObject'   => $this->getParam(static::PARAM_FILE_OBJECT),
            'fileObjectId' => $this->getParam(static::PARAM_FILE_OBJECT_ID),
            'widget'       => '\XLite\View\FileSelectorDialog',
        ];
    }

    /**
     * Return default button label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'File upload';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_OBJECT         => new \XLite\Model\WidgetParam\TypeString('Object', 'product'),
            static::PARAM_OBJECT_ID      => new \XLite\Model\WidgetParam\TypeInt('Object ID', 0),
            static::PARAM_FILE_OBJECT    => new \XLite\Model\WidgetParam\TypeString('File object', 'image'),
            static::PARAM_FILE_OBJECT_ID => new \XLite\Model\WidgetParam\TypeInt('File object ID', 0),
        ];
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' select-file-url-button always-reload';
    }
}
