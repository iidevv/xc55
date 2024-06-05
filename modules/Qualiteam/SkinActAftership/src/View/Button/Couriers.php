<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View\Button;

use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use XLite\Core\TmpVars;

/**
 * Class couriers
 */
class Couriers extends \XLite\View\Button\Regular
{
    use AftershipTrait;

    /**
     * Default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/button/couriers.twig';
    }

    /**
     * Is show button help block
     *
     * @return bool
     */
    protected function isShowButtonHelpBlock(): bool
    {
        return !empty($this->getTmpInfo());
    }

    /**
     * Get temp info for all couriers
     *
     * @return array|null
     */
    protected function getTmpInfo(): ?array
    {
        return TmpVars::getInstance()->{'aftershipCollectCouriers'};
    }

    /**
     * Get help block label
     *
     * @return string
     * @throws \Exception
     */
    protected function getHelpBlockLabel(): string
    {
        $tmpInfo = $this->getTmpInfo();

        return static::t('SkinActAftership last update on x. x couriers imported', [
            'date'           => $this->getFormattedDate($tmpInfo['last_update']),
            'couriers_count' => $tmpInfo['couriers_count'],
        ]);
    }

    /**
     * Formatted a timestamp to date
     *
     * @param int $timestamp
     *
     * @return string
     */
    protected function getFormattedDate(int $timestamp): string
    {
        $formats = \XLite\Core\Converter::getDateFormatsByStrftimeFormat(
            \XLite\Core\Config::getInstance()->Units->date_format
        );
        $format  = $formats['phpFormat'];

        return date($format, $timestamp);
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getClass(): string
    {
        return parent::getClass() . ' couriers-button';
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles(): array
    {
        $list = parent::getJSFiles();

        $list[] = $this->getModulePath() . '/button/couriers.js';

        return $list;
    }

    protected function getJSCode()
    {
        return 'return false';
    }

    /**
     * Return button text
     *
     * @return string
     */
    protected function getButtonLabel(): string
    {
        return static::t('SkinActAftership get a list of all couriers');
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel(): string
    {
        return static::t('SkinActAftership get a list of all couriers');
    }
}
