<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\Controller\Customer;


use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Request;

class Career extends \XLite\Controller\Customer\ACustomer
{

    protected function isVisible()
    {
        return $this->getCareer() && parent::isVisible();
    }

    protected function getCareer()
    {
        $id = (int)Request::getInstance()->id;

        if ($id) {

            $qb = Database::getRepo('\Qualiteam\SkinActCareers\Model\Job')->createPureQueryBuilder();

            $career = $qb->where('j.enabled = 1')
                ->andWhere('j.publicationDate < :time')
                ->andWhere('j.id = :id')
                ->setParameter('time', Converter::time())
                ->setParameter('id', $id)->getSingleResult();

            if ($career) {
                return $career;
            }
        }

        return null;
    }

    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(
            static::t('SkinActCareers Careers'),
            $this->buildURL('careers')
        );
    }

    protected function getLocation()
    {
        return $this->getTitle();
    }

    public function getTitle()
    {
        $career = $this->getCareer();

        if ($career) {
            return $career->getTitle();
        }

        return '';
    }

}
