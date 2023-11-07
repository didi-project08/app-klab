<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Add New MCU Project</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idb_}}dGridSave()">Create</button>
    </cpModalFooter>
<?php } else if(@$mode == 'edit') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Edit MCU Project</cpModalTitle>
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
            <div class="col-12 col-md-12 mb-3 d-none" id="owner_frame">
                <select class="" id="owner" name="owner" style="width:100%"></select>
                <div id="owner_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="corporate_id" name="corporate_id" style="width:100%"></select>
                <div id="corporate_id_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="corporate_client_id" name="corporate_client_id" style="width:100%"></select>
                <div id="corporate_client_id_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="laboratory_id" name="laboratory_id" style="width:100%"></select>
                <div id="laboratory_id_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="mcu_format_id" name="mcu_format_id" style="width:100%"></select>
                <div id="mcu_format_id_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="" type="text" id="title" name="title" style="width:100%">
                <div id="title_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="date_from" name="date_from" style="width:100%">
                <div id="date_from_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="date_to" name="date_to" style="width:100%">
                <div id="date_to_error" class='form_error'></div>
            </div>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#owner').combobox({
            required:true,
            label:"Owner",
            labelPosition:'top',
            prompt:"Choose Owner",
            editable:true,
            valueField:'id',
            textField:'title',
            panelHeight:'auto',
            data:[
                {id:2,title:"PROVIDER / MITRA / LAB"},
                {id:3,title:"CORPORATE"},
            ],
            value:{{ ($userScope['info']->f_user_admin == 1) ? 2 : $userScope['info']->f_user_type }},
            readonly:{{ ($userScope['info']->f_user_admin == 1) ? 0 : 1 }}
        });
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
        $eu('#corporate_client_id').combobox({
            required:true,
            label:"Client",
            labelPosition:'top',
            prompt:"Choose Client",
            method: "GET",
            // url:'{{$urlb}}readCorporateClientCombo',
            editable:false,
            valueField:'id',
            textField:'title',
            panelHeight:150,
            onBeforeLoad:function(){
                $eu('#corporate_client_id').combobox('setValue','');
            }
        });
        $eu('#laboratory_id').combobox({
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
        $eu('#mcu_format_id').combobox({
            required:true,
            label:"MCU Format",
            labelPosition:'top',
            prompt:"Choose MCU Format",
            method: "GET",
            // url:'{{$urlb}}readLaboratoryCombo',
            editable:false,
            valueField:'id',
            textField:'title',
            panelHeight:150,
            onBeforeLoad:function(){
                $eu('#mcu_format_id').combobox('setValue','');
            }
        });
        $eu('#title').textbox({
            required:true,
            label:"Event Title",
            labelPosition:'top',
            prompt:"Event Title",
        });

        $eu('#date_from').datebox({
            required:true,
            label:"Event From",
            labelPosition:'top',
            prompt:"dd/mm/yyyy",
            formatter:datebox_formatter_ddmmyyyy,
            parser:datebox_parser_ddmmyyyy,
            value:" "
        });
        $eu('#date_to').datebox({
            required:true,
            label:"Event To",
            labelPosition:'top',
            prompt:"dd/mm/yyyy",
            formatter:datebox_formatter_ddmmyyyy,
            parser:datebox_parser_ddmmyyyy,
            value:" "

        });

        <?php if($userScope['info']->f_user_admin == 1) { ?>
            $("#owner_frame").removeClass("d-none");
            $eu('body').panel('doLayout');
        <?php } ?>
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
            $eu('#corporate_id').combobox({
                url:'{{$urlb}}readCorporateCombo?selectFirst=1',
                onChange:function(newValue,oldValue){
                    $eu('#corporate_client_id').combobox('reload','{{$urlb}}readCorporateClientCombo?corporate_id='+newValue);
                }
            });
            $eu('#laboratory_id').combobox({
                url:'{{$urlb}}readLaboratoryCombo',
                onChange:function(newValue,oldValue){
                    $eu('#mcu_format_id').combobox('reload','{{$urlb}}readMCUFormatCombo?selectFirst=1&laboratory_id='+newValue);
                }
            });
        })
    </script>
<?php } else if(@$mode == 'edit') { ?>
    <script>
        $eu(function(){
            $eu('#corporate_id').combobox({
                url:'{{$urlb}}readCorporateCombo?delete=-1',
                onChange:function(newValue,oldValue){
                    $eu('#corporate_client_id').combobox('reload','{{$urlb}}readCorporateClientCombo?delete=-1&corporate_id='+newValue);
                }
            });
            $eu('#laboratory_id').combobox({
                url:'{{$urlb}}readLaboratoryCombo?delete=-1',
                onChange:function(newValue,oldValue){
                    $eu('#mcu_format_id').combobox('reload','{{$urlb}}readMCUFormatCombo?delete=-1&laboratory_id='+newValue);
                }
            });

            var selData = {!! $selData !!};
            $eu("#cp{{$_idb_}}dGridForm").form('load', selData);
            $eu('#corporate_id').textbox('disable',true);
            $eu('#corporate_client_id').textbox('disable',true);
            $eu('#laboratory_id').textbox('disable',true);
            $eu('#mcu_format_id').textbox('disable',true);
            $eu('#date_from').datebox('setValue', selData.date_from_dmY_slash);
            $eu('#date_to').datebox('setValue', selData.date_to_dmY_slash);
            $eu('#title').textbox('textbox').focus();
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp{{$_idb_}}dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?>