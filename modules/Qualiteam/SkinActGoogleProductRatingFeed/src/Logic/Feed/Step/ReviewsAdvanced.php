<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\Logic\Feed\Step;

use Doctrine\ORM\PersistentCollection;
use Qualiteam\SkinActGoogleProductRatingFeed\Main;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("Qualiteam\SkinActCustomerReviews")
 */
class ReviewsAdvanced extends Reviews
{
    protected function getReviewRecord(): array
    {
        $list = parent::getReviewRecord();

        if ($this->hasTitle()) {
            $list = Main::sliceArray($list, 'content', $this->prepareTitleTag());
        }

        if ($this->hasAdvantagesDisadvantages()) {
            $list = Main::sliceArray($list, 'content', $this->prepareAdvantagesAndDisadvantagesTag(), true);
        }

        if ($this->hasImages()) {
            $list = Main::sliceArray($list, 'review_url', $this->prepareReviewerImagesTag(), true);
        }

        return $list;
    }

    /**
     * @return bool
     */
    protected function hasTitle(): bool
    {
        return (bool) $this->getReview()->getTitle();
    }

    protected function getTitle(): string
    {
        return $this->getReview()->getTitle();
    }

    protected function prepareTitleTag(): array
    {
        return [
            'title' => $this->getTitle(),
        ];
    }

    /**
     * @return bool
     */
    protected function hasAdvantagesDisadvantages(): bool
    {
        return $this->getReview()->getAdvantages()
            || $this->getReview()->getDisadvantages();
    }

    protected function getAdvantagesTag(): array
    {
        return $this->getReview()->getAdvantages()
            ? [
                'pros' => $this->prepareAdvantagesTag(),
            ]
            : [];
    }

    protected function prepareAdvantagesTag(): array
    {
        return [
            'pro' => $this->getReview()->getAdvantages(),
        ];
    }

    protected function getDisadvantagesTag(): array
    {
        return $this->getReview()->getDisadvantages()
            ? [
                'cons' => $this->prepareDisadvantagesTag(),
            ]
            : [];
    }

    protected function prepareDisadvantagesTag(): array
    {
        return [
            'con' => $this->getReview()->getDisadvantages(),
        ];
    }

    protected function prepareAdvantagesAndDisadvantagesTag(): array
    {
        return array_merge(
            $this->getAdvantagesTag(),
            $this->getDisadvantagesTag()
        );
    }

    /**
     * @return bool
     */
    protected function hasImages(): bool
    {
        return count($this->getReview()->getFiles()) > 0;
    }

    protected function prepareReviewerImagesTag(): array
    {
        return [
            'reviewer_images' => $this->getImages(),
        ];
    }

    protected function getImages(): string
    {
        $images = $this->prepareReviewImages($this->getReview()->getFiles());
        $string = '';

        if (!empty($images)) {
            foreach ($images as $image) {
                $string .= sprintf('<reviewer_image><url>%s</url></reviewer_image>', $image->getFrontUrl());
            }
        }

        return $string;
    }

    protected function prepareReviewImages(PersistentCollection $files): array
    {
        $images = [];

        foreach ($files as $file) {
            if (in_array($file->getMime(), $this->getAllowedMimes(), true)) {
                $images[] = $file;
            }
        }

        return $images;
    }

    protected function getAllowedMimes(): array
    {
        return [
            'image/jpeg',
            'image/jpg',
            'image/png',
        ];
    }
}
