<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Console\Commands\Entries;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\Table;

class EntriesFetchCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('entries:fetch');
        $this->setDescription('Fetch entry.');
        $this->addArgument('id', InputArgument::OPTIONAL, 'Unique identifier of the entry.');
        $this->addArgument('options', InputArgument::OPTIONAL, 'Options array.');
        $this->addOption('collection', null, InputOption::VALUE_NONE, 'Set this flag to fetch entries collection.');
        $this->addOption('find-depth-from', null, InputOption::VALUE_OPTIONAL, 'Restrict the depth of traversing from.');
        $this->addOption('find-depth-to', null, InputOption::VALUE_OPTIONAL, 'Restrict the depth of traversing to.');
        $this->addOption('find-date-from', null, InputOption::VALUE_OPTIONAL, 'Restrict by a date range from.');
        $this->addOption('find-date-to', null, InputOption::VALUE_OPTIONAL, 'Restrict by a date range to.');
        $this->addOption('find-size-from', null, InputOption::VALUE_OPTIONAL, 'Restrict by a size range from.');
        $this->addOption('find-size-to', null, InputOption::VALUE_OPTIONAL, 'Restrict by a size range to.');
        $this->addOption('find-exclude', null, InputOption::VALUE_OPTIONAL, 'Exclude directories from matching.');
        $this->addOption('find-contains', null, InputOption::VALUE_OPTIONAL, 'Find files by content.');
        $this->addOption('find-not-contains', null, InputOption::VALUE_OPTIONAL, 'Find files by content excludes files containing given pattern.');
        $this->addOption('find-path', null, InputOption::VALUE_OPTIONAL, 'Find files and directories by path.');
        $this->addOption('find-sort-by', null, InputOption::VALUE_OPTIONAL, 'Sort the files and directories by the last accessed, changed or modified time. Values: atime, mtime, ctime.');
        $this->addOption('filter-return', null, InputOption::VALUE_OPTIONAL, 'Return items. Valid values: all, first, last, next, random, shuffle');
        $this->addOption('filter-group-by', null, InputOption::VALUE_OPTIONAL, 'Group by key.');
        $this->addOption('filter-offset', null, InputOption::VALUE_OPTIONAL, 'Extract a slice of the current array with specific offset.');
        $this->addOption('filter-limit', null, InputOption::VALUE_OPTIONAL, 'Extract a slice of the current array with offset 0 and specific length.');
        $this->addOption('filter-sort-by-key', null, InputOption::VALUE_OPTIONAL, 'Sort by key.');
        $this->addOption('filter-sort-by-direction', null, InputOption::VALUE_OPTIONAL, 'Sort by direction. Order direction: DESC (descending) or ASC (ascending)');
        $this->addOption('filter-where', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Filters the array items by a given condition.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $id      = $input->getArgument('id') ? $input->getArgument('id') : '';
        $options = $input->getArgument('options') ? serializers()->json()->decode($input->getArgument('options')) : [];
        
        $input->getOption('collection') and $options['collection'] = true;
        
        if ($input->getOption('find-depth-from') || $input->getOption('find-depth-to')) {
            $options['find']['depth'] = [];
            $input->getOption('find-depth-from') and array_push($options['find']['depth'], $input->getOption('find-depth-from'));
            $input->getOption('find-depth-to') and array_push($options['find']['depth'], $input->getOption('find-depth-to'));
        }

        if ($input->getOption('find-date-from') || $input->getOption('find-date-to')) {
            $options['find']['date'] = [];
            $input->getOption('find-date-from') and array_push($options['find']['date'], $input->getOption('find-date-from'));
            $input->getOption('find-date-to') and array_push($options['find']['date'], $input->getOption('find-date-to'));
        }

        if ($input->getOption('find-size-from') || $input->getOption('find-size-to')) {
            $options['find']['size'] = [];
            $input->getOption('find-size-from') and array_push($options['find']['size'], $input->getOption('find-size-from'));
            $input->getOption('find-size-to') and array_push($options['find']['size'], $input->getOption('find-size-to'));
        }

        $input->getOption('find-exclude') and $options['find']['exclude'] = $input->getOption('find-exclude');
        $input->getOption('find-contains') and $options['find']['contains'] = $input->getOption('find-contains');
        $input->getOption('find-not-contains') and $options['find']['not_contains'] = $input->getOption('find-not-contains');
        $input->getOption('find-path') and $options['find']['path'] = $input->getOption('find-path');
        $input->getOption('find-sort-by') and $options['find']['sort-by'] = $input->getOption('find-sort-by');

        $input->getOption('filter-group-by') and $options['filter']['group_by'] = $input->getOption('filter-sort-by');
        $input->getOption('filter-return') and $options['filter']['return'] = $input->getOption('filter-sort-by');
 
        if ($input->getOption('filter-where')) {
            $filterWhere = $input->getOption('filter-where');
            
            foreach ($filterWhere as $key => $value) {
                $where[] = serializers()->json()->decode($value);
            }
            
            $options['filter']['where'] = $where;
        }

        $output->writeln('');

        if ($data = entries()->fetch($id, $options)) {
            if (isset($options['collection']) && $options['collection'] == true) {
                foreach ($data->toArray() as $item) {
                    foreach(arrays($item)->dot() as $key => $value) {
                        $output->writeln('<info>'.$key.':</info> ' . $value);
                    }
                    $output->writeln('');
                }          
            } else {
                foreach(arrays($data)->dot() as $key => $value) {
                    $output->writeln('<info>'.$key.':</info> ' . $value);
                }
                $output->writeln('');
            }

            return Command::SUCCESS;
        } else {
            $io->error('Entry ' . $id . ' doesn\'t exists');
            return Command::FAILURE;
        }
    }
}