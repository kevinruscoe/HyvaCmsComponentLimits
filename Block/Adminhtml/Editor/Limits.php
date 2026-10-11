<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Block\Adminhtml\Editor;

use KevinRuscoe\HyvaCmsComponentLimits\Model\ComponentLimiter;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

class Limits extends Template
{
    private ComponentLimiter $componentLimiter;

    public function __construct(
        Context $context,
        ComponentLimiter $componentLimiter,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->componentLimiter = $componentLimiter;
    }

    /**
     * @return string[]
     */
    public function getDisallowedComponents(): array
    {
        return $this->componentLimiter->getDisallowedComponents(
            (string) $this->getRequest()->getParam('type'),
            (int) $this->getRequest()->getParam('id')
        );
    }
}
