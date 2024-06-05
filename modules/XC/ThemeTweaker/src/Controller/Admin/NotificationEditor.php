<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Controller\Admin;

use XLite\Core\Database;
use XLite\Core\Request;
use XC\ThemeTweaker\Core\Notifications\Data;
use XC\ThemeTweaker\Core\Notifications\ErrorTranslator;

/**
 * ThemeTweaker controller
 */
class NotificationEditor extends \XLite\Controller\Admin\AAdmin
{
    protected $dataSource;

    protected $failedTemplates = [];

    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->params = array_merge($this->params, ['templatesDirectory', 'zone']);
    }

    public function handleRequest()
    {
        parent::handleRequest();

        if (
            !$this->getAction()
            && !$this->isZoneValid()
        ) {
            $notification = $this->getNotification();

            $zone = $notification->getAvailableForCustomer() || $notification->getEnabledForCustomer()
                ? \XLite::ZONE_CUSTOMER
                : \XLite::ZONE_ADMIN;

            $this->redirect($this->buildURL('notification_editor', '', [
                'templatesDirectory' => $this->getNotification()->getTemplatesDirectory(),
                'zone'               => $zone,
            ]));
        }
    }

    /**
     * @return bool
     */
    protected function isZoneValid()
    {
        $notification = $this->getNotification();
        $zone         = Request::getInstance()->zone;

        if ($zone) {
            if ($zone === \XLite::ZONE_ADMIN) {
                return $notification->getAvailableForAdmin()
                    || $notification->getEnabledForAdmin();
            } elseif ($zone === \XLite::ZONE_CUSTOMER) {
                return $notification->getAvailableForCustomer()
                    || $notification->getEnabledForCustomer();
            }
        }

        return false;
    }

    public function isVisible()
    {
        return parent::isVisible()
            && $this->getNotification()
            && $this->getDataSource()->isEditable()
            && $this->getDataSource()->isAvailable();
    }

    protected function isDisplayHtmlTree()
    {
        return false;
    }

    protected function doNoAction()
    {
        parent::doNoAction();

        if (!$this->getDataSource()->isSuitable()) {
            foreach ($this->getDataSource()->getSuitabilityErrors() as $provider => $errors) {
                foreach ($errors as $error) {
                    $translation = ErrorTranslator::translateSuitabilityError(
                        $provider,
                        $error['code'] ?? null,
                        $error['value'] ?? null
                    );

                    if ($translation) {
                        switch ($error['type'] ?? null) {
                            case 'warning':
                                \XLite\Core\TopMessage::addWarning($translation);
                                break;
                            case 'info':
                                \XLite\Core\TopMessage::addInfo($translation);
                                break;
                            default:
                                \XLite\Core\TopMessage::addError($translation);
                        }
                    }
                }
            }
        }
    }

    /**
     * @return \XLite\Model\Notification|null
     */
    public function getNotification()
    {
        return Database::getRepo('XLite\Model\Notification')->find(
            Request::getInstance()->templatesDirectory
        );
    }

    protected function doActionChangeData()
    {
        $data = Request::getInstance()->data ?: [];

        foreach ($this->getDataSource()->update($data) as $provider => $errors) {
            foreach ($errors as $error) {
                $translation = ErrorTranslator::translateError(
                    $provider,
                    $error['code'] ?? null,
                    $error['value'] ?? null
                );

                if ($translation) {
                    \XLite\Core\TopMessage::addWarning($translation);
                }
            }
        }
    }

    /**
     * @return Data
     */
    public function getDataSource()
    {
        return $this->dataSource
            ?: ($this->dataSource = new Data($this->getNotification()));
    }

    /**
     * Mark later
     *
     * @param string $template
     */
    public function addFailedTemplate($template)
    {
        $this->failedTemplates = array_unique(
            array_merge($this->failedTemplates, [
                $template,
            ])
        );
    }

    /**
     * @return array
     */
    public function getFailedTemplates()
    {
        return $this->failedTemplates;
    }

    /**
     * @return bool
     */
    public function isTemplateFailed()
    {
        return !!$this->failedTemplates;
    }
}
