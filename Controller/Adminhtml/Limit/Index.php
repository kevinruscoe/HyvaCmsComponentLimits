<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Controller\Adminhtml\Limit;

use KevinRuscoe\HyvaCmsComponentLimits\Controller\Adminhtml\Limit;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Index extends Limit implements HttpGetActionInterface
{
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magento_Config::system_config');
        $resultPage->getConfig()->getTitle()->prepend(__('Component Limitations'));

        return $resultPage;
    }
}
