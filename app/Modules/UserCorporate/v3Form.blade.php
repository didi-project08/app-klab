<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Add New User</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idb_}}dGridSave()">Create</button>
    </cpModalFooter>
<?php } else if(@$mode == 'edit') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Edit User</cpModalTitle>
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
                <select class="" id="i{{$_idb_}}corporate_id" name="corporate_id" style="width:100%"></select>
                <div id="corporate_id_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="i{{$_idb_}}f_role_name" name="f_role_name" style="width:100%">
                <div id="f_role_name_error" class='form_error'></div>
            </div>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#i{{$_idb_}}corporate_id').combobox({
            required:true,
            label:"Corporate",
            labelPosition:'top',
            prompt:"Choose Corporate",
            method: "GET",
            editable:false,
            valueField:'id',
            textField:'title',
            panelHeight:150
        });
        $eu('#i{{$_idb_}}f_role_name').textbox({
            required:true,
            label:"Role Name",
            labelPosition:'top',
            prompt:"Role Name"
        });
        euDoLayout();
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
            $eu('#i{{$_idb_}}corporate_id').combobox({
                url:'{{$urlb}}readCorporateCombo?selectFirst=1'
            });
        })
    </script>
<?php } else if(@$mode == 'edit') { ?>
    <script>
        $eu(function(){
            $eu('#i{{$_idb_}}corporate_id').combobox({
                url:'{{$urlb}}readCorporateCombo?delete=-1'
            });
            var selData = {!! $selData !!};
            $eu("#cp{{$_idb_}}dGridForm").form('load', selData);
            $eu('#i{{$_idb_}}corporate_id').combobox('disable',true);
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp{{$_idb_}}dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?>