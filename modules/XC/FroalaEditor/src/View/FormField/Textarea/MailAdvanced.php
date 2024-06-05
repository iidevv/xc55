<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FroalaEditor\View\FormField\Textarea;

use XLite\Core\Layout;

class MailAdvanced extends \XC\FroalaEditor\View\FormField\Textarea\Advanced
{
    /**
     * @return array
     */
    protected function getFroalaConfiguration()
    {
        return array_merge(parent::getFroalaConfiguration(), [
            'linkAutoPrefix' => ''
        ]);
    }

    /**
     * @return array
     */
    protected function getFroalaToolbarButtons()
    {
        return [
            'fontSize',
            '|', 'bold', 'italic', 'underline', 'strikeThrough', 'color',
            '|', 'paragraphFormat',
            '|', 'align',
            '|', 'undo', 'redo', 'html',
        ];
    }

    /**
     * @return array
     */
    protected function getIframeStyleFiles()
    {
        return array_merge([
            Layout::getInstance()->getResourceWebPath('reset.css', Layout::WEB_PATH_OUTPUT_SHORT, \XLite::INTERFACE_MAIL, \XLite::ZONE_COMMON)
        ], parent::getIframeStyleFiles());
    }

    /**
     * @return string
     */
    protected function getCustomerLessStyles()
    {
        $lessParser = \XLite\Core\LessParser::getInstance();

        $customerLESS = [
            [
                'file'      => Layout::getInstance()->getResourceFullPath('common/style.less', \XLite::INTERFACE_MAIL, \XLite::ZONE_COMMON),
                'media'     => 'screen',
                'weight'    => 0,
                'filelist'  => [
                    'common/style.less',
                ],
                'interface' => \XLite::INTERFACE_MAIL,
                'original'  => 'common/style.less',
                'url'       => Layout::getInstance()->getResourceWebPath('common/style.less', Layout::WEB_PATH_OUTPUT_SHORT, \XLite::INTERFACE_MAIL, \XLite::ZONE_COMMON),
                'less'      => true,
            ],
            [
                'file'      => Layout::getInstance()->getResourceFullPath('mail/core.less', \XLite::INTERFACE_WEB, \XLite::ZONE_COMMON),
                'media'     => 'screen',
                'weight'    => 0,
                'filelist'  => [
                    'mail/core.less',
                ],
                'interface' => \XLite::INTERFACE_WEB,
                'original'  => 'mail/core.less',
                'url'       => Layout::getInstance()->getResourceWebPath('mail/core.less', Layout::WEB_PATH_OUTPUT_SHORT, \XLite::INTERFACE_WEB, \XLite::ZONE_COMMON),
                'less'      => true,
            ],
            [
                'file'      => Layout::getInstance()->getResourceFullPath('modules/XC/FroalaEditor/mail_textarea.less', \XLite::INTERFACE_WEB, \XLite::ZONE_COMMON),
                'media'     => 'screen',
                'weight'    => 0,
                'filelist'  => [
                    'modules/XC/FroalaEditor/mail_textarea.less',
                ],
                'interface' => \XLite::INTERFACE_WEB,
                'original'  => 'modules/XC/FroalaEditor/mail_textarea.less',
                'url'       => Layout::getInstance()->getResourceWebPath('modules/XC/FroalaEditor/mail_textarea.less', Layout::WEB_PATH_OUTPUT_SHORT, \XLite::INTERFACE_WEB, \XLite::ZONE_COMMON),
                'less'      => true,
            ],
        ];

        // Customer LESS files parsing
        $lessParser->setZone('default');

        $lessParser->setHttp('http');
        $style = $lessParser->makeCSS($customerLESS);

        if ($style && isset($style['url'])) {
            return $style['url'];
        }

        return null;
    }

    /**
     * @return array
     */
    protected function getFroalaEditorStyles()
    {
        return [];
    }
}
