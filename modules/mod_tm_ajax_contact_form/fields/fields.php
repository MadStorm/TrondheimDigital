<?php
defined('JPATH_PLATFORM') or die;
class JFormFieldFields extends JFormField{
	protected $type = 'fields';
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
			if($field->name === 'option_name' || $field->name === 'option_value') continue;
			$head_row_str[] = '<th>' . strip_tags($field->getLabel($field->name));
			$head_row_str[] = '<br /><small style="font-weight:normal">' . JText::_($field->description) . '</small>';
			$head_row_str[] = '</th>';
			$body_row_str[] = '<td>' . $field->getInput() . '</td>';
		}
		$head_row_str[] = '<th><div class="btn-group"><a class="add btn button btn-success"><span class="icon-plus"></span> </a></div></th>';
		$body_row_str[] = '<td><div class="btn-group">';
		$body_row_str[] = '<a class="add btn button btn-success"><span class="icon-plus"></span> </a>';
		$body_row_str[] = '<a class="remove btn button btn-danger"><span class="icon-minus"></span> </a>';
		$body_row_str[] = '</div></td>';
		$table = '<table id="' . $this->id . '_table" class="adminlist ' . $this->element['class'] . ' table table-striped">'
					. '<thead><tr><th width="1%" class="nowrap center hidden-phone"><i class="icon-menu-2"></i></th>' . implode("\n", $head_row_str) . '</tr></thead>'
					. '<tbody><tr><td class="order nowrap center hidden-phone">								
								<span class="sortable-handler">
									<span class="icon-menu"></span>
								</span>';
							$table .= '</td>' . implode("\n", $body_row_str) . '</tr></tbody>'
				. '</table>';
		$str = array();
		$str[] = '<div id="' . $this->id . '_container">';
		$str[] = $table;
		$str[] = '</div>';
		if (is_array($this->value)){
			$this->value = array_shift($this->value);
		}
		$data = array();
		$data[] = 'data-container="#' . $this->id . '_container"';
		$data[] = 'data-repeatable-element=">table>tbody>tr"';
		$data[] = 'data-bt-add="a.add"';
		$data[] = 'data-bt-remove="a.remove"';
		$data[] = 'data-maximum="' . $maximum . '"';
		$data[] = 'data-input="#' . $this->id . '"';
		$value = htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
		$str[] = '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . $value
				. '"  class="form-field-fields" ' . implode(' ', $data) . ' />';
		JHtml::_('bootstrap.framework');
		$this->doc = JFactory::getDocument();
		JHtml::_('jquery.ui', array('core', 'sortable'));
		$this->doc->addScript(JURI::root(true).'/modules/mod_tm_ajax_contact_form/js/fields.js');
		$this->doc->addStyledeclaration('.sortable-handler{cursor:pointer}');
		return implode("\n", $str);
	}
} ?>