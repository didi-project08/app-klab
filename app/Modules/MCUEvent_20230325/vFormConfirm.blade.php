<cpModalSize>modal-md</cpModalSize>
<cpModalTitle>Confirm MCU Event</cpModalTitle>
<cpModalFooter>
    <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
    <button class="btn btn-outline-primary" type="button" onclick="jf{{$_id_}}dGridSave()">Confirm</button>
</cpModalFooter>

<div id="cp{{$_id_}}dGridFormContent">
    <form id="cp{{$_id_}}dGridForm" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="status" name="status" style="width:100%"></select>
                <div id="status_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="confirm_note" name="confirm_note" style="width:100%"></select>
                <div id="confirm_note_error" class='form_error'></div>
            </div>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#status').combobox({
            required:true,
            label:"Status",
            labelPosition:'top',
            prompt:"Choose Status",
            editable:false,
            valueField:'id',
            textField:'title',
            panelHeight:'auto',
            data:[
                {id:'sAccepted',title:"Accept", selected:true},
                {id:'sDeclined',title:"Decline"},
            ]
        });
        $eu('#confirm_note').textbox({
            required:true,
            label:"Note",
            labelPosition:'top',
            prompt:"Note",
            multiline:true,
            height:150
        });
        $eu('#confirm_note').textbox('textbox').focus();
    })

    function jf{{$_id_}}dGridSave(){
        preloader_block();
        $eu("#cp{{$_id_}}dGridForm").form("submit", {
            url: "{{ @$url_save }}",
            onSubmit: function() {
                if(!$eu(this).form('validate')){
                    preloader_none();
                    return $eu(this).form('validate');
                }
            },
            success: function(res) {
                preloader_none();

                var r = JSON.parse(res);

                $(".form_error").html("");

                if (r.success) {
                    Swal.fire({
                        width:'300px',
                        icon: 'success',
                        title: 'Success',
                        text: r.message,
                        showConfirmButton:false
                    })
                    setTimeout(() => { Swal.close(); }, 1000);

                    cpGlobalModalClose("{{$cpModalId}}");
                    $eu("#cp{{$_id_}}dGrid").datagrid('reload');
                    $eu("#cp{{$_id_}}dGrid").datagrid('clearSelections');
                    $eu("#cp{{$_id_}}dGrid").datagrid('clearChecked');
                } else {
                    Swal.fire({
                        width:'300px',
                        icon: 'error',
                        title: 'Oops...',
                        text: r.message,
                        showConfirmButton:false
                    })
                    setTimeout(() => { Swal.close(); }, 3000);

                    if (r.form_error) {
                        var form_error_array = r.form_error_array;
                        for (key in form_error_array) {
                            for (key2 in form_error_array[key]) {
                                if(key2 == 0){
                                    $("#" + key + "_error").append(form_error_array[key][key2]);
                                }else{
                                    $("#" + key + "_error").append("<br>"+form_error_array[key][key2]);
                                }
                            }
                        }
                    }
                }
            }
        })
    }
</script>