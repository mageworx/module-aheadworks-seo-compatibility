<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\AheadworksBlogSeoCompatibility\Model;

use Aheadworks\Blog\Model\Sitemap\ItemsProvider\ProviderInterface;
use Magento\Framework\App\ProductMetadataInterface;
use MageWorx\AheadworksBlogSeoCompatibility\Helper\Data;
use Psr\Log\LoggerInterface;
use Zend\Validator\Sitemap\Lastmod as LastmodValidator;

/**
 * Class Abstract DataProvider
 */
class Provider extends \Aheadworks\Blog\Model\Sitemap\ItemsProviderComposite
{
    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ProviderInterface[]
     */
    protected $providers;

    /**
     * @var LastmodValidator
     */
    protected $lastmodValidator;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Provider constructor.
     *
     * @param ProductMetadataInterface $productMetadata
     * @param LoggerInterface $logger
     * @param Data $helper
     * @param array $providers
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        LoggerInterface $logger,
        Data $helper,
        LastmodValidator $lastmodValidator,
        array $providers = []
    ) {
        $this->productMetadata  = $productMetadata;
        $this->logger           = $logger;
        $this->helper           = $helper;
        $this->providers        = $providers;
        $this->lastmodValidator = $lastmodValidator;
    }

    /**
     * @param int $storeId
     * @param array $excludeMetaRobots
     * @return array
     */
    public function getData($storeId, array $excludeMetaRobots)
    {
        if ($this->helper->isBlogPagesEnabled()) {
            $this->helper->setStoreId($storeId);

            $items = $this->getItems($storeId);

            return $this->convertData($items);
        }

        return [];
    }

    /**
     * Retrieve sitemap items
     *
     * @param int $storeId
     * @return array
     */
    public function getItems($storeId)
    {
        $items  = [];
        $method = $this->resolveMethod();
        foreach ($this->providers as $providerCode => $provider) {

            if (!$this->isAddPages($providerCode)) {
                continue;
            }

            if ($provider instanceof ProviderInterface) {
                $items = array_merge($items, $provider->{$method}($storeId));
            } elseif (is_object($provider)) {
                $this->logger->warning(__('%1 doesn\'t implement %2', get_class($provider), ProviderInterface::class));
            } else {
                $this->logger->warning(__('Given provider doesn\'t implement %1', ProviderInterface::class));
            }
        }

        return $items;
    }

    /**
     * @param \Magento\Framework\DataObject[] $items
     * @return array
     */
    protected function convertData($items)
    {
        $itemsData = [];

        /** @var \Magento\Framework\DataObject $entityWrapper */
        foreach ($items as $item) {

            /** @var \Magento\Framework\DataObject $item */
            if ($item->getUrl()) {

                if ($item->getUpdatedAt() && !$this->lastmodValidator->isValid($item->getUpdatedAt())) {
                    $date = date('Y-m-d', strtotime($item->getUpdatedAt()));
                } else {
                    $date = $item->getUpdatedAt();
                }

                $itemsData[] = [
                    'url_key'      => $item->getUrl(),
                    'date_changed' => $date
                ];
            }
        }

        return [
            'title'      => __('Blog Pages'),
            'changefreq' => $this->helper->getFrequency(),
            'priority'   => $this->helper->getPriority(),
            'items'      => $itemsData
        ];
    }

    /**
     * Resolve provider method
     *
     * @return string
     */
    private function resolveMethod()
    {
        return version_compare($this->productMetadata->getVersion(), '2.3', '>=')
            ? 'getItems23x'
            : 'getItems';
    }

    /**
     * @param string $identifier
     * @return bool
     */
    protected function isAddPages($identifier)
    {
        switch ($identifier) {
            case 'post':
                $result = $this->helper->isAddPosts();
                break;
            case 'category':
                $result = $this->helper->isAddCategories();
                break;
            case 'static_pages':
                $result = $this->helper->isAddStaticPages();
                break;
            case 'author':
            default:
                $result = true;
                break;
        }

        return $result;
    }
}