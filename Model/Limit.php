<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Model;

use KevinRuscoe\HyvaCmsComponentLimits\Model\Limit\Condition\CombineFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;
use Magento\Rule\Model\Action\CollectionFactory as ActionCollectionFactory;

class Limit extends AbstractModel
{
    /** The components can only be placed where the conditions match */
    public const MODE_RESTRICT = 'restrict';

    /** Where the conditions match, only the components (and the children they accept) can be placed */
    public const MODE_ALLOWLIST = 'allowlist';

    /** Both of the above: only these components here, and these components only here */
    public const MODE_EXCLUSIVE = 'exclusive';

    public const MODES = [self::MODE_RESTRICT, self::MODE_ALLOWLIST, self::MODE_EXCLUSIVE];

    protected $_eventPrefix = 'kevinruscoe_hyva_cms_component_limit';

    private CombineFactory $combineFactory;
    private ActionCollectionFactory $actionCollectionFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        CombineFactory $combineFactory,
        ActionCollectionFactory $actionCollectionFactory,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->combineFactory = $combineFactory;
        $this->actionCollectionFactory = $actionCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\Limit::class);
        $this->setIdFieldName('rule_id');
    }

    public function getConditionsInstance()
    {
        return $this->combineFactory->create();
    }

    /**
     * Limitations have no actions, but the abstract rule model still expects an instance
     */
    public function getActionsInstance()
    {
        return $this->actionCollectionFactory->create();
    }

    public function getConditionsFieldSetId(string $formName = ''): string
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    /**
     * Whether the components can only be placed where the conditions match
     */
    public function restrictsComponents(): bool
    {
        return in_array($this->getData('mode'), [self::MODE_RESTRICT, self::MODE_EXCLUSIVE], true);
    }

    /**
     * Whether only the components can be placed where the conditions match
     */
    public function restrictsPlaces(): bool
    {
        return in_array($this->getData('mode'), [self::MODE_ALLOWLIST, self::MODE_EXCLUSIVE], true);
    }

    /**
     * @return string[]
     */
    public function getComponentNames(): array
    {
        $components = $this->getData('components');
        if (is_array($components)) {
            return array_values(array_filter($components));
        }

        return array_values(array_filter(explode(',', (string) $components)));
    }

    public function beforeSave()
    {
        if (is_array($this->getData('components'))) {
            $this->setData('components', implode(',', array_filter($this->getData('components'))));
        }

        return parent::beforeSave();
    }
}
