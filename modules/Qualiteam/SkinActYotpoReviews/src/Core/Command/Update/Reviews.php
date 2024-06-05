<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Command\Update;

use Qualiteam\SkinActYotpoReviews\Core\Api\Reviews\CollectAllBuilder;
use Qualiteam\SkinActYotpoReviews\Core\Command\ICommand;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Reviews\Get\CollectAll;
use Qualiteam\SkinActYotpoReviews\Core\Task\UpdateYotpoReviews;
use XC\Reviews\Model\Review;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;
use XLite\Model\Product as ProductModel;
use XLite\Model\Task;

class Reviews implements ICommand
{
    use ExecuteCachedTrait;

    private bool $changesInDb = false;

    public function __construct(
        private CollectAll $container,
        private CollectAllBuilder $builder
    ) {
        $this->prepareSinceDate();
    }

    protected function prepareSinceDate(): void
    {
        $lastUpdate = $this->getUpdateReviewsPreviousTask()?->getTriggerTime();

        if ($this->hasPreviousTaskTriggerTime($lastUpdate)) {
            $this->builder->setSinceDate($lastUpdate);
        }
    }

    protected function getUpdateReviewsPreviousTask(): ?Task
    {
        return Database::getRepo(Task::class)
            ?->findOneBy(['owner' => UpdateYotpoReviews::class]);
    }

    protected function hasPreviousTaskTriggerTime(?int $lastUpdate): bool
    {
        return $lastUpdate
            && $lastUpdate > 0;
    }

    public function execute(): void
    {
        $response = $this->getResponse();
        $this->parseResponse($response);
    }

    protected function getResponse(): array
    {
        return $this->container->getData($this->builder);
    }

    protected function parseResponse(array $response): void
    {
        if ($this->hasReviewsInResponse($response)) {
            foreach ($response['reviews'] as $review) {
                $product = $this->getProductBySku($review['sku']);

                if ($this->hasProduct($product)) {
                    $xcReview = $this->createXCReview($product, $review);
                    $this->persistDB($xcReview);
                    $this->setChangesInDb(true);
                }
            }

            $this->checkChangesInDb();
            $this->tryParseAnotherResponsePage();
        }
    }

    protected function checkChangesInDb(): void
    {
        if ($this->hasChangesInDb()) {
            $this->flushAndClearDB();
        }
    }

    protected function hasChangesInDb(): bool
    {
        return $this->changesInDb;
    }

    protected function setChangesInDb(bool $value): void
    {
        $this->changesInDb = $value;
    }

    protected function hasProduct(?ProductModel $product): bool
    {
        return (bool) $product;
    }

    protected function hasReviewsInResponse(array $response): bool
    {
        return count($response['reviews']) > 0;
    }

    protected function getProductBySku(string $sku): ?ProductModel
    {
        return $this->executeCachedRuntime(static function () use ($sku) {
            return Database::getRepo(ProductModel::class)
                ?->findOneBy([
                    'sku' => $sku,
                ]);
        }, [
            __CLASS__,
            __METHOD__,
            $sku,
        ]);
    }

    protected function createXCReview(ProductModel $product, array $review): Review
    {
        $xcReview = new Review();
        $xcReview->setProduct($product);
        $xcReview->setTitle($review['title']);
        $xcReview->setReview($review['content']);
        $xcReview->setRating($review['score']);
        $xcReview->setAdditionDate(strtotime($review['created_at']));
        $xcReview->setReviewerName($review['name']);
        $xcReview->setYotpoId($review['id']);
        $xcReview->setIsNew(false);
        $xcReview->setStatus(1);

        return $xcReview;
    }

    protected function persistDB(Review $review): void
    {
        Database::getEM()->persist($review);
    }

    protected function flushAndClearDB(): void
    {
        Database::getEM()->flush();
        Database::getEM()->clear();
    }

    protected function tryParseAnotherResponsePage(): void
    {
        $this->prepareBuilderPage();

        $response = $this->getResponse();
        $this->parseResponse($response);
    }

    protected function prepareBuilderPage(): void
    {
        $currentPage = $this->builder->getPage();
        $this->builder->setPage($currentPage + 1);
    }
}
