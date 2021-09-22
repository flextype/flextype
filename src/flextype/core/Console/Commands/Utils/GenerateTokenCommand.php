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

class GenerateTokenCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('utils:generate-token');
        $this->setDescription('Generate token.');
        $this->addArgument('length', InputArgument::OPTIONAL, 'Token string length.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(generateToken($input->getArgument('length') ?? 16));
        return Command::SUCCESS;
    }
}