<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace XPay\XPaymentsCloud\Controller\Admin;

use \XPay\XPaymentsCloud\Main as XPaymentsHelper;

/**
 * X-Payments saved credit cards
 */
class XpaymentsBulkOperation extends \XLite\Controller\Admin\AAdmin
{
    /** 
     * Batch ID
     *
     * @var string
     */
    protected $batchId = '';

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Bulk operation');
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * Handle request
     *
     * @return void
     */
    public function handleRequest()
    {
        $this->batchId = \XLite\Core\Database::getRepo('XPay\XPaymentsCloud\Model\BulkOperation')
            ->getActiveBatchId(\XPay\XPaymentsCloud\Model\BulkOperation::OPERATION_CAPTURE);

        parent::handleRequest();
    }

    /** 
     * Get orders data (current statuses)
     *
     * @return array [DomID -> StatusID]
     */
    protected function getOrdersData()
    {
        $data = array();

        $operations = \XLite\Core\Database::getRepo('\XPay\XPaymentsCloud\Model\BulkOperation')->findAll();

        foreach ($operations as $operation) {
            $order = \XLite\Core\Database::getRepo('\XLite\Model\Order')->find($operation->getOrderId());
            $domId = sprintf('#data-%s-paymentstatus', $operation->getOrderId());
            $data[$domId] = $order->getPaymentStatus()->getId();
        }

        return $data;
    }

    /**
     * Get data action
     *  
     * @return void
     */
    protected function doActionGetData()
    {
        try {

            $response = XPaymentsHelper::getClient()->doGetBulkOperation($this->batchId);

            $total = (int)$response->total;
            $remain = count($response->payments);
            $done = (int)($total - $remain);

            $finished = ($done == $total)
                || 'finished' == $response->status;

            $message = static::t(
                'Captured {{done}} of {{total}} orders',
                array(
                    'done'  => $done,
                    'total' => $total,
                )
            );

            $data = array(
                'error'    => false,
                'finished' => $finished,
                'percent'  => round(100 * ($done / $total)),
                'message'  => $message,
                'orders'   => $this->getOrdersData(),
            );

            if ($finished) {
                \XLite\Core\Database::getRepo('\XPay\XPaymentsCloud\Model\BulkOperation')->clearAll();
            }

        } catch (\Exception $exception) {
            $data = array(
                'error'    => true,
                'finished' => false,
                'message'  => $exception->getMessage(),
            );
        }

        $this->setPureAction(true);
        $this->setInternalRedirect(false);

        echo json_encode($data);
    }

    /**
     * Set if the form id is needed to make an actions
     * Form class uses this method to check if the form id should be added
     *
     * @return boolean
     */
    public static function needFormId()
    {
        return false; // TODO: CHECK THIS!!!!!
    }
}
