<?php
namespace Economix\DbTranslations\Controller\Adminhtml\Dbtranslations;

use Economix\DbTranslations\Model\Translate;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $translationId) {
            $rowData = $postItems[$translationId];
            if (is_array($rowData) && isset($rowData['translation_locale'])) {
                $rowData['locale'] = $rowData['translation_locale'];
                unset($rowData['translation_locale']);
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add translation id to error message
     *
     * @param Translate $translate
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithTranslationId(Translate $translate, $errorText)
    {
        return '[Translation ID: ' . $translate->getId() . '] ' . $errorText;
    }
}
