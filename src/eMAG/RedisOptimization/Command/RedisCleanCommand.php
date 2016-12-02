<?php

namespace eMAG\RedisOptimization\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RedisCleanCommand extends AbstractRedisCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('clean')
            ->addOption('batch-size', null, InputOption::VALUE_OPTIONAL, 'Batch size iterations to clear', 100000)
            ->addOption('single-key', null, InputOption::VALUE_NONE, 'A flag to set if there is just a single key to clear a not a batch')
            ->setDescription('Will remove the keys from a redis server in batches')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function doExecute (InputInterface $input, OutputInterface $output)
    {
        $string = $input->getOption('key');
        if ($input->getOption('single-key')) {
            $this->client->del($string);

            return;
        }
        $batchSize = $input->getOption('batch-size');
        $batch = 0;
        $items = [];
        $start = microtime(true);
        for ($i = 0; $i < $this->totalItems; $i++, $batch++) {
            $items[sprintf('%s%d', $string, $i)] = sprintf('%s%d', $string, $i);
            if ($batch > $batchSize) {
                $this->progressBar->advance($batchSize);
                $this->client->del($items);
                $items = [];
                $batch = 0;
            }
        }
        $this->client->del($items);
        $this->setFinishMessage(sprintf('<info>Total clean time for <comment>%d</comment> keys (batched in <comment>%d</comment> chunks) took <comment>%fs</comment></info>', $this->totalItems, $batchSize, (microtime(true) - $start)));
    }

    /**
     * @return  string
     */
    public function getStartMessage()
    {
        return 'Clean redis keys';
    }

    /**
     * @return  string
     */
    protected function getOptionKeyName()
    {
        return 'risk';
    }
}