<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\ThemeTweaker;

use Includes\Utils\FileManager;
use XC\ThemeTweaker\Core\TemplateObjectProvider;

/**
 * Code widget
 */
class TemplateCode extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ThemeTweaker/themetweaker/webmaster_mode';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/template_code.twig';
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/template_code.js';

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
        $list[] = $this->getDir() . '/template_code.css';

        return $list;
    }

    /**
     * Retrieve content from the template file
     *
     * @return mixed
     */
    protected function getTemplateContent()
    {
        $value = '';

        if ($this->getTemplateObject() && $this->getTemplateObject()->getId()) {
            $localPath = $this->getTemplateObject()->getTemplate();
        } else {
            $localPath = $this->getTemplatePath();
        }

        $layout = \XLite\Core\Layout::getInstance();
        $tweakerPath = $layout->getTweakerPathByLocalPath($localPath, \XLite::INTERFACE_WEB, \XLite::ZONE_CUSTOMER);

        if ($layout->hasTweakerTemplate($tweakerPath)) {
            return $layout->getTweakerContent($tweakerPath);
        }

        if ($localTemplatePath = $layout->getResourceFullPath($localPath, \XLite::INTERFACE_WEB, \XLite::ZONE_CUSTOMER)) {
            $value = FileManager::read($localTemplatePath);
        } elseif ($localTemplatePath = $layout->getResourceFullPath($localPath, \XLite::INTERFACE_WEB, \XLite::ZONE_COMMON)) {
            $value = FileManager::read($localTemplatePath);
        }

        return $value;
    }

    /**
     * @return integer
     */
    protected function getTemplateObjectId()
    {
        return $this->getTemplateObject()
            ? $this->getTemplateObject()->getId()
            : null;
    }

    protected function getWidgetData()
    {
        return json_encode([
            'templateId' => $this->getTemplateObjectId()
        ]);
    }

    /**
     * @return \XC\ThemeTweaker\Model\Template
     */
    protected function getTemplateObject()
    {
        return TemplateObjectProvider::getInstance()->getTemplateObject();
    }

    /**
     * @return string
     */
    protected function getTemplatePath()
    {
        return TemplateObjectProvider::getInstance()->getTemplatePath();
    }
}
