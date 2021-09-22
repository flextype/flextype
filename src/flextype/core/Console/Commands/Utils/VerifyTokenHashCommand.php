<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Console\Commands\Utils;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

class VerifyTokenHashCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('utils:verify-token-hash');
        $this->setDescription('Verify token hash.');
        $this->addArgument('token', InputArgument::REQUIRED, 'Token.');
        $this->addArgument('token-hash', InputArgument::REQUIRED, 'Token hash.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (verrifyTokenHash($input->getArgument('token'), $input->getArgument('token-hash') )) {
            $io->success('Token ' . $input->getArgument('token') . ' is verified');
            return Command::SUCCESS;
        } else {
            $io->error('Token ' . $input->getArgument('token') . ' isn\'t verified');
            return Command::FAILURE;
        }
    }
}