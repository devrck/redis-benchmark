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
            ->setDescription('Will remove the keys from a redis server in batches')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function doExecute (InputInterface $input, OutputInterface $output)
    {
        $batchSize = $input->getOption('batch-size');
        $string = $input->getOption('key');
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