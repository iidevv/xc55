<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\ThemeTweaker;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\PreloadedLabels\ProviderInterface;
use XC\ThemeTweaker\Core;

/**
 * Widget with resources for inline content editing
 *
 * @ListChild (list="themetweaker-panel--content", weight="100")
 */
class InlineEditor extends \XLite\View\AView implements ProviderInterface
{
    /**
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = [];

        $list[static::RESOURCE_JS][]  = 'froala-editor/js/froala_editor.pkgd.min.js';
        $list[static::RESOURCE_JS][]  = 'froala-editor/js/froala_editor.activate.js';
        $list[static::RESOURCE_CSS][] = 'froala-editor/css/froala_editor.pkgd.min.css';
        $list[static::RESOURCE_JS][]  = $this->getEditorLanguageResource();

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = [];
        $list[] = $this->getDir() . '/editor_style.css';

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list   = [];
        $list[] = $this->getDir() . '/inline_editable_controller.js';
        $list[] = $this->getDir() . '/panel_controller.js';

        return $list;
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return 'modules/XC/ThemeTweaker/themetweaker/inline_editable';
    }

    /**
     * Return resource structure for content editor language file.
     * By default there are several ready-to-use language files from content editor project.
     * The translation module is able to use its own language validation file.
     * It should decorate this method for this case.
     *
     * @return array
     */
    protected function getEditorLanguageResource()
    {
        return [
            'file' => $this->getEditorLanguageFile(),
            'no_minify' => true,
        ];
    }

    /**
     * Return content editor language file path.
     *
     * @return string
     */
    protected function getEditorLanguageFile()
    {
        return 'froala-editor/js/languages/'
            . $this->getCurrentLanguageCode()
            . '.js';
    }

    /**
     * Gets current language code and fixes it in case of en-GB and similar.
     *
     * @return string
     */
    protected function getCurrentLanguageCode()
    {
        $code = $this->getCurrentLanguage()->getCode();

        switch ($code) {
            case 'en':
                return 'en_gb';

            case 'pt':
                return 'pt_pt';

            case 'zh':
                return 'zh_cn';

            default:
                return $code;
        }
    }

    /**
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && Core\ThemeTweaker::getInstance()->isInInlineEditorMode();
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/panel.twig';
    }

    /**
     * @return array
     */
    public function getPreloadedLanguageLabels()
    {
        $list = [
            'Enable',
            'Disable',
            'Save changes',
            'Exit product preview',
            'Exiting...',
            'Changes were successfully saved',
            'Unable to save changes',
            'You are now in preview mode',
            'You have unsaved changes. Are you really sure to exit the preview?',
        ];

        $data = [];
        foreach ($list as $name) {
            $data[$name] = static::t($name);
        }

        return $data;
    }
}
