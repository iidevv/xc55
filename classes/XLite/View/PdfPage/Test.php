<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\PdfPage;

/**
 * Pdf test page
 */
class Test extends \XLite\View\APdfPage
{
    /**
     * Returns PDF document title
     *
     * @return string
     */
    public function getDocumentTitle()
    {
        return 'Test PDF page';
    }

    /**
     * Returns PDF-specific stylesheets
     *
     * @return array
     */
    public function getPdfStylesheets()
    {
        return array_merge(
            parent::getPdfStylesheets(),
            [
                'test/test.css'
            ]
        );
    }

    /**
     * Page Html template path
     * @return string
     */
    public function getDefaultTemplate()
    {
        return 'test/test.twig';
    }
}
