<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Core\Mail\Sender;
use XLite\Core\Mailer;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use XC\ThemeTweaker\Core\Notifications\Data;
use XC\ThemeTweaker\Core\Notifications\DataPreProcessor;

/**
 * Notification
 * @Extender\Mixin
 */
class Notification extends \XLite\Controller\Admin\Notification
{
    protected function doActionSendTestEmail()
    {
        $request            = Request::getInstance();
        $dataSource         = $this->getDataSource();
        $templatesDirectory = $request->templatesDirectory;
        $zone               = $request->page === \XLite::ZONE_ADMIN
            ? \XLite::ZONE_ADMIN
            : \XLite::ZONE_CUSTOMER;

        if (
            $dataSource->isEditable()
            && $dataSource->isAvailable()
        ) {
            $to = Auth::getInstance()->getProfile()->getLogin();

            $result = Mailer::getInstance()->sendNotificationPreview(
                $templatesDirectory,
                $to,
                $zone,
                DataPreProcessor::prepareDataForNotification(
                    $templatesDirectory,
                    $dataSource->getData()
                )
            );

            if ($result) {
                TopMessage::addInfo('The test email notification has been sent to X', ['email' => $to]);
            } else {
                TopMessage::addWarning('Failure sending test email to X', ['email' => $to]);
            }
        }

        $this->setReturnURL($this->buildFullURL(
            $request->from_notification ? 'notification' : 'notification_editor',
            '',
            [
                'templatesDirectory'                          => $request->templatesDirectory,
                $request->from_notification ? 'page' : 'zone' => $zone,
            ]
        ));
    }

    /**
     * Process request
     */
    public function processRequest()
    {
        if ($this->getNotification()) {
            $request    = Request::getInstance();
            $dataSource = $this->getDataSource();

            if (
                $request->preview
                && $dataSource->isEditable()
                && $dataSource->isAvailable()
            ) {
                $zone = Request::getInstance()->page === \XLite::ZONE_ADMIN
                    ? \XLite::ZONE_ADMIN
                    : \XLite::ZONE_CUSTOMER;

                \XLite::getInstance()->addContent(
                    Sender::getNotificationPreviewContent(
                        $request->templatesDirectory,
                        DataPreProcessor::prepareDataForNotification(
                            $request->templatesDirectory,
                            $dataSource->getData()
                        ),
                        $zone
                    )
                );

                return;
            }
        }

        parent::processRequest();
    }

    /**
     * @return Data
     */
    public function getDataSource()
    {
        return $this->dataSource
            ?: ($this->dataSource = new Data($this->getNotification()));
    }
}
