<?php
defined( 'JPATH_PLATFORM' ) or die;
JFormHelper::loadFieldClass('repeatable');
$GLOBALS = array(
    'count' => 0
);
/**
 * This Field Provides Fixes for Joomla's Repeatable Field.
 * Fix 1: JRepeatable Requires Fieldsets to Work But in the Global Config These are Displayed Outside of the Repeatable Field so Need Hiding.
 * Fix 2: The Modal Close Button Doubles up the Fields (so we hide it till it's fixed). See: http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_id=8103&tracker_item_id=32311
 * Fix 3: On Clicking a New Row if There is a Chosen List Field it Requires Re-Initialisation Due a Joomla Bug Which Fires: resetChosen() in it's media/system/js/repeatable-uncompressed.js Which Fails.
 * Fix 4: On Modal Popup Bootstrap/Joomla Radio Fields Aren't Initialised.
 */
class JFormFieldRepeatableFix extends JFormFieldRepeatable{
    /**
     * The form field type.
     * @var     string
     */
    protected $type = 'repeatablefix';
    /**
     * Document Object.
     * @var     object
     */
    protected $doc;
    /**
     * Method to attach a JForm object to the field.
     * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
     * @param   mixed             $value    The form field value to validate.
     * @param   string            $group    The field name group control value. This acts as as an array container for the field.
     *                                      For example if the field has name="foo" and the group value is set to "bar" then the
     *                                      full field name would end up being "bar[foo]".
     * @return  boolean  True on success.
     */
    public function setup( SimpleXMLElement $element, $value, $group = null )
    {
        // Initiate Parent First so we Have Our Element Data Configured
        if(!parent::setup($element, $value, $group)){
            return false;
        }
        $this->doc = JFactory::getDocument();
        if($GLOBALS['count']==0){
            //$this->addCSS();
            $this->addJS();
        }
        $GLOBALS['count']++;
        return true;
    }//end function

    /**
     * Method to Add Field CSS.
     *
     * @return  void
     */
    protected function addCSS()
    {
        // Setup CSS
        $css    = array();
        // Fix 1
        $css[]  = 'input[type=radio]:checked + label.btn { display : none; }';
        // Fix 2
        $css[]  = '#' . $this->id . '_modal_table + .form-actions .btn-link { display : none; }';

        $this->doc->addStyleDeclaration( implode( "\n", $css ) );
    }//end function

    /**
     * Method to Add Field JS.
     * @note    Logic "Borrowed" From Joomla, With Customisations.
     * @return  void
     */
    protected function addJS(){
        // Fix 3
        $js = "(function($){
                    $(document).ready(function(){
                        $( document ).on('click', '.btn-group label:not(.active)', function(ev){
                            var label = $(this);
                            var input = $('#'+label.attr('for'));
                            if(!input.prop('checked')){
                                label.closest('.btn-group').find('label').removeClass('active btn-success btn-danger btn-primary');

                                if(input.val() == ''){
                                    label.addClass('active btn-primary');
                                }
                                else if(input.val() == 0){
                                    label.addClass('active btn-danger');
                                }
                                else{
                                    label.addClass('active btn-success');
                                }
                                input.prop('checked', true);
                                input.trigger('change');
                            }
                        })
                    });
                })(jQuery);";
        JHtml::_('bootstrap.framework');
        $this->doc->addScriptDeclaration( $js );
        $this->doc->addScript(JURI::root(true).'/modules/mod_tm_ajax_contact_form/js/repeatablefix.js');
    }
    protected function getInput(){
        $subForm = new JForm($this->name, array('control' => 'jform'));
        $xml = $this->element->children()->asXML();
        $subForm->load($xml);
        $subForm->repeatCounter = (int) @$this->form->repeatCounter;
        $children = $this->element->children();
        $subForm->setFields($children);
        $maximum = $this->element['maximum'] ? (int) $this->element['maximum'] : '999';
        $head_row_str = array();
        $body_row_str = array();
        foreach ($subForm->getFieldset() as $field){
            $field->name = (string) $field->element['name'];
            $head_row_str[] = '<th>' . strip_tags($field->getLabel($field->name));
            $head_row_str[] = '<br /><small style="font-weight:normal">' . JText::_($field->description) . '</small>';
            $head_row_str[] = '</th>';
            $body_row_str[] = '<td>' . $field->getInput() . '</td>';
        }
        $head_row_str[] = '<th><div class="btn-group"><a href="#" class="add btn button btn-success"><span class="icon-plus"></span> </a></div></th>';
        $body_row_str[] = '<td><div class="btn-group">';
        $body_row_str[] = '<a class="add btn button btn-success"><span class="icon-plus"></span> </a>';
        $body_row_str[] = '<a class="remove btn button btn-danger"><span class="icon-minus"></span> </a>';
        $body_row_str[] = '</div></td>';
        $table = '<table id="' . $this->id . '_table" class="adminlist ' . $this->element['class'] . ' table table-striped">'
                    . '<thead><tr>' . implode("\n", $head_row_str) . '</tr></thead>'
                    . '<tbody><tr>' . implode("\n", $body_row_str) . '</tr></tbody>'
                . '</table>';
        $str = array();
        $str[] = '<div id="' . $this->id . '_container">';
        $str[] = '<div id="' . $this->id . '_modal" class="modal hide">';
        $str[] = $table;
        $str[] = '<div class="modal-footer">';
        $str[] = '<button class="close-modal btn button btn-link">' . JText::_('JCANCEL') . '</button>';
        $str[] = '<button class="save-modal-data btn button btn-primary">' . JText::_('JAPPLY') . '</button>';
        $str[] = '</div>';
        $str[] = '</div>';
        $str[] = '</div>';
        $select = (string) $this->element['select'] ? JText::_((string) $this->element['select']) : JText::_('JLIB_FORM_BUTTON_SELECT');
        $icon = $this->element['icon'] ? '<span class="icon-' . $this->element['icon'] . '"></span> ' : '';
        $str[] = '<button class="open-modal btn" id="' . $this->id . '_button" >' . $icon . $select . '</button>';
        if (is_array($this->value)){
            $this->value = array_shift($this->value);
        }
        $data = array();
        $data[] = 'data-container="#' . $this->id . '_container"';
        $data[] = 'data-modal-element="#' . $this->id . '_modal"';
        $data[] = 'data-repeatable-element="table tbody tr"';
        $data[] = 'data-bt-add="a.add"';
        $data[] = 'data-bt-remove="a.remove"';
        $data[] = 'data-bt-modal-open="#' . $this->id . '_button"';
        $data[] = 'data-bt-modal-close="button.close-modal"';
        $data[] = 'data-bt-modal-save-data="button.save-modal-data"';
        $data[] = 'data-maximum="' . $maximum . '"';
        $data[] = 'data-input="#' . $this->id . '"';
        $value = htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
        $str[] = '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . $value
                . '"  class="form-field-repeatable" ' . implode(' ', $data) . ' />';
        JHtml::_('bootstrap.framework');
        return implode("\n", $str);
    }
} ?>