<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package
 * @copyright   Copyright (c) 2017 E-CONOMIX GmbH (http://www.e-conomix.at)
 */

namespace Economix\DbTranslations\Api;

/**
 * Interface TranslateInterface
 * @package Economix\DbTranslations\Api
 * @api
 */
interface TranslateInterface
{

    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const KEY_ID          = 'key_id';
    const STORE_ID              = 'store_id';
    const LOCALE          = 'locale';
    const TRANSLATE         = 'translate';
    const STRING      = 'string';

    /**
     * @return int|null
     */
    public function getKeyId();

    /**
     * @return int|null
     */
    public function getStoreId();

    /**
     * @return string|null
     */
    public function getLocale();
    /**
     * @return string|null
     */
    public function getTranslate();
    /**
     * @return string|null
     */
    public function getString();

    /**
     * @param int $keyId
     * @return TranslateInterface
     */
    public function setKeyId($keyId);
    /**
     * @param int $storeId
     * @return TranslateInterface
     */
    public function setStoreId($storeId);
    /**
     * @param string $locale
     * @return TranslateInterface
     */
    public function setLocale($locale);
    /**
     * @param string $translate
     * @return TranslateInterface
     */
    public function setTranslate($translate);
    /**
     * @param string $string
     * @return TranslateInterface
     */
    public function setString($string);
}