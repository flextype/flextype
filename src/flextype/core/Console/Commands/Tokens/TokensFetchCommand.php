<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS 
 * and with the full functionality of a traditional CMS!
 * 
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 */

namespace Flextype\Console\Commands\Tokens;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\Table;
use function Thermage\div;
use function Thermage\span;
use function Thermage\renderToString;

class TokensFetchCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('tokens:fetch');
        $this->setDescription('Fetch token entry.');
        $this->addArgument('id', InputArgument::OPTIONAL, 'Unique identifier of the token entry.');
        $this->addArgument('options', InputArgument::OPTIONAL, 'Options array.');
        $this->addOption('collection', null, InputOption::VALUE_NONE, 'Set this flag to fetch tokens entries collection.');
        $this->addOption('find-depth-from', null, InputOption::VALUE_OPTIONAL, 'Restrict the tokens entries files depth of traversing from.');
        $this->addOption('find-depth-to', null, InputOption::VALUE_OPTIONAL, 'Restrict the tokens entries files depth of traversing to.');
        $this->addOption('find-date-from', null, InputOption::VALUE_OPTIONAL, 'Restrict the tokens entries files by a date range from.');
        $this->addOption('find-date-to', null, InputOption::VALUE_OPTIONAL, 'Restrict the tokens entries files by a date range to.');
        $this->addOption('find-size-from', null, InputOption::VALUE_OPTIONAL, 'Restrict the tokens entries files by a size range from.');
        $this->addOption('find-size-to', null, InputOption::VALUE_OPTIONAL, 'Restrict the tokens entries files by a size range to.');
        $this->addOption('find-exclude', null, InputOption::VALUE_OPTIONAL, 'Exclude directories from matching.');
        $this->addOption('find-contains', null, InputOption::VALUE_OPTIONAL, 'Find tokens entries files by content.');
        $this->addOption('find-not-contains', null, InputOption::VALUE_OPTIONAL, 'Find tokens entries files by content excludes files containing given pattern.');
        $this->addOption('find-path', null, InputOption::VALUE_OPTIONAL, 'Find tokens entries files and directories by path.');
        $this->addOption('find-sort-by-key', null, InputOption::VALUE_OPTIONAL, 'Sort the tokens entries files and directories by the last accessed, changed or modified time. Values: atime, mtime, ctime.');
        $this->addOption('find-sort-by-direction', null, InputOption::VALUE_OPTIONAL, 'Sort the tokens entries files and directories by direction. Order direction: DESC (descending) or ASC (ascending)');
        $this->addOption('filter-return', null, InputOption::VALUE_OPTIONAL, 'Return items. Valid values: all, first, last, next, random, shuffle');
        $this->addOption('filter-group-by', null, InputOption::VALUE_OPTIONAL, 'Group array collection by key.');
        $this->addOption('filter-offset', null, InputOption::VALUE_OPTIONAL, 'Extract a slice of the current array collection with specific offset.');
        $this->addOption('filter-limit', null, InputOption::VALUE_OPTIONAL, 'Extract a slice of the current array collection with offset 0 and specific length.');
        $this->addOption('filter-sort-by-key', null, InputOption::VALUE_OPTIONAL, 'Sort array collection by key.');
        $this->addOption('filter-sort-by-direction', null, InputOption::VALUE_OPTIONAL, 'Sort array collection by direction. Order direction: DESC (descending) or ASC (ascending)');
        $this->addOption('filter-where', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Filters the array collection fields by a given condition.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id      = $input->getArgument('id') ? $input->getArgument('id') : '';
        $options = [];

        if ($input->getArgument('options')) {
            if (strings($input->getArgument('options'))->isJson()) {
                $options = serializers()->json()->decode($input->getArgument('options'));
            } else {
                parse_str($input->getArgument('options'), $options);
            }
        }

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
        $input->getOption('find-sort-by-key') and $options['find']['sort_by']['key'] = $input->getOption('find-sort-by-key');
        $input->getOption('find-sort-by-direction') and $options['find']['sort_by']['direction'] = $input->getOption('find-sort-by-direction');

        $input->getOption('filter-group-by') and $options['filter']['group_by'] = $input->getOption('filter-group-by');
        $input->getOption('filter-return') and $options['filter']['return'] = $input->getOption('filter-return');
 
        if ($input->getOption('filter-where')) {
            $filterWhere = $input->getOption('filter-where');
            
            foreach ($filterWhere as $key => $value) {

                if (strings($value)->isJson()) {
                    $whereValues = serializers()->json()->decode($value);
                } else {
                    parse_str($value, $whereValues);
                }
        
                $where[] = $whereValues;
            }
            
            $options['filter']['where'] = $where;
        }
        
        $innerData = [];
        $innerDataString = '';
        
        $data = entries()->fetch('tokens/' . $id, $options);

        if (count($data) > 0) {
            if (isset($options['collection']) && $options['collection'] == true) {
                foreach ($data->toArray() as $item) {
                    foreach(collection($item)->dot() as $key => $value) {
                        $innerDataString .= renderToString(span('[b][color=success]' . $key . '[/color][/b]: ' . $value) . PHP_EOL);
                    }
                    $innerData[] = $innerDataString;
                    $innerDataString = '';
                }       
            } else {
                foreach(collection($data)->dot() as $key => $value) {
                    $innerDataString .= renderToString(span('[b][color=success]' . $key . '[/color][/b]: ' . $value) . PHP_EOL);
                }
                $innerData[] = $innerDataString;
                $innerDataString = '';
            }

            foreach ($innerData as $item) {
                $output->write(
                    renderToString(
                        div($item, 'px-2 border-square border-color-success')
                    )
                );
            }

            return Command::SUCCESS;
        } else {
            $output->write(
                renderToString(
                    div('Failure: Token entry [b]' . $id . '[/b] doesn\'t exists.', 
                        'bg-danger px-2 py-1')
                )
            );
            return Command::FAILURE;
        }
    }
}