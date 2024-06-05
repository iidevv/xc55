<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Common;

use XLite\Core\Cache\ExecuteCachedTrait;

class TestEmail extends \XLite\Core\Mail\AMail
{
    use ExecuteCachedTrait;

    private $error = null;

    public static function getZone()
    {
        return \XLite::ZONE_ADMIN;
    }

    public static function getDir()
    {
        return 'test_email';
    }

    public function __construct($from, $to, $body)
    {
        parent::__construct();

        $this->setFrom($from);
        $this->setTo($to);

        $this->appendData(['body' => $body]);
    }

    public function send()
    {
        $this->executeCachedRuntime(function () {
            if (\XLite\Core\Config::getInstance()->NotificationAttachments->attach_pdf_invoices) {
                $page = new \XLite\View\PdfPage\Test();
                $page->setWidgetParams([
                    'zone' => static::getZone(),
                ]);

                $handler = \XLite\Core\Pdf\Handler::getDefault();
                $handler->handlePdfPage($page);
                $document = $handler->output();
                $filename = 'test_attach.pdf';

                $this->addAttachment([$document, $filename, 'base64', 'application/pdf']);
            }
        });
        return parent::send();
    }

    public function handleSendError($error, $message)
    {
        parent::handleSendError($error, $message);

        $this->setError($error);
    }

    /**
     * Return Error
     *
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set Error
     *
     * @param string|null $error
     *
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }
}
