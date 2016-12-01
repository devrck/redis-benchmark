<?php

namespace eMAG\RedisOptimization\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisIterativeGettingKeys extends AbstractRedisCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('benchmark:get:iterative')
            ->setDescription('Will loop through all the values and fetch it one by one.')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $string = $input->getOption('key');
        $this->runCommand('benchmark:set:pipeline', ['--key' => $string]);

        $response = [];
        $start = microtime(true);
        for ($i = 0; $i < $this->totalItems; $i++) {
            $response[] = $this->client->get(sprintf('%s%d', $string, $i));
            $this->progressBar->advance();
        }
        $end = microtime(true) - $start;

        $key = $this->getRandomKey();
        if ($response[$key] == $key) {
            $this->setFinishMessage(sprintf('<info>Iterative GET of <comment>%d</comment> keys took <comment>%fs</comment></info>', $this->totalItems, $end));
        } else {
            $this->setFinishMessage(sprintf('<error>Could not complete the operation correct!</error>'));
        }
    }

    /**
     * @inheritDoc
     */
    protected function getOptionKeyName()
    {
        return 'rigk';
    }

    /**
     * @inheritDoc
     */
    public function getStartMessage()
    {
        return 'Started iterative GET test';
    }
}