<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Profile
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Profile
{
    /**
     * customer Survey
     *
     * @var \QSL\CustomerSatisfaction\Model\Survey
     *
     * @ORM\OneToOne (targetEntity="QSL\CustomerSatisfaction\Model\Survey", mappedBy="customer", cascade={"merge","detach"})
     */
    protected $customerSurveys;

    /**
     * customer Survey
     *
     * @var \QSL\CustomerSatisfaction\Model\Survey
     *
     * @ORM\OneToOne (targetEntity="QSL\CustomerSatisfaction\Model\Survey", mappedBy="manager", cascade={"merge","detach"})
     */
    protected $managerSurveys;

    /**
     * Gets the customer Survey.
     *
     * @return \QSL\CustomerSatisfaction\Model\Survey
     */
    public function getCustomerSurveys()
    {
        return $this->customerSurveys;
    }

    /**
     * Sets the customer Survey.
     *
     * @param \QSL\CustomerSatisfaction\Model\Survey $customerSurveys the customer surveys
     *
     * @return self
     */
    public function setCustomerSurveys(\QSL\CustomerSatisfaction\Model\Survey $customerSurveys)
    {
        $this->customerSurveys = $customerSurveys;

        return $this;
    }

    /**
     * Gets the customer Survey.
     *
     * @return \QSL\CustomerSatisfaction\Model\Survey
     */
    public function getManagerSurveys()
    {
        return $this->managerSurveys;
    }

    /**
     * Sets the customer Survey.
     *
     * @param \QSL\CustomerSatisfaction\Model\Survey $managerSurveys the manager surveys
     *
     * @return self
     */
    public function setManagerSurveys(\QSL\CustomerSatisfaction\Model\Survey $managerSurveys)
    {
        $this->managerSurveys = $managerSurveys;

        return $this;
    }
}
