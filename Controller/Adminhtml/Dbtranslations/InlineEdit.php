<?php
declare(strict_types=1);

namespace Economix\DbTranslations\Controller\Adminhtml\Dbtranslations;

use Economix\DbTranslations\Api\TranslateInterface;
use Economix\DbTranslations\Model\Translate;
use Economix\DbTranslations\Model\TranslateFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class InlineEdit extends Action
{
    /**
     * Authorization level of a basic admin session.
     */
    public const ADMIN_RESOURCE = 'Magento_Backend::admin';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \Economix\DbTranslations\Model\TranslateFactory
     */
    private $translateFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Economix\DbTranslations\Model\TranslateFactory $translateFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        TranslateFactory $translateFactory,
        DataObjectHelper $dataObjectHelper,
        LoggerInterface $logger
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->translateFactory = $translateFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $messages = [];
        $error = false;

        $postItems = $this->getRequest()->getParam('items', []);
        if (!$this->getRequest()->getParam('isAjax') || !is_array($postItems) || $postItems === []) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach ($postItems as $translationId => $rowData) {
            $translation = $this->translateFactory->create();
            try {
                $translation->load((int) $translationId);
                if (!$translation->getId()) {
                    $messages[] = (string) __(
                        '[Translation ID: %1] Translation not found.',
                        $translationId
                    );
                    $error = true;
                    continue;
                }

                $rowData = $this->normalizeRowData((array) $rowData);
                $this->dataObjectHelper->populateWithArray(
                    $translation,
                    $rowData,
                    TranslateInterface::class
                );
                $translation->save();
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithTranslationId($translation, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $messages[] = $this->getErrorWithTranslationId(
                    $translation,
                    (string) __('Something went wrong while saving the translation.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error,
        ]);
    }

    /**
     * Map admin-safe `translation_locale` field back to the DB column `locale`.
     *
     * The listing exposes the locale under `translation_locale` because a
     * top-level `locale` request parameter would switch the adminhtml UI
     * locale via the backend locale resolver.
     *
     * @param array $rowData
     * @return array
     */
    private function normalizeRowData(array $rowData): array
    {
        if (array_key_exists('translation_locale', $rowData)) {
            $rowData[TranslateInterface::LOCALE] = $rowData['translation_locale'];
            unset($rowData['translation_locale']);
        }
        return $rowData;
    }

    /**
     * Add translation id to error message.
     *
     * @param \Economix\DbTranslations\Model\Translate $translate
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithTranslationId(Translate $translate, string $errorText): string
    {
        return '[Translation ID: ' . ($translate->getId() ?: '-') . '] ' . $errorText;
    }
}
