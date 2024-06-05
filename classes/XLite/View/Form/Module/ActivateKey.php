<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Module;

use XLite\Core\PreloadedLabels\ProviderInterface;

class ActivateKey extends \XLite\View\Form\Module\AModule implements ProviderInterface
{
    protected function getWebDir(): string
    {
        $hostDetails = \Includes\Utils\ConfigParser::getOptions(['host_details']);

        return $hostDetails['web_dir'];
    }

    protected function getFormAction(): string
    {
        return $this->getWebDir() . '/service.php/api/licenses';
    }

    protected function getFormParams(): array
    {
        return [];
    }

    protected function getDefaultTarget(): string
    {
        return $this->isPopupTarget()
            ? 'activate_key_popup'
            : 'activate_key';
    }

    /**
     * The 'trial_notice' and 'activate_key' targets are used
     * when form goes in the popup window
     */
    protected function isPopupTarget(): bool
    {
        return in_array(
            \XLite\Core\Request::getInstance()->target,
            [
                'trial_notice',
                'activate_key',
            ]
        );
    }

    public function getPreloadedLanguageLabels(): array
    {
        return [
            'X-Cart license key has been successfully verified' => static::t('X-Cart license key has been successfully verified'),
            'License key has been successfully verified and activated for "{{name}}" module by "{{author}}" author.' => static::t('License key has been successfully verified and activated for "{{name}}" module by "{{author}}" author.'),
        ];
    }
}
