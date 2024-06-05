<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\Model\Repo;


use XLite\Core\Converter;

class Job extends \XLite\Model\Repo\ARepo
{
    public function getJobForDemo()
    {
        $qb = $this->createPureQueryBuilder();
        return $qb->where('j.id > 0')->getSingleResult();
    }

    public function getEnabledJobById($id)
    {
        return $this->findOneBy(['enabled' => true, 'id' => $id]);
    }

    public function getCareersForCustomer()
    {
        $qb = $this->createPureQueryBuilder();

        $qb->where('j.enabled = 1')
            ->andWhere('j.publicationDate < :time')
            ->setParameter('time', Converter::time())
            ->orderBy('j.position', 'ASC');

        return $qb->getResult();
    }


    public function getOneCareerForCustomerById($id)
    {
        $qb = $this->createPureQueryBuilder();

        return $qb->where('j.enabled = 1')
            ->andWhere('j.publicationDate < :time')
            ->andWhere('j.id = :id')
            ->setParameter('time', Converter::time())
            ->setParameter('id', $id)->getSingleResult();
    }

}