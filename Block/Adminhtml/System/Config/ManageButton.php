<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * The limitations are managed in their own grid; this links to it from the Hyvä Commerce config tab
 */
class ManageButton extends Field
{
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->getLayout()->createBlock(Button::class)->setData([
            'label' => __('Manage Component Limitations'),
            'class' => 'primary',
            'onclick' => sprintf("setLocation('%s')", $this->getUrl('componentlimits/limit/index')),
        ])->toHtml();
    }
}
