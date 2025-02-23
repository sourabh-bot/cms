$(document).ready(function () {
    let contact_table = $("#contact_table").DataTable({
        ajax: $("#contact_table").data('url'),
        columns: [
            {
                data: 'name',
            },
            {
                data: 'email',
            },
            {
                data: 'phone',
            },
            {
                data: 'gender_id',
            },
            {
                data: 'profile_image'
            },
            {
                data: 'status'
            },
            {
                data: 'action',
                orderable: false,
                searchable: false
            }
        ]
    });

    $(document).on('submit', '#add_contact_form', function (e) {
        e.preventDefault();
        let url = $(this).attr('action');
        let type = $(this).attr('method');
        let data = new FormData(this);
        clearValidationErrors();
        $.ajax({
            url,
            type,
            data,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (resp) {
                if (resp.success) {
                    alert(resp.message)
                    contact_table.ajax.reload();
                    $("#add_contact_form").find('input[name="_method"]').remove();
                    hideModal();
                    resetForm($("#add_contact_form"));
                }
            },
            error: function (resp) {
                if (!resp.responseJSON.success) {
                    showValidationErrors(resp.responseJSON.errors);
                } else {
                    console.log(resp.responseJSON)
                }
            }
        })
    });

    $(document).on('click', '.delete', function () {
        const isConfirm = confirm('Do you want to delete ?');
        if (isConfirm) {
            let url = $(this).data('url');
            $.ajax({
                url,
                type: 'delete',
                dataType: 'json',
                success: function (resp) {
                    if (resp.success) {
                        alert(resp.message);
                        contact_table.ajax.reload();
                    }
                },
                error: function (resp) {
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
                    let form = $("#add_contact_form");
                    form.append('<input type="hidden" value="PUT" name="_method"/>');
                    form.attr('action', resp.url);
                    setFormValues("#add_contact_form", resp.data);
                    $("#add_new_contact").find('.modal-title').text('Edit Contact');
                    $("#add_new_contact").find('.submit_btn').text('Edit Contact')
                    showModal($("#add_new_contact"));
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
        $("#add_contact_form").attr('action', storeUrl);
        $("#add_new_contact").find('.modal-title').text('Add Contact');
        $("#add_new_contact").find('.submit_btn').text('Add Contact')
    });

    $(document).on('click', '.merge_contact', function(){
        let url = $(this).data('url');
        let contact_id = $(this).data('contact_id');
        $.ajax({
            url,
            dataType:'json',
            success:function(resp){
                if(resp.success){
                    $(".contact_list").empty();
                    $(".contact_list").html(resp.data);
                    $(".contact_id").val(contact_id);
                    $("#contact_merged_modal").modal('show');
                }
            },
            error:function(resp){
                console.log(resp);
                alert('Error');
            }
        })
    });

    $(document).on('submit', '#contact_merge_form', function(e){
        e.preventDefault();
        let data = $(this).serialize();
        let url = $(this).attr('action');
        let type = $(this).attr('method');
        let form = $(this);
        clearValidationErrors();
        $.ajax({
            url,
            type,
            dataType: 'json',
            data,
            success:function(resp){
                if(resp.success){
                    alert(resp.message);
                    hideModal();
                    contact_table.ajax.reload();
                }
            },
            error:function(resp){
                console.log(resp);
                if(resp.responseJSON.success == 422){
                    showValidationErrors(form);
                }else{
                    alert('Error');
                }
            }
        });
    });
});
