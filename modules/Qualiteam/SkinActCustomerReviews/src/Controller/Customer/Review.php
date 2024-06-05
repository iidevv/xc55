<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCustomerReviews\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Request;


/**
 * @Extender\Mixin
 */
class Review extends \XC\Reviews\Controller\Customer\Review
{

    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['vote']);
    }

    protected function doActionVote()
    {
        $request = Request::getInstance();

        $voted = [];

        if (is_array(\XLite\Core\Session::getInstance()->voted)) {
            $voted = array_merge($voted, \XLite\Core\Session::getInstance()->voted);
        }

        if (in_array((int)$request->id, $voted, true)) {
            exit(\json_encode([
                'status' => 'error',
                'message' => static::t('SkinActCustomerReviews already voted')
            ]));
        }

        $review = Database::getRepo('\XC\Reviews\Model\Review')->find((int)$request->id);

        if ($review) {

            if ($request->positive > 0) {

                Database::getRepo('\XC\Reviews\Model\Review')
                    ->createQueryBuilder('r')
                    ->update()
                    ->set('r.useful', 'r.useful + 1')
                    ->where('r.id = :id')
                    ->setParameter('id', $review->getId())
                    ->getQuery()
                    ->execute();

            } else {

                Database::getRepo('\XC\Reviews\Model\Review')
                    ->createQueryBuilder('r')
                    ->update()
                    ->set('r.nonUseful', 'r.nonUseful + 1')
                    ->where('r.id = :id')
                    ->setParameter('id', $review->getId())
                    ->getQuery()
                    ->execute();
            }

            Database::getEM()->refresh($review);

            $voted[] = $review->getId();

            \XLite\Core\Session::getInstance()->voted = $voted;

            exit(\json_encode([
                'useful' => static::t('SkinActCustomerReviews useful Yes') . ' (' . $review->getUseful() . ')',
                'nonUseful' => static::t('SkinActCustomerReviews useful No') . ' (' . $review->getNonUseful() . ')',
                'status' => 'ok',
                'message' => static::t('SkinActCustomerReviews vote success')
            ]));
        }

        exit(\json_encode([
            'status' => 'error',
            'message' => static::t('SkinActCustomerReviews vote fail')
        ]));

    }

    protected function getEditableFields()
    {
        return array_merge(parent::getEditableFields(), [
            'title', 'advantages', 'disadvantages'
        ]);
    }

    protected function replaces($string)
    {
        return str_replace("\r\n", "\n", $string);
    }

    protected function getRequestData()
    {
        $data = parent::getRequestData();

        $maxLen = 5000;

        if (!empty($data['advantages'])) {
            $data['advantages'] = $this->replaces($data['advantages']);
            $data['advantages'] = mb_substr($data['advantages'], 0, $maxLen);
        }

        if (!empty($data['disadvantages'])) {
            $data['disadvantages'] = $this->replaces($data['disadvantages']);
            $data['disadvantages'] = mb_substr($data['disadvantages'], 0, $maxLen);
        }

        if (!empty($data['title'])) {
            $data['title'] = $this->replaces($data['title']);
            $data['title'] = mb_substr($data['title'], 0, $maxLen);
        }

        if (!empty($data['review'])) {
            $data['review'] = $this->replaces($data['review']);
            $data['review'] = mb_substr($data['review'], 0, $maxLen);
        }

        return $data;
    }

    protected function doActionUpdate()
    {
        $data = $this->getRequestData();

        $review = $this->getReview();

        $status = (\XLite\Core\Config::getInstance()->XC->Reviews->disablePendingReviews === false || empty($data['review']))
            ? \XC\Reviews\Model\Review::STATUS_APPROVED
            : \XC\Reviews\Model\Review::STATUS_PENDING;

        $review->setStatus($status);
        $review->map($data);

        if ($status === \XC\Reviews\Model\Review::STATUS_PENDING) {
            $review->setIsNew(true);
        }

        \XLite\Core\Database::getEM()->flush();

        \XLite\Core\TopMessage::addInfo(
            static::t('Your review has been updated. Thank your for sharing your opinion with us!')
        );

        $files = Request::getInstance()->review_files;
        $files = is_array($files) ? $files : [];

        $review->processFiles('files', $files);

        Database::getEM()->flush();
    }

    protected function doActionCreate()
    {
        $data = $this->getRequestData();

        $profile = $this->getReviewerProfile();

        $review = new \XC\Reviews\Model\Review();

        $review->map($data);
        $review->setProfile($profile);

        if (!$review->getReviewerName()) {
            $review->setReviewerName($this->getProfileField('reviewerName'));
        }

        $status = (\XLite\Core\Config::getInstance()->XC->Reviews->disablePendingReviews === false || !$review->getReview())
            ? \XC\Reviews\Model\Review::STATUS_APPROVED
            : \XC\Reviews\Model\Review::STATUS_PENDING;

        $review->setStatus($status);

        if (
            $this->isValidReviewKey()
            && $profile
            && ($reviewKey = $this->getReviewKey())
            && $reviewKey->getOrder()
            && $reviewKey->getOrder()->getOrigProfile()
            && $reviewKey->getOrder()->getOrigProfile()->getProfileId() == $profile->getProfileId()
        ) {
            // Set relation between review key and submitted review
            $review->setReviewKey($reviewKey);
            $reviewKey->addReviews($review);
        }

        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getProductId());
        $review->setProduct($product);
        $product->addReviews($review);

        \XLite\Core\Database::getEM()->flush();

        $message = 'Thank your for sharing your opinion with us!';

        if (!$review->getReview()) {
            $message = 'Your product rating is saved. Thank you!';
        }

        \XLite\Core\TopMessage::addInfo(
            static::t($message)
        );

        $files = Request::getInstance()->review_files;
        $files = is_array($files) ? $files : [];

        $review->processFiles('files', $files);

        \XLite\Core\Database::getEM()->flush();

    }
}