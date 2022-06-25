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

namespace Flextype\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use function Thermage\breakline;
use function Thermage\anchor;
use function Thermage\div;
use function Thermage\span;
use function Thermage\hr;
use function Thermage\renderToString;

class AboutCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('about');
        $this->setDescription('Get information about Flextype.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->write(
            renderToString(
           
                    hr('[b]Flextype[/b]', 'my-1') . 
                    div('[b][color=success]Version[/color][/b]: ' . registry()->get('flextype.manifest.version'), '') .
                    div('[b][color=success]Author[/color][/b]', '') .
                    div('[b][color=success]  Name[/color][/b]: ' . registry()->get('flextype.manifest.author.name'), '') .
                    div('[b][color=success]  Email[/color][/b]: ' . registry()->get('flextype.manifest.author.email'), '') .
                    div('[b][color=success]  Url[/color][/b]: ' . anchor(registry()->get('flextype.manifest.author.url'))->href(registry()->get('flextype.manifest.author.url')), 'clearfix') . breakline() .

                    hr('[b]Plugins[/b]', 'my-1') . 
                    div('[b][color=success]Enabled[/color][/b]: ' . implode(', ', array_keys(registry()->get('plugins'))), '') .

                    hr('[b]Constants[/b]', 'my-1') . 
                    div('[b][color=success]PROJECT_NAME[/color][/b]: ' . PROJECT_NAME, '') .
                    div('[b][color=success]ROOT_DIR[/color][/b]: ' . ROOT_DIR, '') .
                    div('[b][color=success]PATH_PROJECT[/color][/b]: ' . PATH_PROJECT, '') .
                    div('[b][color=success]PATH_TMP[/color][/b]: ' . PATH_TMP, '') .
                    div('[b][color=success]FLEXTYPE_MINIMUM_PHP[/color][/b]: ' . FLEXTYPE_MINIMUM_PHP, '') .

                    hr('[b]PHP Information[/b]', 'my-1') .
                    div('[b][color=success]PHP Version[/color][/b]: ' . phpversion(), '') .
                    div('[b][color=success]PHP Modules[/color][/b]: ' . implode(', ', get_loaded_extensions()), '')
                    
                    . PHP_EOL
           
            )
        );

        return Command::SUCCESS;
    }
}