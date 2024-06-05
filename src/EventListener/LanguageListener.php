<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\EventListener;

use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use XLite\API\Language;
use XLite\Core\Database;

final class LanguageListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (substr($request->getRequestUri(), 0, 5) !== '/api/') {
            return;
        }

        $xc = \XLite::getInstance();
        $xc->run(true);

        $acceptLanguage = $request->headers->get('Accept-Language');
        if (empty($acceptLanguage)) {
            return;
        }

        $acceptLanguageParts = HeaderUtils::split($acceptLanguage, ',;');
        if (empty($acceptLanguageParts[0][0])) {
            return;
        }

        $code = explode('-', $acceptLanguageParts[0][0])[0];
        $language = Database::getRepo('XLite\Model\Language')->findOneByCode($code);

        if (isset($language) && $language->getEnabled()) {
            $langCode = $language->getCode();
            Language::getInstance()->setLanguageCode($langCode);
        }
    }
}
