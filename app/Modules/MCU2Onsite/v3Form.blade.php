<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Regist MCu</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idb_}}dGridSave()">Submit</button>
    </cpModalFooter>
<?php } ?>

<div id="cp{{$_idb_}}dGridFormContent">
    <form id="cp{{$_idb_}}dGridForm" method="post" enctype="multipart/form-data">
        @csrf
        <?php if(@$mode == 'edit' || @$mode == 'present') { ?>
            @method('PUT')
        <?php } ?>
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="cp{{$_idb_}}schedule_date" name="schedule_date" style="width:100%">
                <div id="schedule_date_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="cp{{$_idb_}}actual_date" name="actual_date" style="width:100%">
                <div id="actual_date_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="cp{{$_idb_}}mcu_format_package_id" name="mcu_format_package_id" style="width:100%"></select>
                <div id="mcu_format_package_id_error" class='form_error'></div>
            </div>
        </div>
    </form>
    <div style="height:200px">
    <table id="cp{{$_idb_}}dGridFromdGrid01"></table>
    </div>
</div>

<script>
    $eu(function(){
        $eu('#cp{{$_idb_}}schedule_date').datebox({
            required:true,
            label:"Schedule Date",
            labelPosition:'top',
            prompt:"dd/mm/yyyy",
            formatter:datebox_formatter_ddmmyyyy,
            parser:datebox_parser_ddmmyyyy,
            value:' '
        });
        $eu('#cp{{$_idb_}}actual_date').datebox({
            required:false,
            label:"Actual Date",
            labelPosition:'top',
            prompt:"dd/mm/yyyy",
            formatter:datebox_formatter_ddmmyyyy,
            parser:datebox_parser_ddmmyyyy,
            value:' '
        });
        $eu('#cp{{$_idb_}}mcu_format_package_id').combobox({
            required:true,
            label:"Pakcage",
            labelPosition:'top',
            prompt:"Choose Package",
            method: "GET",
            // url:'{{$urlb}}readFormatPackageCombo',
            editable:false,
            valueField:'id',
            textField:'title',
            panelHeight:115
        });
        
        let checkedData = $eu("#cp{{$_idb_}}dGrid").datagrid('getChecked'); 
        let checkedDataNew = {
            total:checkedData.length,
            rows:checkedData
        }
        $eu("#cp{{$_idb_}}dGridFromdGrid01").datagrid({
            border:true,
            striped: true,
            pagination: false,
            fit: true,
            fitColumns: false,
            rownumbers: true,
            checkbox: true,
            singleSelect: true,
            selectOnCheck: false,
            checkOnSelect: false,
            nowrap: false,
            idField: "id",
            data:checkedDataNew,
            frozenColumns: [[
                
            ]],
            columns: [[
                // {field: "corporate_name",title: "<b>Corporate</b>",align: "left",width: 200},
                {field: "emp_no",title: "<b>Emp. Number</b>",align: "center",width: 150},
                {field: "nik",title: "<b>ID (KTP / Passport)</b>",align: "center",width: 150},
                {field: "name",title: "<b>Fullname</b>",align: "left",width: 200},
                {field: "gender",title: "<b>Gender</b>",align: "center",width: 70},
                {field: "dob_dmY_slash",title: "<b>DOB</b>",align: "center",width: 100},
                {field: "phone",title: "<b>Phone</b>",align: "center",width: 150},
                {field: "address",title: "<b>Address</b>",align: "left",width: 200},
                {field: "area",title: "<b>Job Area</b>",align: "left",width: 150},
                {field: "division",title: "<b>Job Division</b>",align: "left",width: 150},
                {field: "position",title: "<b>Job Position</b>",align: "left",width: 150},
            ]],
            rowStyler: function(index, row){
                return 'color:'+row.allowRegistMCUColor;
            }
        })
        euDoLayout();
    })

    function jf{{$_idb_}}dGridSave(){
        preloader_block();

        let checkedData = $eu("#cp{{$_idb_}}dGrid").datagrid('getChecked'); 
        let corpEmpIdList = [];
        for (let i = 0; i < checkedData.length; i++) {
            corpEmpIdList.push(checkedData[i].id);
        }

        $eu("#cp{{$_idb_}}dGridForm").form("submit", {
            url: "{{ @$url_save }}",
            onSubmit: function() {
                if(!$eu(this).form('validate')){
                    preloader_none();
                    return $eu(this).form('validate');
                }
            },
            queryParams : { corpEmpIdList : corpEmpIdList},
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
                    setTimeout(() => { Swal.close(); }, 5000);

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
    <script>
        $(function(){
            $eu("#cp{{$_idb_}}mcu_format_package_id").combobox({url:'{{$urlb}}readFormatPackageCombo?mcu_format_id={{$eventData->mcu_format_id}}'});
        })
    </script>
<?php } else if(@$mode == 'edit' || @$mode == 'present') { ?>
    <script>
        $(function(){
            var selData = {!! $selData !!};

            $eu("#cp{{$_idb_}}mcu_format_package_id").combobox({url:'{{$urlb}}readFormatPackageCombo?delete=-1&mcu_format_id={{$eventData->mcu_format_id}}'});

            $eu("#cp{{$_idb_}}dGridForm").form('load', selData);
            // $eu('#type').combobox('disable',true);
            // $eu('#corporate_id').combobox('disable',true);
            // $eu('#corporate_emp_id').combogrid('disable',true);
            // $eu('#dob').datebox('setValue', selData.dob_dmY_slash);
            $eu('#schedule_date').datebox('setValue', selData.schedule_date_dmY_slash);
            $eu('#actual_date').datebox('setValue', selData.actual_date_dmY_slash);
            setTimeout(() => {
                $eu('#emp_no').textbox('textbox').focus();
            }, 500);
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp{{$_idb_}}dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?>