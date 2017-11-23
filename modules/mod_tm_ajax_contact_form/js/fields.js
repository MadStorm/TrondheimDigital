

;(function($){
    $(document).ready(function (){
$( "#jform_params_fields_list_table" ).sortable({
    items: '>tbody>tr',
    change: function( event, ui ) {
        console.log(ui);
    }
});
})
	"use strict";

    $.Fields = function(input, options){
        // To avoid scope issues,
        var self = this;

        //direct call
        if(!self || self === window){
        	return new $.Fields(input, options);
        }

        self.$input = $(input);

        // check if alredy exist
        if(self.$input.data("Fields")){
        	return self;
        }

        // Add a reverse reference to the DOM object
        self.$input.data("Fields", self);

        // method initialize
        self.init = function(){
        	// merge options
            self.options = $.extend({}, $.Fields.defaults, options);

            self.$container = $(self.options.container);
            // Move out form the Form container
            // for prevent sending to server

            // container where the rows is live
            self.$rowsContainer = self.$container.find(self.options.repeatableElement).parent();


            // container for storing info about inputs
            self.inputs = [];
            self.values = {};

            // prepare a row template, and find available field names
            self.prepareTemplate();

            // check the values and keep it as object
            var val = self.$input.val();
            if(val){
            	// value can be not valid JSON
            	try {
            		self.values = JSON.parse(val);
				} catch (e) {
					if(e instanceof SyntaxError){
						// guess there a single quote problem
						try {
							val = val.replace(/'/g, '"').replace(/\\"/g, "\'");// ho ho ho
		            		self.values = JSON.parse(val);
						} catch (e) {
							// nop
							if(window.console){
    							console.log(e);
    						}
						}
					} else if(window.console){
						console.log(e);
					}
				}
            }

            // so init the form depend from values that we have
            self.buildRows();

            // bind add button
            self.$container.on('click', self.options.btAdd, function (e) {
            	e.preventDefault();
            	var after = $(this).closest('tr');
            	if(!after.length || after.closest('thead').length){
            		after = null;
            	}
            	self.addRow(after);
            });
            // bind remove button
            self.$container.on('click', self.options.btRemove, function (e) {
            	e.preventDefault();
            	var row = $(this).closest('tr');
            	self.removeRow(row);
            });



            var $apply = $('#toolbar-apply button').attr('onclick');
            var $save = $('#toolbar-save button').attr('onclick');
            var $save_new = $('#toolbar-save-new button').attr('onclick');
            var $save_copy = $('#toolbar-save-copy button').attr('onclick');
            $('#toolbar-apply button').removeAttr('onclick');
            $('#toolbar-save button').removeAttr('onclick');
            $('#toolbar-save-new button').removeAttr('onclick');
            $('#toolbar-save-copy button').removeAttr('onclick');
            $('#toolbar-apply button').click(function(){
                self.refreshValue();
                (new Function ($apply))()
            });
            $('#toolbar-save button').click(function(){
                self.refreshValue();
                (new Function ($save))()
            });
            $('#toolbar-save-new button').click(function(){
                self.refreshValue();
                (new Function ($save_new))()
            });
            $('#toolbar-save-copy button').click(function(){
                self.refreshValue();
                (new Function ($save_copy))()
            });

            // tell all that we a ready
            self.$input.trigger('weready');
            $('.btn-group label').removeClass('active').removeClass('danger').removeClass('btn-success');
            $('.btn-group input[type=radio]').each(function(){
                if($(this).is(':checked')){
                    var id = $(this).attr('id');
                    var active_class = $(this).val() == '1' ? 'btn-success' : 'btn-danger';
                    $('.btn-group label[for="'+id+'"]').addClass(active_class).addClass('active')
                }
            })
        };

        // prepare a template that we will use for repeating
        self.prepareTemplate = function(){
        	//find available
        	var $rows = self.$container.find(self.options.repeatableElement);
        	var $row = $($rows.get(0));
        	// clear scripts that can be attached to the fields
        	try {
        		self.clearScripts($row);
			} catch (e) {
				if(window.console){
					console.log(e);
				}
			}

        	var inputs = $row.find('>td>*[name],>td>fieldset>*[name]');
        	//keep the name and type for each
        	for(var i = 0, l = inputs.length; i < l; i++){
        		var name = $(inputs[i]).attr('name');
        		// check if alredy exist, for radio case
        		if(self.values[name]){
        			continue;
        		}
        		self.inputs.push({
        			name: name,
        			type: $(inputs[i]).attr('type') || inputs[i].tagName.toLowerCase()
        		});
        		// initialize values
        		self.values[name] = [];
        	}

        	// keep template
        	self.template = $row.prop('outerHTML');
        	// remove
        	$rows.remove();

        	// tell all that the template ready
            self.$input.trigger('prepare-template', self.template);
        };

        // build rows
        self.buildRows = function(){
        	// clean up any old
        	var $oldRows = self.$rowsContainer.children();
        	if($oldRows.length){
        		self.removeRow($oldRows);
        	}

	        // go through values and add a new copy
	        // but make sure that at least one will be added
	        var count = self.values[Object.keys(self.values)[0]].length || 1,
            	row = null;
            for(var i = 0; i < count; i++){
            	row = self.addRow(row, i);
            }
        };

        // add new row
        self.addRow = function(after, valueKey){
        	// count how much we already have
        	var count = self.$container.find(self.options.repeatableElement).length;
        	if(count >= self.options.maximum){
        		return null;
        	}

        	// make new from template
        	var row = $.parseHTML(self.template);

        	//add to container
        	if(after){
        		$(after).after(row);
        	} else {
        		self.$rowsContainer.prepend(row);
        	}

        	var $row = $(row);
        	// fix names and id`s
        	self.fixUniqueAttributes($row, count);
        	// set values
        	if(valueKey !== null && valueKey !== undefined){
            	for(var i = 0, l = self.inputs.length; i < l; i++){
            		var name  = self.inputs[i].name,
            			type  = self.inputs[i].type,
            			value = null;
            		if(self.values[name]){
            			value = self.values[name][valueKey];
            		}
            		// skip undefined
            		if(value === null || value === undefined){
            			continue;
            		}

            		if(type === 'radio'){
            			$row.find('*[name*="'+name+'"][value="' + value + '"]').attr('checked', 'checked');
            		}else if(type === 'checkbox'){
            			// check if there a multiple
            			if(value.length){
            				for(var v = 0, vl = value.length; v < vl; v++){
            					$row.find('*[name*="'+name+'"][value="' + value[v] + '"]').attr('checked', 'checked');
            				}
            			} else {
            				$row.find('*[name*="'+name+'"][value="' + value + '"]').attr('checked', 'checked');
            			}

            		} else {
            			$row.find('*[name*="'+name+'"]').val(value);
            		}
            	}
        	}

        	// try find out with related scripts,
        	// tricky thing, so be careful
        	try {
        		self.fixScripts($row);
			} catch (e) {
				if(window.console){
					console.log(e);
				}
			}

            $(row).find('input.form-field-repeatable').JRepeatable();

			// tell all about new row
            self.$input.trigger('row-add', $row);

        	return $row;
        };

        // remove row from container
        self.removeRow = function(row){
        	// tell all about row removing
            /*$($(row).find('input.form-field-repeatable').attr('data-modal-element')).modal({
                show: false,
                backdrop : false
            })
            $($(row).find('input.form-field-repeatable').attr('data-modal-element')).removeData("modal");
            $('body').find('>'+$(row).find('input.form-field-repeatable').attr('data-container')).remove();*/
            self.$input.trigger('row-remove', row);

        	$(row).remove();
        };

        //fix names ind id`s for field that in $row
        self.fixUniqueAttributes = function($row, count){
            //all elements that have a "id" attribute
            var haveIds = $row.find('input[type="radio"]');
            self.incresseAttrName(haveIds, 'id', count);
            self.incresseAttrName(haveIds, 'name', count);

            var haveIds = $row.find('input[type="hidden"], div[id], table[id], button.open-modal[id]');
            self.incresseAttrName(haveIds, 'id', count);

            // all labels that have a "for" attribute
            var haveFor = $row.find('label.btn[for]');
            self.incresseAttrName(haveFor, 'for', count);

            var haveDataInput = $row.find('*[data-input]');
            self.incresseAttrName(haveDataInput, 'data-input', count);

            var haveDataBtModalOpen = $row.find('*[data-bt-modal-open]');
            self.incresseAttrName(haveDataBtModalOpen, 'data-bt-modal-open', count);

            var haveDataModalElement = $row.find('*[data-modal-element]');
            self.incresseAttrName(haveDataModalElement, 'data-modal-element', count);

            var haveDataContainer = $row.find('*[data-container]');
            self.incresseAttrName(haveDataContainer, 'data-container', count);
        };

        // increse attribute name like: attribute_value + '_' + count
        self.incresseAttrName = function (elements, attr, count){
        	for(var i = 0, l = elements.length; i < l; i++){
        		var $el =  $(elements[i]);
        		var oldValue = $el.attr(attr);
        		// set new
        		$el.attr(attr, oldValue + '_' + count);
        	}
        };

        // refresh value in the main input
        self.refreshValue = function(){
        	var $rows = self.$container.find(self.options.repeatableElement);
        	// reset existing
        	self.values = {};
        	// go through available input names
            for(var i = 0, l = self.inputs.length; i < l; i++){
            	var name = self.inputs[i].name,
            		type = self.inputs[i].type;
            	// init new
            	self.values[name] = [];
            	// find all inputs and take their values
            	for(var r = 0, rl = $rows.length; r < rl; r++){
            		var $row = $($rows[r]),
            			val  = null;
            		if(type === 'radio'){
            			val = $row.find('*[name*="'+name+'"]:checked').val();
            		}else if(type === 'checkbox'){
            			var checked = $row.find('*[name*="'+name+'"]:checked');
            			// test for multiple
            			if(checked.length > 1){
            				val = [];
            				for(var c = 0, cl = checked.length; c < cl; c++){
            					val.push($(checked[c]).val());
            				}
            			} else {
            				// single checkbox
            				val = checked.val();
            			}
            		}else{
            			val = $row.find('*[name="'+name+'"]').val();
            		}
            		val = val === null ? '' : val;

            		self.values[name].push(val)
            	}
        	}
        	// put in to the main input
            self.$input.val(JSON.stringify(self.values));

            // tell all about value changed
            self.$input.trigger('value-update', self.values);
        };

        // remove scripts attached to fields
        self.clearScripts = function($row){
        	// destroy chosen if any
        	if($.fn.chosen){
        		$row.find('select.chzn-done').chosen('destroy');
        	}
        	// colorpicker
        	if($.fn.minicolors){
        		$row.find('.minicolors input').each(function(){
        			$(this).removeData('minicolors-initialized')
        			.removeData('minicolors-settings')
        			.removeProp('size')
        			.removeProp('maxlength')
        			.removeClass('minicolors-input')
        			// move out from <span>
        			.parents('span.minicolors').parent().append(this);
        		});
        		$row.find('span.minicolors').remove();
        	}
        };

        // method for hack the scripts that can be related
        // to the one of field that in given $row
        self.fixScripts = function($row){
        	// chosen hack
        	if($.fn.chosen){
        		$row.find('select').chosen()
        	}

        	//color picker
        	$row.find('.minicolors').each(function() {
        		var $el = $(this);
        		$el.minicolors({
					control: $el.attr('data-control') || 'hue',
					position: $el.attr('data-position') || 'right',
					theme: 'bootstrap'
				});
			});

        	// fix media field
        	$row.find('a[onclick*="jInsertFieldValue"]').each(function(){
        		var $el = $(this),
        			inputId = $el.siblings('input[type="text"]').attr('id'),
        			$select = $el.prev(),
        			oldHref = $select.attr('href');
        		// update the clear button
        		$el.attr('onclick', "jInsertFieldValue('', '" + inputId + "');return false;")
        		// update select button
        		$select.attr('href', oldHref.replace(/&fieldid=(.+)&/, '&fieldid=' + inputId + '&'));
        	});

        	// another modals
        	if(window.SqueezeBox){
        		SqueezeBox.assign($row.find('a.modal').get(), {parse: 'rel'});
        	}
        };

        // Run initializer
        self.init();
    };

    // defaults
    $.Fields.defaults = {
    	btModalSaveData: ".save-modal-data", // button for close the modal window, and keep the all changes
    	btAdd: "a.add", //  button selector for "add" action
    	btRemove: "a.remove",//  button selector for "remove" action
    	maximum: 10, // maximum repeating
    	repeatableElement: "table tbody tr"
    };

    $.fn.Fields = function(options){
        return this.each(function(){
        	var options = options || {},
        		data = $(this).data();

        	for (var p in data) {
                // check options in the element
                if (data.hasOwnProperty(p)) {
                     options[p] = data[p];
                }
            }
         	new $.Fields(this, options);
        });
    };

    // initialise all available
    // wait when all will be loaded, important for scripts fix
	$(window).on('load', function(){
		$('input.form-field-fields').Fields();
	})

})(jQuery);