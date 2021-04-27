<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace IZ\StorePerformance\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DummyStores extends Command
{

    /**
     * @var \IZ\StorePerformance\Helper\Store
     */
    private $storeHelper;

    public function __construct(
        \IZ\StorePerformance\Helper\Store $storeHelper,
        string $name = null)
    {
        parent::__construct($name);
        $this->storeHelper = $storeHelper;
    }

    const NUMBER_STORE_OPTION = "number-of-store";

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    )
    {
        $loop = (int)$input->getOption(self::NUMBER_STORE_OPTION);
        $times = $this->storeHelper->dummyStore($loop);

        $output->writeln("This process used " . '<info>' . (int)($this->storeHelper->runTime($times[0], $times[1]) / $loop) . '</info>' .
            " seconds for its computations per entity");
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("iz:p:s");
        $this->setDescription("Test create store performance");
        $this->setDefinition([
            new InputOption(self::NUMBER_STORE_OPTION, "-N", InputOption::VALUE_REQUIRED, self::NUMBER_STORE_OPTION, 1)
        ]);
        parent::configure();
    }
}

