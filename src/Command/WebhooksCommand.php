<?php

namespace CodeCloud\Bundle\ShopifyBundle\Command;

use CodeCloud\Bundle\ShopifyBundle\Exception\StoreNotFoundException;
use CodeCloud\Bundle\ShopifyBundle\Model\ShopifyStoreManagerInterface;
use CodeCloud\Bundle\ShopifyBundle\Service\WebhookCreatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WebhooksCommand extends Command
{
    /**
     * @var WebhookCreatorInterface
     */
    private $webhookCreator;

    /**
     * @var ShopifyStoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array
     */
    private $topics = [];

    /**
     * @param WebhookCreatorInterface $webhookCreator
     * @param ShopifyStoreManagerInterface $storeManager
     * @param array $topics
     */
    public function __construct(WebhookCreatorInterface $webhookCreator, ShopifyStoreManagerInterface $storeManager, array $topics)
    {
        $this->webhookCreator = $webhookCreator;
        $this->storeManager = $storeManager;
        $this->topics = $topics;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('codecloud:shopify:webhooks')
            ->setDescription('Interact with Shopify Webhooks')
            ->addArgument('store', InputArgument::REQUIRED, 'The store to install webhooks in')
            ->addOption('delete', null, InputOption::VALUE_NONE, 'Delete existing webhooks')
            ->addOption('list', null, InputOption::VALUE_NONE, 'List existing webhooks')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->storeManager->storeExists($store = $input->getArgument('store'))) {
            throw new StoreNotFoundException($store);
        }

        if ($input->getOption('list')) {
            $output->writeln(print_r($this->webhookCreator->listWebhooks($store), true));

            return 0;
        }

        if ($input->getOption('delete')) {
            $this->webhookCreator->deleteAllWebhooks($store);
            $output->writeln('Webhooks deleted');

            return 0;
        }

        if (empty($this->topics)) {
            throw new \LogicException('No webhook topics configured');
        }

        $this->webhookCreator->createWebhooks($store, $this->topics);

        $output->writeln('Webhooks created');

        return 0;
    }
}
