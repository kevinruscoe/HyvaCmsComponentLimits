<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Model\Limit\Condition;

use KevinRuscoe\HyvaCmsComponentLimits\Model\PlacementContext;
use Hyva\MenuBuilder\Model\ResourceModel\Menu\CollectionFactory as MenuCollectionFactory;
use Magento\Cms\Model\Page\Source\PageLayout;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as BlockCollectionFactory;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

/**
 * Matches against the entity open in the Hyva CMS editor (see PlacementContext)
 */
class Placement extends AbstractCondition
{
    private const SELECT_ATTRIBUTES = ['content_type', 'page_id', 'page_layout', 'block_id', 'menu_id'];

    private PageCollectionFactory $pageCollectionFactory;
    private BlockCollectionFactory $blockCollectionFactory;
    private MenuCollectionFactory $menuCollectionFactory;
    private PageLayout $pageLayout;

    public function __construct(
        Context $context,
        PageCollectionFactory $pageCollectionFactory,
        BlockCollectionFactory $blockCollectionFactory,
        MenuCollectionFactory $menuCollectionFactory,
        PageLayout $pageLayout,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->blockCollectionFactory = $blockCollectionFactory;
        $this->menuCollectionFactory = $menuCollectionFactory;
        $this->pageLayout = $pageLayout;
    }

    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            'content_type' => __('Editor Type'),
            'page_id' => __('CMS Page'),
            'page_identifier' => __('CMS Page URL Key'),
            'page_layout' => __('CMS Page Layout'),
            'block_id' => __('CMS Block'),
            'block_identifier' => __('CMS Block Identifier'),
            'menu_id' => __('Menu'),
            'menu_identifier' => __('Menu Identifier'),
        ]);

        return $this;
    }

    public function getInputType()
    {
        return in_array($this->getAttribute(), self::SELECT_ATTRIBUTES, true) ? 'select' : 'string';
    }

    public function getValueElementType()
    {
        return in_array($this->getAttribute(), self::SELECT_ATTRIBUTES, true) ? 'select' : 'text';
    }

    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            $this->setData('value_select_options', $this->buildValueSelectOptions());
        }

        return $this->getData('value_select_options');
    }

    private function buildValueSelectOptions(): array
    {
        switch ($this->getAttribute()) {
            case 'content_type':
                $options = [];
                foreach (PlacementContext::CONTENT_TYPES as $value => $label) {
                    $options[] = ['value' => $value, 'label' => __($label)];
                }
                return $options;
            case 'page_id':
                return $this->toOptions($this->pageCollectionFactory->create()->setOrder('title', 'ASC'), 'page_id', 'title');
            case 'block_id':
                return $this->toOptions($this->blockCollectionFactory->create()->setOrder('title', 'ASC'), 'block_id', 'title');
            case 'menu_id':
                return $this->toOptions($this->menuCollectionFactory->create()->setOrder('title', 'ASC'), 'id', 'title');
            case 'page_layout':
                return $this->pageLayout->toOptionArray();
        }

        return [];
    }

    private function toOptions(iterable $collection, string $idField, string $titleField): array
    {
        $options = [];
        foreach ($collection as $item) {
            $options[] = [
                'value' => $item->getData($idField),
                'label' => sprintf('%s (%s)', $item->getData($titleField), $item->getData('identifier')),
            ];
        }

        return $options;
    }
}
