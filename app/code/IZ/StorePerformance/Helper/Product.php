<?php


namespace IZ\StorePerformance\Helper;


use Magento\Store\Model\WebsiteFactory;

class Product extends \IZ\StorePerformance\Helper\Data
{

    /**
     * @var WebsiteFactory
     */
    private $websiteFactory;
    /**
     * @var \Magento\Store\Model\Website
     */
    private $_website;
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    public function __construct(
        WebsiteFactory $websiteFactory,
        \Magento\Framework\App\State $state
    )
    {
        $this->websiteFactory = $websiteFactory;
        $this->state = $state;
    }

    protected function _beforeDummyProduct()
    {
        if (is_null($this->_website)) {
            try {
                $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            } catch (\Exception $e) {

            }
            $this->_website = $this->websiteFactory->create();
            $this->_website->load('base');

            if (!$this->_website->getId()) {
                throw new \Exception("could not found base website");
            }
        }
    }

    public function dummyProduct($num = 1)
    {
        $this->_beforeDummyProduct();
        $rustart = microtime(true);

        for ($i = 0; $i < $num; $i++) {
            $code = $this->generateRandomString(10);
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
            $product = $objectManager->create('\Magento\Catalog\Model\Product');
            $product->setSku($code); // Set your sku here
            $product->setName($code); // Name of Product
            $product->setAttributeSetId(4); // Attribute set id
            $product->setStatus(1); // Status on product enabled/ disabled 1/0
            $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
            $product->setTaxClassId(0); // Tax class id
            $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
            $product->setPrice(1); // price of product
            $product->setWebsiteIds([$this->_website->getId(),
            ]);
            $product->setStockData(
                array(
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 0,
                    'is_in_stock' => 1,
                    'qty' => 0
                )
            );
            $product->save();
        }

        $ru = microtime(true);

        return [$rustart, $ru];
    }
}