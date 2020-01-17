<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\AheadworksBlogSeoCompatibility\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MageWorx\AheadworksBlogSeoCompatibility\Model\Provider;

/**
 * Class AddBlogPages
 *
 */
class AddBlogPagesObserver implements ObserverInterface
{
    /**
     * @var Provider
     */
    protected $provider;

    /**
     * AddBlogPagesObserver constructor.
     *
     * @param Provider $provider
     */
    public function __construct(
        Provider $provider
    ) {
        $this->provider = $provider;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $storeId = $observer->getData('storeId');

        if (!$storeId) {
            return;
        }

        $excludeMetaRobots = $this->getExcludeMetaRobots($observer);

        $container                    = $observer->getEvent()->getContainer();
        $providers                    = $container->getGenerators();
        $providers['aheadworks_blog'] = $this->provider->getData($storeId, $excludeMetaRobots);

        $container->setGenerators($providers);
    }

    /**
     * @param Observer $observer
     * @return array
     */
    protected function getExcludeMetaRobots($observer)
    {
        $metaRobots = $observer->getData('exclude_meta_robots');
        $metaRobots = is_array($metaRobots) ? $metaRobots : [$metaRobots];

        foreach ($metaRobots as $key => $item) {
            $metaRobots[$key] = str_replace(' ', '', strtoupper($item));
        }

        return $metaRobots;
    }
}