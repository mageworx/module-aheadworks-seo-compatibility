<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\AheadworksBlogSeoCompatibility\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * XML config path show Aheadworks Blog Pages
     */
    const XML_PATH_ADD_BLOG_PAGES = 'mageworx_seo/xml_sitemap/xml_sitemap_aheadworks_blog/add_blog_pages';

    /**
     * XML config path add post pages
     */
    const XML_PATH_ADD_POSTS = 'mageworx_seo/xml_sitemap/xml_sitemap_aheadworks_blog/add_posts';

    /**
     * XML config path add categories pages
     */
    const XML_PATH_ADD_CATEGORIES = 'mageworx_seo/xml_sitemap/xml_sitemap_aheadworks_blog/add_categories';

    /**
     * XML config path add static pages
     */
    const XML_PATH_ADD_STATIC_PAGES = 'mageworx_seo/xml_sitemap/xml_sitemap_aheadworks_blog/add_static_pages';

    /**
     * XML config path add author pages
     */
    const XML_PATH_PRIORITY = 'mageworx_seo/xml_sitemap/xml_sitemap_aheadworks_blog/blog_pages_priority';

    /**
     * XML config path add author pages
     */
    const XML_PATH_CHANGEFREQ = 'mageworx_seo/xml_sitemap/xml_sitemap_aheadworks_blog/blog_pages_changefreq';

    /**
     * @var int
     */
    protected $storeId = Store::DEFAULT_STORE_ID;

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;

        return $this;
    }

    /**
     * Check if add blog pages enabled
     *
     * @return bool
     */
    public function isBlogPagesEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ADD_BLOG_PAGES,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Check if add post pages enabled
     *
     * @return bool
     */
    public function isAddPosts()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ADD_POSTS,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }


    /**
     * Check if add categories pages enabled
     *
     * @return bool
     */
    public function isAddCategories()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ADD_CATEGORIES,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Check if add tag pages enabled
     *
     * @return bool
     */
    public function isAddStaticPages()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ADD_STATIC_PAGES,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get Frequency for blog pages
     *
     * @return string
     */
    public function getFrequency()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CHANGEFREQ,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get priority for blog pages
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRIORITY,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }
}
