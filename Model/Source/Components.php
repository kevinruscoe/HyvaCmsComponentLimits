<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Model\Source;

use Hyva\CmsLiveviewEditor\Model\Component\Provider;
use Magento\Framework\Data\OptionSourceInterface;

class Components implements OptionSourceInterface
{
    private Provider $componentProvider;

    public function __construct(Provider $componentProvider)
    {
        $this->componentProvider = $componentProvider;
    }

    public function toOptionArray()
    {
        $options = [];
        foreach ($this->componentProvider->getComponents() as $name => $component) {
            $options[] = [
                'value' => $name,
                'label' => sprintf('%s (%s)', $component['label'] ?? $name, $name),
            ];
        }

        usort($options, fn ($a, $b) => strcasecmp($a['label'], $b['label']));

        return $options;
    }
}
