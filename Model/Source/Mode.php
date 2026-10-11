<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Model\Source;

use KevinRuscoe\HyvaCmsComponentLimits\Model\Limit;
use Magento\Framework\Data\OptionSourceInterface;

class Mode implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => Limit::MODE_RESTRICT, 'label' => __('Keep these components in these places')],
            ['value' => Limit::MODE_ALLOWLIST, 'label' => __('Only allow these components in these places')],
            ['value' => Limit::MODE_EXCLUSIVE, 'label' => __('Lock these components and places together')],
        ];
    }
}
