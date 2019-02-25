<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package
 * @copyright   Copyright (c) 2017 E-CONOMIX GmbH (http://www.e-conomix.at)
 */

namespace Economix\DbTranslations\Model;


use Economix\DbTranslations\Api\TranslateInterface;
use Magento\Framework\Model\AbstractModel;

class Translate extends AbstractModel implements TranslateInterface
{
    protected $_eventPrefix = 'ecx_translate';

    protected function _construct()
    {
        $this->_init(\Magento\Backend\Model\ResourceModel\Translate::class);
    }

    /**
     * @return int|null
     */
    public function getKeyId()
    {
        return $this->getData(TranslateInterface::KEY_ID);
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->getData(TranslateInterface::STORE_ID);
    }

    /**
     * @return string|null
     */
    public function getLocale()
    {
        return $this->getData(TranslateInterface::LOCALE);
    }

    /**
     * @return string|null
     */
    public function getTranslate()
    {
        return $this->getData(TranslateInterface::TRANSLATE);
    }

    /**
     * @return string|null
     */
    public function getString()
    {
        return $this->getData(TranslateInterface::STRING);
    }

    /**
     * @param int $keyId
     * @return TranslateInterface
     */
    public function setKeyId($keyId)
    {
        return $this->setData(TranslateInterface::KEY_ID, $keyId);
    }

    /**
     * @param int $storeId
     * @return TranslateInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(TranslateInterface::STORE_ID, $storeId);
    }

    /**
     * @param string $locale
     * @return TranslateInterface
     */
    public function setLocale($locale)
    {
        return $this->setData(TranslateInterface::LOCALE, $locale);
    }

    /**
     * @param string $translate
     * @return TranslateInterface
     */
    public function setTranslate($translate)
    {
        return $this->setData(TranslateInterface::TRANSLATE, $translate);
    }

    /**
     * @param string $string
     * @return TranslateInterface
     */
    public function setString($string)
    {
        return $this->setData(TranslateInterface::STRING, $string);
    }

    public function beforeSave()
    {
        // update crc to current corresponding translte
        $this->setData('crc', crc32($this->getTranslate()));
        return parent::beforeSave();
    }


}