<?php

namespace App\Command;

use App\Service\TakerManager;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TakeScreenshotCommand extends Command
{
    private $takerManager;

    protected static $defaultName = 'app:sshot';

    protected static $defaultDescription = 'Take a new screenshot from a website';

    public function __construct(TakerManager $taker)
    {
        $this->takerManager = $taker;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::REQUIRED,
                'The url of the website we which to take a screeshot.')
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL,
                'Set the output of the results from the render. The output can be either JSON (json) or the raw image (image) captured.',
                'image')
            ->addOption('width', null, InputOption::VALUE_OPTIONAL,
                'Viewport width in pixels of the browser render.', 1680)
            ->addOption('height', null, InputOption::VALUE_OPTIONAL,
                'Viewport height in pixels of the browser render.', 867)
            ->addOption('file_type', 'f', InputOption::VALUE_OPTIONAL,
                'File type for the file (If output is not set to json), the options include PNG, JPG, WebP, and PDF.',
                'png')
            ->addOption('lazy_load', 'l', InputOption::VALUE_NONE,
                'If lazy load is set to true, the browser will cross down the entire page to ensure all content is loaded in the render.')
            ->addOption('dark_mode', null, InputOption::VALUE_NONE,
                '(Undocumented) Ask the website to render the css style dark mode.')
            ->addOption('grayscale', 'g', InputOption::VALUE_OPTIONAL,
                '(Undocumented) Set the image to grayscale values, between 0 and 100.',
                0)
            ->addOption('delay', 'd', InputOption::VALUE_OPTIONAL,
                'Time delay in milliseconds (ms) before the screenshot is rendered from the browser instance (this includes PDFs).',
                0)
            ->addOption('user_agent', null, InputOption::VALUE_OPTIONAL,
                'Sets the User-Agent string for the render for a particular request.',
                '')
            ->addOption('full_page', null, InputOption::VALUE_NONE,
                'Capture the full page of a website vs. the scrollable area that is visible in the viewport upon render.')
            ->addOption('fail_on_error', null, InputOption::VALUE_NONE,
                'If fail on error is set to true, then the API will return an error if the render encounters a 4xx or 5xx status code.')
            ->addOption('clip_x', null, InputOption::VALUE_OPTIONAL,
                '(Undocumented) Select a part of the screenshot to create the image, from this coordinate x.')
            ->addOption('clip_y', null, InputOption::VALUE_OPTIONAL,
                '(Undocumented) Select a part of the screenshot to create the image, from this coordinate y.')
            ->addOption('clip_w', null, InputOption::VALUE_OPTIONAL,
                '(Undocumented) Select a part of the screenshot to create the image, from represent the width of the clip')
            ->addOption('clip_h', null, InputOption::VALUE_OPTIONAL,
                '(Undocumented) Select a part of the screenshot to create the image, from represent the height of the clip')
            ->setHelp('This command sent a request to take a screenshot of the website the user requested in URL');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $url = urlencode($input->getArgument('url'));
        $output->writeln("Taking a screenshot off website $url");

        // Get options fields from input to create an array with the values from cli
        // and clear standard options, this is not the best solution as the default
        // options may change.
        $options = $input->getOptions();
        unset($options['help']);
        unset($options['quiet']);
        unset($options['verbose']);
        unset($options['version']);
        unset($options['ansi']);
        unset($options['no-interaction']);
        unset($options['env']);
        unset($options['no-debug']);

        // Get the service for taking the screenshot where there would be some
        // validation from input.
        try {
            $response = $this->takerManager->take($url, $options);
            if ($response->success) {
                $output->write("Save screenshot at $response->filename ($response->size bytes)");
                $output->writeln(' - ' . $response->time->format('c'));
            } else {
                $output->writeln('Failed to execute command!');
                $output->writeln($response->errorMessage);
                if ($response->errorType === TakerManager::ERR_VALIDATION) {
                    return Command::INVALID;
                } else {
                    return Command::FAILURE;
                }
            }
        } catch (Exception $ex) {
            $output->writeln('Failed to execute command: ' . $ex->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
