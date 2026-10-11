<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Controller\Adminhtml\Limit;

use KevinRuscoe\HyvaCmsComponentLimits\Controller\Adminhtml\Limit;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends Limit implements HttpGetActionInterface
{
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_FORWARD)->forward('edit');
    }
}
