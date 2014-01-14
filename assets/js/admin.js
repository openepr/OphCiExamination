$(document).ready(function() {

	$('.sortable').sortable({
		update: function(event, ui) {
			if (typeof(OphCiExamination_sort_url) !== 'undefined') {
				var ids = [];
				$('div.sortable').children('li').map(function() {
					ids.push($(this).attr('data-attr-id'));
				});
				$.ajax({
					'type': 'POST',
					'url': OphCiExamination_sort_url,
					'data': {
						order: ids,
						YII_CSRF_TOKEN: YII_CSRF_TOKEN
					},
					'success': function(data) {
						new OpenEyes.UI.Dialog.Alert({
							content: 'Questions reordered'
						}).open();
					}
				});
			}
		}
	});

	$('#question_disorder').bind('change', function() {
		did = $(this).val();
		if (did) {
			window.location.href = URI(window.location.href).setSearch('disorder_id',did);
		}
	});

	$('input.model_enabled').bind('change', function() {
		var model_id = $(this).parents('tr').data('attr-id');
		var model_name = $(this).parents('tr').data('attr-name');

		var enabled = 0;
		if ($(this).attr('checked')) {
			enabled = 1;
		}
		$.ajax({
			type: 'POST',
			url: OphCiExamination_model_status_url,
			data: {
				id: model_id,
				enabled: enabled,
				YII_CSRF_TOKEN: YII_CSRF_TOKEN
			},
			'success': function() {
				if (enabled) {
					new OpenEyes.UI.Dialog.Alert({
						content: model_name + ' enabled'
					}).open();
				}
				else {
					new OpenEyes.UI.Dialog.Alert({
						content: model_name + ' disabled'
					}).open();
				}
			}
		});
	});

	$('#admin_workflow_steps tbody tr').click(function(e) {
		e.preventDefault();

		$('#admin_workflow_steps tbody tr').removeClass('selected');
		$(this).addClass('selected');

		var id = $(this).data('id');

		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/OphCiExamination/admin/editWorkflowStep?step_id='+id,
			'success': function(html) {
				$('#step_element_types').html(html);
			}
		});
	});

	$('#admin_workflow_steps tbody').sortable({
		update: function(event, ui) {
			var i = 1;

			var ids = {};

			$('#admin_workflow_steps tbody tr').map(function() {
				$(this).children('td:first').text(i);
				ids[$(this).data('id')] = i;
				i += 1;
			});

			$.ajax({
				'type': 'POST',
				'data': $.param(ids)+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				'url': baseUrl+'/OphCiExamination/admin/reorderWorkflowSteps',
				'success': function(resp) {
					if (resp != "1") {
						alert("Something went wrong trying to save the new order.  Please refresh the page and try again or contact support for assistance.");
					}
				}
			});
		}
	});

	$('#et_add_element_type').live('click',function(e) {
		e.preventDefault();

		if ($('#element_type_id').val() == '') {
			alert("Please select an element type to add");
			return;
		}

		$.ajax({
			'type': 'POST',
			'data': 'element_type_id='+$('#element_type_id').val()+'&step_id='+$('#admin_workflow_steps tr.selected').data('id')+'&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
			'url': baseUrl+'/OphCiExamination/admin/addElementTypeToWorkflowStep',
			'success': function(resp) {
				if (resp != "1") {
					alert("Something went wrong trying to add the element type.  Please try again or contact support for assistance.");
				} else {
					$('#admin_workflow_steps tr.selected').click();
				}
			}
		});
	});

	$('a.removeElementType').live('click',function(e) {
		e.preventDefault();

		var row = $(this).parent().parent();
		var element_type_name = row.children('td:first').text();
		var element_type_id = $(this).data('element-type-id');

		$.ajax({
			'type': 'POST',
			'data': 'element_type_item_id='+$(this).attr('rel')+'&step_id='+$('#admin_workflow_steps tr.selected').data('id')+'&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
			'url': baseUrl+'/OphCiExamination/admin/removeElementTypeFromWorkflowStep',
			'success': function(resp) {
				if (resp != "1") {
					alert("Something went wrong trying to remove the element type.	Please try again or contact support for assistance.");
				} else {
					row.remove();
					$('#element_type_id').append('<option value="'+element_type_id+'">'+element_type_name+'</option>');
					sort_selectbox($('#element_type_id'));
				}
			}
		});
	});

	$('#et_add_step').click(function(e) {
		e.preventDefault();

		$.ajax({
			'type': 'POST',
			'dataType': 'json',
			'data': 'workflow_id='+$('#OphCiExamination_Workflow_id').val()+'&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
			'url': baseUrl+'/OphCiExamination/admin/addWorkflowStep',
			'success': function(data) {
				if (typeof(data['id']) == 'undefined') {
					alert("Something went wrong trying to add the workflow step.	Please try again or contact support for assistance.");
				} else {
					window.location.reload();
				}
			}
		});
	});

	$('a.removeElementSet').live('click',function(e) {
		e.preventDefault();

		var element_set_id = $(this).attr('rel');

		$.ajax({
			'type': 'POST',
			'data': 'workflow_id='+$('#OphCiExamination_Workflow_id').val()+'&element_set_id='+element_set_id+'&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
			'url': baseUrl+'/OphCiExamination/admin/removeWorkflowStep',
			'success': function(resp) {
				if (resp != "1") {
					alert("Something went wrong trying to remove the workflow step.  Please try again or contact support for assistance.");
				} else {
					window.location.reload();
				}
			}
		});
	});

	$('#et_save_step_name').live('click',function(e) {
		e.preventDefault();

		if ($('#step_name').val() == '') {
			alert("Name cannot be blank");
			return;
		}

		$.ajax({
			'type': 'POST',
			'data': 'workflow_id='+$('#OphCiExamination_Workflow_id').val()+'&element_set_id='+$('#admin_workflow_steps tbody tr.selected').data('id')+'&step_name='+$('#step_name').val()+'&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
			'url': baseUrl+'/OphCiExamination/admin/saveWorkflowStepName',
			'success': function(resp) {
				if (resp != "1") {
					alert("Something went wrong trying to set the name for the step.  Please try again or contact support for assistance.");
				} else {
					$('#admin_workflow_steps tr.selected td:nth-child(2)').text($('#step_name').val());
				}
			}
		});
	});
});
