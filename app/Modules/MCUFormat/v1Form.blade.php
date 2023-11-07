<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Add New MCU-Format</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idb_}}dGridSave()">Create</button>
    </cpModalFooter>
<?php } else if(@$mode == 'edit') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Edit MCU-Format</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idb_}}dGridSave()">Update</button>
    </cpModalFooter>
<?php } ?>

<div id="cp{{$_idb_}}dGridFormContent">
    <form id="cp{{$_idb_}}dGridForm" method="post" enctype="multipart/form-data">
        @csrf
        <?php if(@$mode == 'edit') { ?>
            @method('PUT')
        <?php } ?>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="i{{$_idb_}}laboratory_id" name="laboratory_id" style="width:100%"></select>
                <div id="laboratory_id_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="i{{$_idb_}}title" name="title" style="width:100%">
                <div id="title_error" class='form_error'></div>
            </div>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#i{{$_idb_}}laboratory_id').combobox({
            required:true,
            label:"Provider",
            labelPosition:'top',
            prompt:"Choose Provider",
            method: "GET",
            // url:'{{$urlb}}readLaboratoryCombo',
            editable:false,
            valueField:'id',
            textField:'title',
            panelHeight:150
        });
        $eu('#i{{$_idb_}}title').textbox({
            required:true,
            label:"Format Name",
            labelPosition:'top',
            prompt:"Format Name"
        });
        setTimeout(() => {
            $eu('#i{{$_idb_}}title').textbox('textbox').focus();
        }, 500);
    })

    function jf{{$_idb_}}dGridSave(){
        preloader_block();
        $eu("#cp{{$_idb_}}dGridForm").form("submit", {
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
                    $eu("#cp{{$_idb_}}dGrid").datagrid('reload');
                    $eu("#cp{{$_idb_}}dGrid").datagrid('clearSelections');
                    $eu("#cp{{$_idb_}}dGrid").datagrid('clearChecked');
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

<?php if(@$mode == 'add') { ?>
    <script>
        $eu(function(){
            $eu('#i{{$_idb_}}laboratory_id').combobox('reload','{{$urlb}}readLaboratoryCombo');
        })
    </script>
<?php } else if(@$mode == 'edit') { ?>
    <script>
        $eu(function(){
            $eu('#i{{$_idb_}}laboratory_id').combobox('reload','{{$urlb}}readLaboratoryCombo?delete=-1');

            var selData = {!! $selData !!};
            $eu("#cp{{$_idb_}}dGridForm").form('load', selData);
            $eu('#i{{$_idb_}}laboratory_id').textbox('disable',true);
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp{{$_idb_}}dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?>