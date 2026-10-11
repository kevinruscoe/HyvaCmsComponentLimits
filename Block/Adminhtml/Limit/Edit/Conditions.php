<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Block\Adminhtml\Limit\Edit;

use KevinRuscoe\HyvaCmsComponentLimits\Model\Limit;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Rule\Block\Conditions as ConditionsRenderer;
use Magento\Rule\Model\Condition\AbstractCondition;

/**
 * Conditions builder, rendered into the ui_component form (same approach as catalog price rules)
 */
class Conditions extends Generic
{
    public const FORM_NAME = 'kevinruscoe_component_limit_form';

    private ConditionsRenderer $conditionsRenderer;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        ConditionsRenderer $conditionsRenderer,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->conditionsRenderer = $conditionsRenderer;
    }

    protected function _prepareForm()
    {
        /** @var Limit $limit */
        $limit = $this->_coreRegistry->registry('current_component_limit');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldsetId = $limit->getConditionsFieldSetId(self::FORM_NAME);
        $renderer = $this->getLayout()->createBlock(Fieldset::class)
            ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl(
                'componentlimits/limit/newConditionHtml/form/' . $fieldsetId,
                ['form_namespace' => self::FORM_NAME]
            ))
            ->setFieldSetId($fieldsetId);

        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            ['legend' => __('Places: where these conditions match (leave empty for everywhere)')]
        )->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', [
            'name' => 'conditions',
            'label' => __('Conditions'),
            'title' => __('Conditions'),
            'data-form-part' => self::FORM_NAME,
        ])->setRule($limit)->setRenderer($this->conditionsRenderer);

        $form->setValues($limit->getData());
        $this->setConditionFormName($limit->getConditions(), self::FORM_NAME, $fieldsetId);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    private function setConditionFormName(AbstractCondition $conditions, string $formName, string $jsFormName): void
    {
        $conditions->setFormName($formName);
        $conditions->setJsFormObject($jsFormName);

        foreach ((array) $conditions->getConditions() as $condition) {
            $this->setConditionFormName($condition, $formName, $jsFormName);
        }
    }
}
