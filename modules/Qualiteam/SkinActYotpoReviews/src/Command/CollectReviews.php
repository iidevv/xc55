<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Command;

use Doctrine\ORM\PersistentCollection;
use Qualiteam\SkinActYotpoReviews\Command\CSV\CSVException;
use Qualiteam\SkinActYotpoReviews\Command\CSV\IFile;
use Qualiteam\SkinActYotpoReviews\Command\DTO\IDTO;
use Qualiteam\SkinActYotpoReviews\Command\Validator\IValidator;
use Qualiteam\SkinActYotpoReviews\Command\Validator\ValidatorException;
use Qualiteam\SkinActYotpoReviews\Core\Configuration\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use XC\Reviews\Model\Review;
use XCart\Container;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Model\Repo\ARepo;

class CollectReviews extends Command
{
    protected function configure()
    {
        $this
            ->setName('SkinActYotpoReview:CollectReviews')
            ->setDescription('Collect reviews to Yotpo service')
            ->addOption('delimiter', 'd', InputOption::VALUE_OPTIONAL, 'Delimiter')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Collect XC reviews');

        $file = $this->getFile();
        $delimiter = $input->getOption('delimiter');

        if ($delimiter) {
            $validator = $this->delimiterValidator($delimiter);

            try {
                $validator->valid();
            } catch (ValidatorException $e) {
                $io->error($e->getMessage());

                return Command::FAILURE;
            }

            $file->setDelimiter($delimiter);
        }

        try {
            $file->initFilePointer();
        } catch (CSVException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $header = $file->getCSVHeader();
        $file->write($header);

        $progress = $io->createProgressBar($this->getReviewsDBCount());
        $progress->setFormat('very_verbose');
        $progress->start();

        $reviews = $this->getReviewsDB();

        foreach ($reviews as $review) {
            $progress->advance();

            $dto = $this->getDTO($review);
            $file->write($dto->getData());
        }

        $file->stop();
        $progress->finish();

        $io->newLine(2);

        return Command::SUCCESS;
    }

    protected function getReviewsDB(): PersistentCollection|iterable
    {
        $cnd = new CommonCell();

        if ($this->isDevMode()) {
            $repo = Database::getRepo(Review::class);
            $alias = $repo?->getDefaultAlias();

            $cnd->{ARepo::P_LIMIT} = [0, 20];
            $cnd->{ARepo::P_ORDER_BY} = ["{$alias}.review", 'DESC'];
        }

        return Database::getRepo(Review::class)?->search($cnd);
    }

    protected function getReviewsDBCount(): int
    {
        return $this->isDevMode() ? 20 : Database::getRepo(Review::class)?->search(null, true);
    }

    protected function isDevMode(): bool
    {
        return $this->getConfiguration()->isDevMode();
    }

    protected function getConfiguration(): Configuration
    {
        return Container::getContainer()?->get('yotpo.reviews.configuration');
    }

    protected function getFile(): IFile
    {
        return new CSV\Reviews();
    }

    protected function getDTO(Review $review): IDTO
    {
        return new DTO\Reviews($review);
    }

    protected function delimiterValidator(string $delimiter): IValidator
    {
        return new Validator\Delimiter($delimiter);
    }
}
