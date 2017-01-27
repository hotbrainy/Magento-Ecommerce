<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
class Amasty_Shopby_Block_Adminhtml_Filter_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Filter form
     * @var Varien_Data_Form
     */
    protected $_form;

    /** @var  Amasty_Shopby_Model_Filter */
    protected $model;

    protected $yesno;

    protected function _prepareForm()
    {
        //create form structure
        $this->_form = new Varien_Data_Form();
        $this->setForm($this->_form);

        $this->model = Mage::registry('amshopby_filter');

        $this->yesno = array($this->__('No'), $this->__('Yes'));

        $this->_prepareRegularForm();

        //set form values
        $data = Mage::getSingleton('adminhtml/session')->getFormData();

        if ($data) {
            $this->_form->setValues($data);
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        }
        elseif ($this->model) {
            $this->_form->setValues($this->model->getData());
        }

        return parent::_prepareForm();
    }

    protected function _prepareRegularForm()
    {
        $this->_prepareFieldsetGeneral();

        if (!$this->isDecimal()) {
            $this->_prepareFieldsetBlocks();
        }

        $this->_prepareFieldsetSeo();

        $this->_prepareFieldsetSpecial();

        $this->setupDependencies();
    }

    protected function _prepareFieldsetGeneral()
    {
        $fldSet = $this->_form->addFieldset('amshopby_general', array('legend'=> $this->__('Display Properties')));

        $fldSet->addField('block_pos', 'select', array(
            'label'     => $this->__('Show in the Block'),
            'name'      => 'block_pos',
            'values'    => Mage::getModel('amshopby/source_position')->toOptionArray(),
        ));

        $fldSet->addField('display_type', 'select', array(
            'label'     => $this->__('Display Type'),
            'name'      => 'display_type',
            'values'    => $this->model->getDisplayTypeOptionsSource()->toOptionArray(),
        ));

        if ($this->isDecimal()) {
            $fldSet->addField('slider_type', 'select', array(
                'label'     => $this->__('Indicate maximum ranges'),
                'name'      => 'slider_type',
                'values'    => $this->yesno,
            ));

            $fldSet->addField('slider_decimal', 'text', array(
                'label'     => $this->__('Slider step'),
                'name'      => 'slider_decimal',
            ));


            $fldSet->addField('range', 'text', array(
                'label'     => $this->__('Range Step'),
                'name'      => 'range',
                'note'      => $this->__('Set 10 to get ranges 10-20,20-30, etc. Custom value improves pages speed. Leave empty to get default ranges.'),
            ));

            $fldSet->addField('from_to_widget', 'select', array(
                'label'     => $this->__('Show From-To Widget'),
                'name'      => 'from_to_widget',
                'values'    => $this->yesno,
            ));

            $fldSet->addField('value_label', 'text', array(
                'label'     => $this->__('Units label'),
                'name'      => 'value_label',
                'note'      => $this->__('Specify attribute units, like inch., MB, px, ft etc.'),
            ));
        }
        else {
            $fldSet->addField('show_search', 'select', array(
                'label'     => $this->__('Show Search Box'),
                'name'      => 'show_search',
                'values'    => $this->yesno,
            ));

            $fldSet->addField('number_options_for_show_search', 'text', array(
                'label'     => $this->__('Minimum items to show Search Box'),
                'name'      => 'number_options_for_show_search',
                'note'      => $this->__('0 - display Search Box anytime'),
            ));

            $fldSet->addField('max_options', 'text', array(
                'label'     => $this->__('Number of unfolded options'),
                'name'      => 'max_options',
                'note'      => $this->__('Applicable for `Labels Only`, `Images only` and `Labels and Images` display types. Zero means all options are unfolded')
            ));

            $fldSet->addField('sort_by', 'select', array(
                'label'     => $this->__('Sort Options By'),
                'name'      => 'sort_by',
                'values'    => array(
                    array(
                        'value' => 0,
                        'label' => $this->__('Position')
                    ),
                    array(
                        'value' => 1,
                        'label' => $this->__('Name')
                    ),
                    array(
                        'value' => 2,
                        'label' => $this->__('Product Quantities')
                    )),
            ));

            $fldSet->addField('sort_featured_first', 'select', array(
                'label'     => $this->__('When folded, display featured options first'),
                'name'      => 'sort_featured_first',
                'values'    => $this->yesno,
            ));
        }


        $fldSet->addField('hide_counts', 'select', array(
            'label'     => $this->__('Hide quantities'),
            'name'      => 'hide_counts',
            'values'    => $this->yesno
        ));

        $fldSet->addField('collapsed', 'select', array(
            'label'     => $this->__('Collapsed'),
            'name'      => 'collapsed',
            'values'    => $this->yesno,
            'note'      => $this->__('Will be collapsed until customer select any filter option'),
        ));

        $fldSet->addField('comment', 'text', array(
            'label'     => $this->__('Tooltip'),
            'name'      => 'comment',
        ));
    }

    protected function _prepareFieldsetBlocks()
    {
        $fldSet2 = $this->_form->addFieldset('amshopby_blocks', array('legend'=> $this->__('Additional Blocks')));
        $fldSet2->addField('show_on_list', 'select', array(
            'label'     => $this->__('Show on List'),
            'name'      => 'show_on_list',
            'values'    => $this->yesno,
            'note'      => $this->__('Show option description and image above product listing.'),
        ));

        $fldSet2->addField('show_on_view', 'select', array(
            'label'     => $this->__('Show on Product'),
            'name'      => 'show_on_view',
            'values'    => $this->yesno,
            'note'      => $this->__('Show options images block on product view page. You need to perform modifications in theme template, see Amasty Improved Navigation User Guide on page 15.'),
        ));
    }

    protected function _prepareFieldsetSeo()
    {
        $fldSet3 = $this->_form->addFieldset('amshopby_seo', array('legend'=> $this->__('Search Engines Optimization')));
        $fldSet3->addField('seo_nofollow', 'select', array(
            'label'     => $this->__('Robots NoFollow Tag'),
            'name'      => 'seo_nofollow',
            'values'    => $this->yesno,
        ));
        $fldSet3->addField('seo_noindex', 'select', array(
            'label'     => $this->__('Robots NoIndex Tag'),
            'name'      => 'seo_noindex',
            'values'    => $this->yesno,
        ));
        $fldSet3->addField('seo_rel', 'select', array(
            'label'     => $this->__('Rel NoFollow'),
            'name'      => 'seo_rel',
            'values'    => $this->yesno,
            'note'      => $this->__('For the links in the left navigation'),
        ));
        $fldSet3->addField('disable_seo_url', 'select', array(
            'label'     => $this->__('Keep as GET parameter in URL'),
            'name'      => 'disable_seo_url',
            'values'    => $this->yesno,
            'note'      => $this->__('SEO URL mode will not affect this filter if set "Yes"'),
        ));
    }

    protected function _prepareFieldsetSpecial()
    {
        $fldSet = $this->_form->addFieldset('amshopby_special', array('legend'=> $this->__('Special Cases')));

        if (!$this->isDecimal()) {
            $fldSet->addField('single_choice', 'select', array(
                'label'     => $this->__('Single Choice Only'),
                'name'      => 'single_choice',
                'values'    => $this->yesno,
                'note'      => $this->__('Disables multiple selection'),
            ));

            $fldSet->addField('use_and_logic', 'select', array(
                'label'     => $this->__('Use AND logic for multiple selections'),
                'name'      => 'use_and_logic',
                'values'    => $this->yesno,
                'note'      => $this->__('Each product that will be displayed should contain ALL selected options'),
            ));
        }

        $fldSet->addField('include_in', 'text', array(
            'label'     => $this->__('Include Only In Categories'),
            'name'      => 'include_in',
            'note'      => $this->__('Comma separated list of the categories IDs like 17,4,25'),
            'after_element_html' => $this->__('<div class="field-tooltip"><div>' . 'You can also use Landing Page URL aliases here like "red-sport-cars".' . '</div></div>'),
        ));

        $fldSet->addField('exclude_from', 'text', array(
            'label'     => $this->__('Exclude From Categories'),
            'name'      => 'exclude_from',
            'note'      => $this->__('Comma separated list of the categories IDs like 17,4,25'),
            'after_element_html' => $this->__('<div class="field-tooltip"><div>' . 'You can also use Landing Page URL aliases here like "red-sport-cars".' . '</div></div>'),
        ));

        $fldSet->addField('depend_on', 'text', array(
            'label'     => $this->__('Show only when one of the following options are selected'),
            'name'      => 'depend_on',
            'note'      => $this->__('Comma separated list of the option IDs'),
        ));

        $fldSet->addField('depend_on_attribute', 'text', array(
            'label'     => $this->__('Show only when any options of attributes below is selected'),
            'name'      => 'depend_on_attribute',
            'note'      => $this->__('Comma separated list of the attribute codes like color, brand etc'),
        ));
    }

    protected function setupDependencies()
    {
        /** @var Mage_Adminhtml_Block_Widget_Form_Element_Dependence $mapper */
        $mapper = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence');

        if ($this->isDecimal()) {
            $mapper
                ->addFieldMap('display_type', 'display_type')
                ->addFieldMap('slider_type', 'slider_type')
                ->addFieldMap('slider_decimal', 'slider_decimal')
                ->addFieldDependence('slider_type', 'display_type', Amasty_Shopby_Model_Catalog_Layer_Filter_Price::DT_SLIDER)
                ->addFieldDependence('slider_decimal', 'display_type', Amasty_Shopby_Model_Catalog_Layer_Filter_Price::DT_SLIDER)
                ;
        } else {
            $mapper
                ->addFieldMap('single_choice', 'single_choice')
                ->addFieldMap('use_and_logic', 'use_and_logic')
                ->addFieldDependence('use_and_logic', 'single_choice', 0)
                ->addFieldMap('show_search', 'show_search')
                ->addFieldMap('number_options_for_show_search', 'number_options_for_show_search')
                ->addFieldDependence('number_options_for_show_search', 'show_search', 1)
                ;
        }

        $this->setChild('form_after', $mapper);
    }

    protected function isDecimal()
    {
        return $this->model->getBackendType() == 'decimal';
    }
}
