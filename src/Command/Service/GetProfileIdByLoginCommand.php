<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command\Service;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GetProfileIdByLoginCommand extends Command
{
    protected static $defaultName = 'xcart:service:get-profile-id-by-login';

    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->addArgument('login', InputArgument::REQUIRED | InputArgument::IS_ARRAY);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $conn = $this->entityManager->getConnection();
        //$tablePrefix = $conn->getParams()
        $profileId = $conn->fetchOne('SELECT profile_id FROM profiles WHERE login = ?', [1], [Types::STRING]);

        if ($profileId) {
            $output->writeln($profileId);
        }

        return Command::SUCCESS;
    }
}
