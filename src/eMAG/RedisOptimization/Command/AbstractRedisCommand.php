<?php

namespace eMAG\RedisOptimization\Command;

use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractRedisCommand extends Command
{
    /**
     * @var int
     */
    protected $totalItems;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ProgressBar
     */
    protected $progressBar;

    /**
     * @var string
     */
    protected $finishMessage;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    protected function configure()
    {
        $this
            ->addOption('iterations', 'i', InputOption::VALUE_OPTIONAL, 'Maximum iterations', 100000)
            ->addOption('key', 'k', InputOption::VALUE_OPTIONAL, 'Set the name of the key to use while testing', $this->getOptionKeyName())
            ->addOption('auto-clean', null, InputOption::VALUE_NONE, 'Clean-up after yourself')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->totalItems = (int) $input->getOption('iterations');
        $this->client = $this->getApplication()->getClient();
        $this->progressBar = new ProgressBar($output, $this->totalItems);
        $this->progressBar->setFormat('<comment>[%bar%]</comment> <info>%percent:5s%% %memory:6s%</info>');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->getFormatter()->setStyle('delimiter', new OutputFormatterStyle('white', 'green', ['bold']));

        $output->writeln(sprintf('<delimiter>%s %s %s</delimiter>', $this->getDelimiter(), $this->getName(), $this->getDelimiter()));
        $output->writeln(sprintf('<info>%s</info>', $this->getStartMessage()));

        $this->progressBar->start();
        $this->progressBar->setRedrawFrequency(($this->totalItems * 0.01) < 0 ? 1 : $this->totalItems * 0.01);
        $this->doExecute($input, $output);
        $this->progressBar->finish();

        $output->writeln('');
        $output->writeln($this->finishMessage);
        if ($input->getOption('auto-clean') && $this->getName() != 'clean') {
            $output->writeln(sprintf('<comment>Running auto-clean keys...</comment>'));
            $this->runCommand('clean', [
                '--key' => $input->getOption('key'),
                '--single-key' => !$this->isMultiKey(),
            ]);
        }

        $output->writeln(sprintf('<delimiter>%s %s %s</delimiter>', $this->getDelimiter(), $this->getName(), $this->getDelimiter()));
    }

    /**
     * @param   string  $string
     *
     * @return  $this
     */
    public function setFinishMessage($string)
    {
        $this->finishMessage = $string;

        return $this;
    }

    /**
     * @return  string
     */
    protected function getDelimiter ()
    {
        return str_repeat('#', 20);
    }

    /**
     * @param   string  $name
     * @param   array   $parameters
     *
     * @return  int
     */
    protected function runCommand ($name, array $parameters = [])
    {
        return $this->getApplication()->find($name)->run(new ArrayInput(array_merge([
            'command' => $name,
            '--iterations' => $this->totalItems,
            '--key' => $this->getOptionKeyName(),
        ], $parameters)), new NullOutput());
    }

    /**
     * @return  bool
     */
    protected function isMultiKey()
    {
        return true;
    }

    /**
     * @return  int
     */
    protected function getRandomKey()
    {
        return rand(0, $this->totalItems - 1);
    }

    /**
     * @param   InputInterface $input
     * @param   OutputInterface $output
     *
     * @return  int
     */
    abstract protected function doExecute (InputInterface $input, OutputInterface $output);

    /**
     * @return  string
     */
    abstract protected function getOptionKeyName();

    /**
     * @return  string
     */
    abstract public function getStartMessage();
}