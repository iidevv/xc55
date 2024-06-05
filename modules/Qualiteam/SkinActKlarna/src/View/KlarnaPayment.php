<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\View;

use XCart\Container;
use XLite\View\AView;
use Qualiteam\SkinActKlarna\Traits\KlarnaTrait;
use Qualiteam\SkinActKlarna\Core\KlarnaCheckout;

class KlarnaPayment extends AView
{
    use KlarnaTrait;

    protected function getKlarnaSessionsContainer(): KlarnaCheckout
    {
        return Container::getContainer()
            ->get('klarna.service.api.payments.sessions');
    }

    protected function getKlarnaSessions(): array
    {
        return $this->getKlarnaSessionsContainer()->getKlarnaSessions();
    }

    public function getKlarnaPaymentMethods(): array
    {
        $session = $this->getKlarnaSessions();
        return $session['payment_method_categories'];
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getModulePath() . '/checkout/klarna.js';

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getModulePath() . '/checkout/klarna.less';

        return $list;
    }

    public function displayCommentedData(array $data)
    {
        if (!empty($data)) {
            echo('<script type="text/x-cart-data">' . "\r\n" . json_encode($data) . "\r\n" . '</script>' . "\r\n");
        }
    }

    public function getCommentedData(): array
    {
        return [
            "klarna_session" => $this->getKlarnaSessions(),
            "paymentId" => Container::getContainer()->get('klarna.configuration')->getMethodId(),
            "klarnaCategoriesCount" => $this->getKlarnaCategoriesCount(),
        ];
    }

    protected function getKlarnaCategoriesCount(): int
    {
        return count($this->getKlarnaPaymentMethods());
    }

    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/checkout/checkout.twig';
    }
}