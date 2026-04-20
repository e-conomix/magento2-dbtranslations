<?php
/**
 * NOTICE OF LICENSE & COPYRIGHT
 *
 * Copyright (C) 2026 by MSTAGE GmbH - All Rights Reserved
 * Unauthorized copying or editing of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 *
 * @copyright 2026 MSTAGE GmbH
 * @author Benjamin Rosenberger <benjamin.rosenberger@mstage.at>
 */
declare(strict_types=1);

namespace Economix\DbTranslations\Plugin\Translation;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Translation\App\Config\Type\Translation;
use Magento\Translation\Model\ResourceModel\Translate;

/**
 * Prevents DB-backed inline translations from loading in adminhtml while keeping
 * compiled i18n config (same non-DB branch as {@see Translate::getTranslationArray}).
 */
class TranslateResourcePlugin
{
    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Framework\App\Config
     */
    private $appConfig;

    /**
     * @var \Magento\Framework\App\ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\App\Config $appConfig
     * @param \Magento\Framework\App\ScopeResolverInterface $scopeResolver
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        State $appState,
        Config $appConfig,
        ScopeResolverInterface $scopeResolver,
        StoreManagerInterface $storeManager
    ) {
        $this->appState = $appState;
        $this->appConfig = $appConfig;
        $this->scopeResolver = $scopeResolver;
        $this->storeManager = $storeManager;
    }

    /**
     * Skip DB translation merge in adminhtml.
     *
     * @param \Magento\Translation\Model\ResourceModel\Translate $subject
     * @param callable $proceed
     * @param int|null $storeId
     * @param string|null $locale
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetTranslationArray(
        Translate $subject,
        callable $proceed,
        $storeId = null,
        $locale = null
    ): array {
        try {
            if ($this->appState->getAreaCode() !== Area::AREA_ADMINHTML) {
                return $proceed($storeId, $locale);
            }
        } catch (LocalizedException $exception) {
            return $proceed($storeId, $locale);
        }

        if ($storeId === null) {
            $storeId = (int) $this->storeManager->getStore()->getId();
        } else {
            $storeId = (int) $storeId;
        }
        $locale = (string) $locale;
        $storeCode = $this->scopeResolver->getScope($storeId)->getCode();

        $data = $this->appConfig->get(
            Translation::CONFIG_TYPE,
            $locale . '/' . $storeCode,
            []
        );

        return is_array($data) ? $data : [];
    }
}
