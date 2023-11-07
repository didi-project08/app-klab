<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Add New User</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idbf_}}dGridSave()">Create</button>
    </cpModalFooter>
<?php } else if(@$mode == 'edit') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Edit User</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idbf_}}dGridSave()">Update</button>
    </cpModalFooter>
<?php } ?>

<div id="cp{{$_idbf_}}dGridFormContent">
    <form id="cp{{$_idbf_}}dGridForm" method="post" enctype="multipart/form-data">
        @csrf
        <?php if(@$mode == 'edit') { ?>
            @method('PUT')
        <?php } ?>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="i{{$_idbf_}}f_role_name" name="f_role_name" style="width:100%">
                <div id="f_role_name_error" class='form_error'></div>
            </div>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#i{{$_idbf_}}f_role_name').textbox({
            required:true,
            label:"Role Name",
            labelPosition:'top',
            prompt:"Role Name"
        });
        euDoLayout();
    })

    function jf{{$_idbf_}}dGridSave(){
        preloader_block();
        $eu("#cp{{$_idbf_}}dGridForm").form("submit", {
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
    <!-- no action -->
<?php } else if(@$mode == 'edit') { ?>
    <script>
        $eu(function(){
            var selData = {!! $selData !!};
            $eu("#cp{{$_idbf_}}dGridForm").form('load', selData);
            $eu('#i{{$_idbf_}}laboratory_id').combobox('disable',true);
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp{{$_idbf_}}dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?>