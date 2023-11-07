<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Add New Client</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idb_}}dGridSave()">Create</button>
    </cpModalFooter>
<?php } else if(@$mode == 'edit') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Edit Client</cpModalTitle>
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
                <input class="form-controls" type="text" id="corporate_id" name="corporate_id" style="width:100%">
                <div id="corporate_id_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="title" name="title" style="width:100%">
                <div id="title_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="form-controls" type="text" id="prov" name="prov" style="width:100%">
                <div id="prov_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="form-controls" type="text" id="city" name="city" style="width:100%">
                <div id="city_error" class='form_error'></div>
            </div>
            <div class="col-12 mb-3">
                <input class="form-controls" type="text" id="address" name="address" style="width:100%">
                <div id="address_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="pic_name" name="pic_name" style="width:100%">
                <div id="pic_name_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="pic_phone" name="pic_phone" style="width:100%">
                <div id="pic_phone_error" class='form_error'></div>
            </div>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#corporate_id').combobox({
            required:true,
            label:"Corporate",
            labelPosition:'top',
            prompt:"Choose Corporate",
            method: "GET",
            // url:'{{$urlb}}readCorporateCombo',
            editable:false,
            valueField:'id',
            textField:'title',
            panelHeight:150
        });
        $eu('#title').textbox({
            required:true,
            label:"Client Name",
            labelPosition:'top',
            prompt:"Client Name"
        })
        setTimeout(() => {
            $eu('#title').textbox('textbox').focus();
        }, 500);
        $eu('#prov').textbox({
            required:false,
            label:"Provice",
            labelPosition:'top',
            prompt:"Provice"
        })
        $eu('#city').textbox({
            required:false,
            label:"City",
            labelPosition:'top',
            prompt:"City"
        })
        $eu('#address').textbox({
            required:false,
            label:"Address",
            labelPosition:'top',
            prompt:"Address",
            multiline:true,
            height:100
        })
        $eu('#pic_name').textbox({
            required:true,
            label:"PIC Name",
            labelPosition:'top',
            prompt:"PIC Name"
        })
        $eu('#pic_phone').textbox({
            required:true,
            label:"PIC Phone (WA)",
            labelPosition:'top',
            prompt:"PIC Phone (WA)"
        })
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
            $eu('#corporate_id').combobox("reload",'{{$urlb}}readCorporateCombo?selectFirst=1');
        })
    </script>
<?php } else if(@$mode == 'edit') { ?>
    <script>
        $(function(){
            var selData = {!! $selData !!};
            $eu('#corporate_id').combobox("reload",'{{$urlb}}readCorporateCombo?id='+selData.corporate_id);
            $eu("#cp{{$_idb_}}dGridForm").form('load', selData);
            $eu('#corporate_id').combobox('disable',true);
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp{{$_idb_}}dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?>