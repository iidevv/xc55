<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\BulkOperation;

/**
 * Bulk operation progress section
 */
class Progress extends \XLite\View\AView
{
    /**
     * Get skin directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XPay/XPaymentsCloud/bulk_operation/';
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . 'style.css';
        $list[] = 'event_task_progress/style.less';

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . 'controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . 'progress.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return bool
     */
    protected function isVisible()
    {
        $batchId = \XLite\Core\Database::getRepo('XPay\XPaymentsCloud\Model\BulkOperation')
            ->getActiveBatchId(\XPay\XPaymentsCloud\Model\BulkOperation::OPERATION_CAPTURE);

        return parent::isVisible()
            && !empty($batchId);
    }
}
