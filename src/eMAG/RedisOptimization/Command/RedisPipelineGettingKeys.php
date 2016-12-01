<?php

namespace eMAG\RedisOptimization\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisPipelineGettingKeys extends AbstractRedisCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('benchmark:get:pipeline')
            ->setDescription('In a loop will get a key in a pipeline and then execute it against redis server and get all the values.')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $string = $input->getOption('key');
        $this->runCommand('benchmark:set:pipeline', ['--key' => $string]);

        $string = $input->getOption('key');
        $start = microtime(true);
        $pipe = $this->client->pipeline();
        for ($i = 0; $i < $this->totalItems; $i++) {
            $pipe->get(sprintf('%s%d', $string, $i));
            $this->progressBar->advance();
        }
        $response = $pipe->execute();
        $end = microtime(true) - $start;

        $key = $this->getRandomKey();
        if ($response[$key] == $key) {
            $this->setFinishMessage(sprintf('<info>Pipeline GET of <comment>%d</comment> keys took <comment>%fs</comment></info>', $this->totalItems, $end));
        } else {
            $this->setFinishMessage(sprintf('<error>Could not complete the operation correct!</error>'));
        }
        $pipe = null;
    }

    /**
     * @inheritDoc
     */
    protected function getOptionKeyName()
    {
        return 'rpgk';
    }

    /**
     * @inheritDoc
     */
    public function getStartMessage()
    {
        return 'Started pipeline GET test';
    }
}