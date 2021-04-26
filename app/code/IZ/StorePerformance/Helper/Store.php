<?php


namespace IZ\StorePerformance\Helper;


use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\Group;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\Website;
use Magento\Store\Model\WebsiteFactory;

class Store extends \IZ\StorePerformance\Helper\Data
{

    /**
     * @var WebsiteFactory
     */
    private $websiteFactory;
    /**
     * @var Website
     */
    private $websiteResourceModel;
    /**
     * @var StoreFactory
     */
    private $storeFactory;
    /**
     * @var GroupFactory
     */
    private $groupFactory;
    /**
     * @var Group
     */
    private $groupResourceModel;
    /**
     * @var Store
     */
    private $storeResourceModel;
    /**
     * @var ManagerInterface
     */
    private $eventManager;

    public function __construct(
        WebsiteFactory $websiteFactory,
        Website $websiteResourceModel,
        Group $groupResourceModel,
        StoreFactory $storeFactory,
        GroupFactory $groupFactory,
        ManagerInterface $eventManager
    )
    {
        $this->websiteFactory = $websiteFactory;
        $this->websiteResourceModel = $websiteResourceModel;
        $this->storeFactory = $storeFactory;
        $this->groupFactory = $groupFactory;
        $this->groupResourceModel = $groupResourceModel;
        $this->eventManager = $eventManager;
    }

    public function dummyStore($numberOfStore = 1)
    {
        $updateFirst = false;
        /** @var \Magento\Store\Model\Website $website */
        $website = $this->websiteFactory->create();
        $website->load('base');
        if (!$website->getId()) {
            $code = $this->generateRandomString();
            $website->setCode($code);
            $website->setName($code);
            $website->save();

            $group = $this->groupFactory->create();
            $group->setWebsiteId($website->getWebsiteId());
            $group->setName($code);
            $group->save();
            $website->setDefaultGroupId($group->getId());
        } else {
            $group = $website->getDefaultGroup();
            $updateFirst = true;
        }

        for ($i = 0; $i < $numberOfStore; $i++) {
            $code = 's' . $this->generateRandomString(8);
            try {
                $newStoreId = $this->creteStore($website, $group, [
                    'code' => $code,
                    'name' => $code
                ]);
                if (!$updateFirst) {
                    $group->setDefaultStoreId($newStoreId)->save();
                    $updateFirst = true;
                }
            } catch (\Exception $e) {
                $a = 1;
            }
        }

    }

    protected function creteStore(\Magento\Store\Model\Website $website, \Magento\Store\Model\Group $group, array $storeData)
    {
        if (!isset($storeData['code']) || !isset($storeData['name'])) {
            throw  new \Exception("please define store data");
        }

        /** @var  \Magento\Store\Model\Store $store */
        $store = $this->storeFactory->create();
        if (is_null($store->getData('store_id'))) {
            $store->setCode($storeData['code']);
            $store->setName($storeData['name']);
            $store->setWebsite($website);
            $store->setGroupId($group->getId());
            $store->setData('is_active', $storeData['is_active'] ?? 1);
            $store->save();
            // Trigger event to insert some data to the sales_sequence_meta table (fix bug place order in checkout)
//            $this->eventManager->dispatch('store_add', ['store' => $store]);
        }

        return $store->getId();
    }

}