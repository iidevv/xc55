<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Mail\Sender;
use XC\ThemeTweaker\Core\Notifications\Data;
use XC\ThemeTweaker\Core\Notifications\DataPreProcessor;

/**
 * Theme tweaker template page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class NotificationEditor extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'notification_editor';

        return $list;
    }

    protected function isVisible()
    {
        return parent::isVisible() && $this->getDataSource();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/notification_editor/body.twig';
    }

    /**
     * @return string
     */
    protected function getNotificationContent()
    {
        $content = Sender::getNotificationEditableContent(
            $this->getDir(),
            $this->prepareData($this->getData()),
            $this->getZone()
        );

        return \XLite::getController()->isTemplateFailed()
            ? $this->prepareFailedTemplates(\XLite::getController()->getFailedTemplates())
            : $content;
    }

    /**
     * @param array $templates
     *
     * @return string
     */
    protected function prepareFailedTemplates(array $templates)
    {
        return sprintf(
            '<div class="notification_editor">%s:<br> %s</div>',
            static::t('Templates error'),
            implode('<br>', array_map(static function ($template) {
                if (mb_strpos($template, LC_DIR_SKINS) === 0) {
                    $template = mb_substr($template, mb_strlen(LC_DIR_SKINS));
                }

                return $template;
            }, $templates))
        );
    }

    /**
     * Return true if current template's content is empty
     *
     * @return boolean
     */
    protected function isEmptyTemplateContent()
    {
        $result = false;

        $path = $this->getNotificationTemplatePath();

        if ($path) {
            $content = \Includes\Utils\FileManager::read($path);

            if (preg_match('/^[\s\n]*({#[\s\S]*[^#]#})?([\s\S]*?)$/', $content, $m)) {
                $result = empty($m[2]);
            }
        }

        return $result;
    }

    /**
     * Get notification template full or local path
     *
     * @return string
     */
    protected function getNotificationTemplatePath($local = false)
    {
        $fullPath = $this->getNotificationRootTemplate($this->getDataSource()->getDirectory(), $this->getZone());

        return $local
            ? substr($fullPath, strlen(\LC_DIR_SKINS))
            : $fullPath;
    }

    /**
     * Get URL for 'Add TWIG code' button
     *
     * @return boolean
     */
    protected function getAddTwigCodeButtonURL()
    {
        return $this->buildURL(
            'theme_tweaker_template',
            '',
            [
                'template'  => $this->getNotificationTemplatePath(true),
                'interface' => \XLite::INTERFACE_MAIL,
                'zone'      => $this->getZone(),
            ]
        );
    }

    /**
     * Get URL for 'Preview full email' button
     *
     * @return boolean
     */
    protected function getPreviewURL()
    {
        return $this->buildURL(
            'notification',
            '',
            [
                'templatesDirectory' => $this->getDataSource()->getDirectory(),
                'page'               => $this->getZone(),
                'preview'            => true,
            ]
        );
    }

    /**
     * @param $data
     *
     * @return array
     */
    protected function prepareData(array $data)
    {
        return DataPreProcessor::prepareDataForNotification($this->getDir(), $data);
    }

    /**
     * @return Data
     */
    protected function getDataSource()
    {
        return \XLite::getController()->getDataSource();
    }

    /**
     * @return mixed
     */
    protected function getDir()
    {
        return \XLite\Core\Request::getInstance()->templatesDirectory;
    }

    /**
     * @return array
     */
    protected function getData()
    {
        return $this->getDataSource()->getData();
    }

    /**
     * @return string
     */
    protected function getZone()
    {
        return \XLite\Core\Request::getInstance()->zone;
    }
}
