<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Model\Limit\Condition;

use Magento\Rule\Model\Condition\Context;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    private PlacementFactory $placementFactory;

    public function __construct(
        Context $context,
        PlacementFactory $placementFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->placementFactory = $placementFactory;
        $this->setType(self::class);
    }

    public function getNewChildSelectOptions()
    {
        $attributes = $this->placementFactory->create()->loadAttributeOptions()->getAttributeOption();

        $groups = [
            (string) __('Editor') => ['content_type'],
            (string) __('CMS Page') => ['page_id', 'page_identifier', 'page_layout'],
            (string) __('CMS Block') => ['block_id', 'block_identifier'],
            (string) __('Menu') => ['menu_id', 'menu_identifier'],
        ];

        $conditions = parent::getNewChildSelectOptions();
        $conditions[] = ['value' => self::class, 'label' => __('Conditions Combination')];

        foreach ($groups as $label => $codes) {
            $options = [];
            foreach ($codes as $code) {
                $options[] = ['value' => Placement::class . '|' . $code, 'label' => $attributes[$code]];
            }
            $conditions[] = ['label' => $label, 'value' => $options];
        }

        return $conditions;
    }
}
