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

class GenerateTokenHashCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('utils:generate-token-hash');
        $this->setDescription('Generate token hash.');
        $this->addArgument('token', InputArgument::REQUIRED, 'Token string.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(generateTokenHash($input->getArgument('token')));
        return Command::SUCCESS;
    }
}