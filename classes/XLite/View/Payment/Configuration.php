<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Payment;

/**
 * Payment configuration page
 */
class Configuration extends \XLite\View\AView implements \XLite\Core\PreloadedLabels\ProviderInterface
{
    /**
     * @var array
     */
    protected $banner;

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'payment/configuration/style.less';

        return array_merge(
            $list,
            $this->getWidget([], '\XLite\View\SearchPanel\Payment\Admin\Main')->getCSSFiles(),
            $this->getWidget([], '\XLite\View\ItemsList\Model\Payment\OnlineMethods')->getCSSFiles(),
            $this->getWidget([], '\XLite\View\Pager\Admin\Model\Table')->getCSSFiles(),
            $this->getWidget([], '\XLite\View\FormField\Select\Model\CountrySelector')->getCSSFiles()
        );
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'marketing_info/script.js';

        return array_merge(
            $list,
            $this->getWidget([], '\XLite\View\SearchPanel\Payment\Admin\Main')->getJSFiles(),
            $this->getWidget([], '\XLite\View\ItemsList\Model\Payment\OnlineMethods')->getJSFiles(),
            $this->getWidget([], '\XLite\View\Pager\Admin\Model\Table')->getJSFiles(),
            $this->getWidget([], '\XLite\View\FormField\Select\Model\CountrySelector')->getJSFiles()
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'payment/configuration/body.twig';
    }

    // {{{ Content helpers

    /**
     * Check - has active payment modules
     *
     * @return boolean
     */
    protected function hasPaymentModules()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->hasActivePaymentModules();
    }

    /**
     * Check - has installed all-in-one and acc gateways payment modules or not
     *
     * @return boolean
     */
    protected function hasGateways()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{\XLite\Model\Repo\Payment\Method::P_MODULE_ENABLED} = true;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_TYPE}           = [
            \XLite\Model\Payment\Method::TYPE_ALLINONE,
            \XLite\Model\Payment\Method::TYPE_CC_GATEWAY,
            \XLite\Model\Payment\Method::TYPE_ALTERNATIVE,
        ];

        return 0 < \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->search($cnd, true);
    }

    /**
     * Check - has added predefined payment methods or not
     *
     * @return boolean
     */
    protected function hasAddedPredefinedGateways()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{\XLite\Model\Repo\Payment\Method::P_MODULE_ENABLED} = true;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_ADDED}          = true;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_PREDEFINED}     = true;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_TYPE}           = [
            \XLite\Model\Payment\Method::TYPE_ALLINONE,
            \XLite\Model\Payment\Method::TYPE_CC_GATEWAY,
            \XLite\Model\Payment\Method::TYPE_ALTERNATIVE,
        ];

        return 0 < \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->search($cnd, true);
    }

    /**
     * Check - has added non-predefined payment methods or not
     *
     * @return boolean
     */
    protected function hasAddedNonPredefinedGateways()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{\XLite\Model\Repo\Payment\Method::P_MODULE_ENABLED} = true;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_ADDED}          = true;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_PREDEFINED}     = false;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_TYPE}           = [
            \XLite\Model\Payment\Method::TYPE_ALLINONE,
            \XLite\Model\Payment\Method::TYPE_CC_GATEWAY,
            \XLite\Model\Payment\Method::TYPE_ALTERNATIVE,
        ];

        return 0 < \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->search($cnd, true);
    }

    // }}}

    /**
     * Array of labels in following format.
     *
     * 'label' => 'translation'
     *
     * @return mixed
     */
    public function getPreloadedLanguageLabels()
    {
        return [
            'Are you sure you want to delete the selected payment method?' => static::t('Are you sure you want to delete the selected payment method?'),
        ];
    }
}
