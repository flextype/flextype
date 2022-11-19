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

namespace Flextype\Console\Commands\Entries;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function array_push;
use function count;
use function Flextype\collection;
use function Flextype\registry;
use function Flextype\entries;
use function Flextype\serializers;
use function Glowy\Strings\strings;
use function Glowy\Filesystem\filesystem;
use function parse_str;
use function Thermage\div;
use function Thermage\renderToString;
use function Thermage\span;

use const PHP_EOL;

class EntriesExportCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('entries:export');
        $this->setDescription('Export entry.');
        $this->addArgument('id', InputArgument::OPTIONAL, 'Unique identifier of the entry.');
        $this->addArgument('options', InputArgument::OPTIONAL, 'Options array.');
        $this->addOption('collection', null, InputOption::VALUE_NONE, 'Set this flag to fetch entries collection.');
        $this->addOption('find-depth-from', null, InputOption::VALUE_OPTIONAL, 'Restrict the entries files depth of traversing from.');
        $this->addOption('find-depth-to', null, InputOption::VALUE_OPTIONAL, 'Restrict the entries files depth of traversing to.');
        $this->addOption('find-date-from', null, InputOption::VALUE_OPTIONAL, 'Restrict the entries files by a date range from.');
        $this->addOption('find-date-to', null, InputOption::VALUE_OPTIONAL, 'Restrict the entries filesby a date range to.');
        $this->addOption('find-size-from', null, InputOption::VALUE_OPTIONAL, 'Restrict the entries files by a size range from.');
        $this->addOption('find-size-to', null, InputOption::VALUE_OPTIONAL, 'Restrict the entries files by a size range to.');
        $this->addOption('find-exclude', null, InputOption::VALUE_OPTIONAL, 'Exclude directories from matching.');
        $this->addOption('find-contains', null, InputOption::VALUE_OPTIONAL, 'Find entries files by content.');
        $this->addOption('find-not-contains', null, InputOption::VALUE_OPTIONAL, 'Find entries files by content excludes files containing given pattern.');
        $this->addOption('find-path', null, InputOption::VALUE_OPTIONAL, 'Find entries files and directories by path.');
        $this->addOption('find-sort-by-key', null, InputOption::VALUE_OPTIONAL, 'Sort the entries files and directories by the last accessed, changed or modified time. Values: atime, mtime, ctime.');
        $this->addOption('find-sort-by-direction', null, InputOption::VALUE_OPTIONAL, 'Sort the entries files and directories by direction. Order direction: DESC (descending) or ASC (ascending)');
        $this->addOption('filter-return', null, InputOption::VALUE_OPTIONAL, 'Return items. Valid values: all, first, last, next, random, shuffle');
        $this->addOption('filter-group-by', null, InputOption::VALUE_OPTIONAL, 'Group array collection by key.');
        $this->addOption('filter-offset', null, InputOption::VALUE_OPTIONAL, 'Extract a slice of the current array collection with specific offset.');
        $this->addOption('filter-limit', null, InputOption::VALUE_OPTIONAL, 'Extract a slice of the current array collection with offset 0 and specific length.');
        $this->addOption('filter-sort-by-key', null, InputOption::VALUE_OPTIONAL, 'Sort array collection by key.');
        $this->addOption('filter-sort-by-direction', null, InputOption::VALUE_OPTIONAL, 'Sort array collection by direction. Order direction: DESC (descending) or ASC (ascending)');
        $this->addOption('filter-where', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Filters the array collection fields by a given condition.');
        $this->addOption('path', null, InputOption::VALUE_OPTIONAL, 'Export path.');
        $this->addOption('filename', null, InputOption::VALUE_OPTIONAL, 'Export filename.');
        $this->addOption('extension', null, InputOption::VALUE_OPTIONAL, 'Export extension.');
        $this->addOption('serializer', null, InputOption::VALUE_OPTIONAL, 'Export serializer.');
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

        $input->getOption('find-exclude') and $options['find']['exclude']                        = $input->getOption('find-exclude');
        $input->getOption('find-contains') and $options['find']['contains']                      = $input->getOption('find-contains');
        $input->getOption('find-not-contains') and $options['find']['not_contains']              = $input->getOption('find-not-contains');
        $input->getOption('find-path') and $options['find']['path']                              = $input->getOption('find-path');
        $input->getOption('find-sort-by-key') and $options['find']['sort_by']['key']             = $input->getOption('find-sort-by-key');
        $input->getOption('find-sort-by-direction') and $options['find']['sort_by']['direction'] = $input->getOption('find-sort-by-direction');

        $input->getOption('filter-group-by') and $options['filter']['group_by'] = $input->getOption('filter-group-by');
        $input->getOption('filter-return') and $options['filter']['return']     = $input->getOption('filter-return');

        if ($input->getOption('filter-where')) {
            $filterWhere = $input->getOption('filter-where');

            $where = [];

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

        $innerData       = [];
        $innerDataString = '';

        $data = entries()->fetch($id, $options);

        if (count($data) <= 0) {
            if ($options['collection'] === true) {
                $output->write(
                    renderToString(
                        div(
                            'Entry [b]' . $id . '[/b] collection is empty.',
                            'color-danger px-2 py-1'
                        )
                    )
                );
            } else {
                $output->write(
                    renderToString(
                        div(
                            'Entry [b]' . $id . '[/b] doesn\'t exists.',
                            'color-danger px-2 py-1'
                        )
                    )
                );
            }

            return Command::FAILURE;
        }
    
        $exportPath = $input->getOption('export-path') ? $input->getOption('export-path') : registry()->get('flextype.settings.entries.export.path');
        $exportFilename = $input->getOption('export-filename') ? $input->getOption('export-filename') : registry()->get('flextype.settings.entries.export.filename');

        if ($exportFilename == '') {
            $exportFilename = 'export-' . time();
        }

        filesystem()->directory($exportPath)->ensureExists(0755, true);
        filesystem()->file($exportPath . '/' . $exportFilename . '.md')->put(serializers()->frontmatter()->encode($data->toArray()));

        return Command::SUCCESS;
    }
}
