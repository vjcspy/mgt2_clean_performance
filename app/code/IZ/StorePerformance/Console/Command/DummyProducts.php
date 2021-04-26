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

class DummyProducts extends Command
{

    /**
     * @var \IZ\StorePerformance\Helper\Product
     */
    private $productHelper;

    public function __construct(
        \IZ\StorePerformance\Helper\Product $productHelper,
        string $name = null)
    {
        parent::__construct($name);
        $this->productHelper = $productHelper;
    }

    const NUMBER_PRODUCT_OPTION = "number-of-products";

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    )
    {
        $loop = (int)$input->getOption(self::NUMBER_PRODUCT_OPTION);
        $times = $this->productHelper->dummyProduct($loop);

        $output->writeln("This process used " . '<info>' . (int)($this->productHelper->rutime($times[1], $times[0], "utime") / $loop) . '</info>' .
            " ms for its computations");
        $output->writeln("It spent " . '<info>' . (int)($this->productHelper->rutime($times[1], $times[0], "stime") / $loop) . '</info>' .
            " ms in system calls\n");
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("iz:p:p");
        $this->setDescription("Test create product performance");
        $this->setDefinition([
            new InputOption(self::NUMBER_PRODUCT_OPTION, "-N", InputOption::VALUE_REQUIRED, "Number of products", 1)
        ]);
        parent::configure();
    }
}

