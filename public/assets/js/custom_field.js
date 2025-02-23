$(document).ready(function(){
    let dataTable = $("#custom_field_table").DataTable({
        ajax:$("#custom_field_table").data('url'),
        columns:[
            {
                data: 'label'
            },
            {
                data: 'field_id'
            },
            {
                data: 'is_required'
            },
            {
                data: 'action',
                searchable: false,
                orderable: false
            }
        ]
    });

    $(document).on('submit', "#custom_field_form", function(e){
        e.preventDefault();
        let url = $(this).attr('action');
        let type = $(this).attr('method');
        let data = $(this).serialize();
        clearValidationErrors();
        $.ajax({
            url,
            type,
            data,
            dataType: 'json',
            success: function(resp){
                if(resp.success){
                    alert(resp.message);
                    dataTable.ajax.reload();
                    $("#custom_field_form").find('input[name="_method"]').remove();
                    hideModal();
                    resetForm($("#custom_field_form"));
                }
            },
            error: function(resp){
                if(!resp.responseJSON.success){
                    showValidationErrors(resp.responseJSON.errors);
                }else{
                    console.log(resp.responseJSON)
                }
            }
        });
    });

    $(document).on('click', '.delete', function(){
        const isConfirm = confirm('Do you want to delete ?');
        if(isConfirm){
            let url = $(this).data('url');
            $.ajax({
                url,
                type:'delete',
                dataType:'json',
                success:function(resp){
                    if(resp.success){
                        alert(resp.message);
                        dataTable.ajax.reload();
                    }
                },
                error:function(resp){
                    alert('Error');
                    console.log(resp);
                }
            })
        }
    });

    $(document).on('click', '.edit', function(){
        let url = $(this).data('url');
        $.ajax({
            url,
            type:'get',
            dataType: 'json',
            success:function(resp){
                if(resp.success){
                    let form = $("#custom_field_form");
                    form.append('<input type="hidden" value="PUT" name="_method"/>');
                    form.attr('action', resp.url);
                    setFormValues("#custom_field_form", resp.data);
                    $("#add_custom_field").find('.modal-title').text('Edit Custom Field');
                    $("#add_custom_field").find('.submit_btn').text('Edit Field')
                    showModal($("#add_custom_field"));
                }
            },
            error:function(resp){
                alert('Error');
                console.log(resp);
            }
        })
    });

    $('.modal').on('hidden.bs.modal', function(){
        clearValidationErrors();
        resetForm();
    });

    $(document).on('click', '.add', function(){
        $("#custom_field_form").attr('action', storeUrl);
        $("#add_custom_field").find('.modal-title').text('Add Custom Field');
        $("#add_custom_field").find('.submit_btn').text('Add Field');
    });
});
