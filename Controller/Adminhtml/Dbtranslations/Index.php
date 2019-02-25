<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @package
 * @copyright   Copyright (c) 2017 E-CONOMIX GmbH (http://www.e-conomix.at)
 */

namespace Economix\DbTranslations\Controller\Adminhtml\Dbtranslations;


use Magento\Backend\App\Action;

class Index extends Action
{

    protected $resultPageFactory = false;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $this->messageManager->addErrorMessage(__('These translations should not be used if not absolutely necessary! Create translations within module, language pack or theme instead.'));
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Db Translations')));
        return $resultPage;
    }
}