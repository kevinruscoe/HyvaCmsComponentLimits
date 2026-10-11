<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * The entity being edited in the Hyva CMS editor, flattened for rule conditions to validate against.
 * Every attribute is always set (null when not applicable) so conditions never try to load() it.
 */
class PlacementContext extends AbstractModel
{
    public const CONTENT_TYPES = [
        'cms_page' => 'CMS Page',
        'cms_block' => 'CMS Block',
        'menu' => 'Menu',
        'template' => 'Template',
        'snippet' => 'Snippet',
        'category_attribute' => 'Category Attribute',
        'product_attribute' => 'Product Attribute',
    ];

    public const ATTRIBUTES = [
        'content_type',
        'entity_id',
        'page_id',
        'page_identifier',
        'page_layout',
        'block_id',
        'block_identifier',
        'menu_id',
        'menu_identifier',
    ];
}
