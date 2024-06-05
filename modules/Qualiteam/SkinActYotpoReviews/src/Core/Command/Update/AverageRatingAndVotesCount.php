<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Command\Update;

use Qualiteam\SkinActYotpoReviews\Core\Command\ACreateUpdateCommand;
use Qualiteam\SkinActYotpoReviews\Core\Command\ICommand;
use Qualiteam\SkinActYotpoReviews\Core\Factory\LoggerFactory;
use XLite\Core\Database;
use XLite\Model\Product;

class AverageRatingAndVotesCount extends ACreateUpdateCommand implements ICommand
{
    public const ITEM_LENGTH = 100;

    /**
     * @return void
     * @throws \Exception
     */
    protected function executeCommand(): void
    {
        $i = 0;

        do {
            $processed = $this->updateChunk($i);

            if (0 < $processed) {
                $this->clearEntity();
            }

            $i += $processed;
        } while (0 < $processed);
    }

    /**
     * @param int $position
     * @param int $length
     *
     * @return int
     */
    public function updateChunk(int $position = 0, int $length = self::ITEM_LENGTH): int
    {
        $processed = 0;

        $products = Database::getRepo(Product::class)?->findProductsForUpdateAverageRatingAndVotesCount($position, $length);

        foreach ($products as $product) {
            $this->getResultYotpoRequest($product);

            if (!$this->isErrorResult()) {
                $this->setAverageRating($product);
                $this->setVotesCount($product);
                $this->persistEntity();
            }

            sleep(1);

            $processed++;
        }

        if (0 < $processed) {
            try {
                $this->updateEntity();
            } catch (\Exception $e) {
                LoggerFactory::logger()->error($e->getMessage());
            }
        }

        return $processed;
    }

    /**
     * @param \XLite\Model\Product $product
     *
     * @return void
     */
    protected function setAverageRating(Product $product): void
    {
        $product->setAverageRating(
            $this->getAverageRating()
        );
    }

    /**
     * @return string
     */
    protected function getAverageRating(): string
    {
        return $this->result['response']['bottomline']['average_score'];
    }

    /**
     * @param \XLite\Model\Product $product
     *
     * @return void
     */
    protected function setVotesCount(Product $product): void
    {
        $product->setVotesCount(
            $this->getVotesCount()
        );
    }

    /**
     * @return string
     */
    protected function getVotesCount(): string
    {
        return $this->result['response']['bottomline']['total_reviews'];
    }
}
