<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Controller\Adminhtml\Limit;

use KevinRuscoe\HyvaCmsComponentLimits\Controller\Adminhtml\Limit;
use KevinRuscoe\HyvaCmsComponentLimits\Model\LimitFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\ConditionInterface;

/**
 * Returns the HTML for a condition row when "+" is clicked in the conditions builder
 */
class NewConditionHtml extends Limit implements HttpPostActionInterface, HttpGetActionInterface
{
    private LimitFactory $limitFactory;

    public function __construct(Context $context, LimitFactory $limitFactory)
    {
        parent::__construct($context);
        $this->limitFactory = $limitFactory;
    }

    public function execute()
    {
        $types = explode('|', str_replace('-', '/', (string) $this->getRequest()->getParam('type', '')));
        $type = $types[0];

        if (!class_exists($type) || !in_array(ConditionInterface::class, class_implements($type), true)) {
            return $this->getResponse()->setBody('');
        }

        $condition = $this->_objectManager->create($type)
            ->setId($this->getRequest()->getParam('id'))
            ->setType($type)
            ->setRule($this->limitFactory->create())
            ->setPrefix('conditions');

        if (!empty($types[1])) {
            $condition->setAttribute($types[1]);
        }

        $html = '';
        if ($condition instanceof AbstractCondition) {
            $condition->setJsFormObject($this->getRequest()->getParam('form'));
            $condition->setFormName($this->getRequest()->getParam('form_namespace'));
            $html = $condition->asHtmlRecursive();
        }

        return $this->getResponse()->setBody($html);
    }
}
