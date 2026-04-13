<?php
namespace Economix\DbTranslations\Controller\Adminhtml\Dbtranslations;

use Economix\DbTranslations\Model\Translate;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Economix\DbTranslations\Model\ResourceModel\Translation\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var string
     */
    protected $successMessage;
    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param string $successMessage
     * @param string $errorMessage
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        $successMessage = '',
        $errorMessage = ''
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->successMessage    = $successMessage;
        $this->errorMessage      = $errorMessage;
        parent::__construct($context);
    }

    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
            foreach ($collection as $translation) {
                /** @var Translate $translation */
                $translation->delete();
            }
            $this->messageManager->addSuccessMessage(__($this->successMessage, $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __($this->errorMessage));
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('ecx_dbtranslations/*/index');
        return $redirectResult;
    }
}
