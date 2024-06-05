<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Order
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * Survey Order ID
     *
     * @var \QSL\CustomerSatisfaction\Model\Survey
     *
     * @ORM\OneToOne (targetEntity="QSL\CustomerSatisfaction\Model\Survey", mappedBy="order", cascade={"all"})
     */
    protected $survey;

    /**
     * Date when survey will be send
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $surveyFutureSendDate = 0;

    /**
     * Set SurveyFutureSendDate
     *
     * @param int $surveyFutureSendDate
     * @return Order
     */
    public function setSurveyFutureSendDate($surveyFutureSendDate)
    {
        $this->surveyFutureSendDate = $surveyFutureSendDate;

        return $this;
    }

    /**
     * Get SurveyFutureSendDate
     *
     * @return int
     */
    public function getSurveyFutureSendDate()
    {
        return $this->surveyFutureSendDate;
    }

    /**
     * Set payment status
     *
     * @param mixed $paymentStatus Payment status
     *
     * @return void
     */
    public function setPaymentStatus($paymentStatus = null)
    {
        $oldStatus = $this->getPaymentStatus();

        parent::setPaymentStatus($paymentStatus);

        $newPaymentStatus = $this->getPaymentStatus();
        $statusChanged = $newPaymentStatus && (!$oldStatus || $oldStatus->getId() !== $newPaymentStatus->getId());
        $CSConfig       = \XLite\Core\Config::getInstance()->QSL->CustomerSatisfaction;

        if (
            $statusChanged
            && $CSConfig->cs_send_email_by == 'S'
            && $CSConfig->cs_payment_status == $newPaymentStatus->getId()
        ) {
            $this->createSatisfactionSurvey();
        }
    }

    /**
     * Set shipping status
     *
     * @param mixed $shippingStatus Shipping status
     *
     * @return void
     */
    public function setShippingStatus($shippingStatus = null)
    {
        $oldStatus = $this->getShippingStatus();

        parent::setShippingStatus($shippingStatus);

        $newShippingStatus = $this->getShippingStatus();
        $statusChanged = $newShippingStatus && (!$oldStatus || $oldStatus->getId() !== $newShippingStatus->getId());
        $CSConfig       = \XLite\Core\Config::getInstance()->QSL->CustomerSatisfaction;

        if (
            $statusChanged
            && $CSConfig->cs_send_email_by == 'T'
            && $CSConfig->cs_shipping_status == $newShippingStatus->getId()
        ) {
            $this->createSatisfactionSurvey();
        }
    }

    protected function createSatisfactionSurvey()
    {
        if (
            $this->isPersistent()
            && $this->getOrderNumber()
        ) {
            $delayInDays = \XLite\Core\Config::getInstance()->QSL->CustomerSatisfaction->cs_delay_in_days;
            if ($delayInDays == 0 || empty($delayInDays)) {
                \XLite\Core\Database::getRepo('QSL\CustomerSatisfaction\Model\Survey')->createSurvey($this);
            } else {
                $this->setSurveyFutureSendDate(strtotime('+' . $delayInDays . ' days', $this->getDate()));
                \XLite\Core\Database::getEM()->flush($this);
            }
        }
    }

    /**
     * Called when an order successfully placed by a client
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function processSucceed()
    {
        parent::processSucceed();

        $paymentStatus  = $this->getPaymentStatus();
        $shippingStatus = $this->getShippingStatus();
        $CSConfig       = \XLite\Core\Config::getInstance()->QSL->CustomerSatisfaction;

        if (
            $CSConfig->cs_send_email_by == 'S' && $CSConfig->cs_payment_status == $paymentStatus->getId()
            || $CSConfig->cs_send_email_by == 'T' && $CSConfig->cs_shipping_status == $shippingStatus->getId()
        ) {
            $this->createSatisfactionSurvey();
        }
    }

    /**
     * Gets the Survey Order ID.
     *
     * @return \QSL\CustomerSatisfaction\Model\Survey
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * Sets the Survey Order ID.
     *
     * @param \QSL\CustomerSatisfaction\Model\Survey $survey the survey
     *
     * @return self
     */
    public function setSurvey(\QSL\CustomerSatisfaction\Model\Survey $survey)
    {
        $this->survey = $survey;

        return $this;
    }
}
