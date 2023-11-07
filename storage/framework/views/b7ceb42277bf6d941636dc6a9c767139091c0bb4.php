<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Add New Patient</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idb_); ?>dGridSave()">Create</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_id_); ?>dGridAdd()">Open </button>
    </cpModalFooter>
<?php } else if(@$mode == 'edit') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Edit Patient</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idb_); ?>dGridSave()">Update</button>
    </cpModalFooter>
<?php } else if(@$mode == 'present') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Present Patient</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idb_); ?>dGridSave()">Present</button>
    </cpModalFooter>
<?php } ?>

<div id="cp<?php echo e($_idb_); ?>dGridFormContent">
    <form id="cp<?php echo e($_idb_); ?>dGridForm" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php if(@$mode == 'edit' || @$mode == 'present') { ?>
            <?php echo method_field('PUT'); ?>
        <?php } ?>
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="emp_no" name="emp_no" style="width:100%">
                <div id="emp_no_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="area" name="area" style="width:100%">
                <div id="area_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="division" name="division" style="width:100%">
                <div id="division_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="position" name="position" style="width:100%">
                <div id="position_error" class='form_error'></div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="nik" name="nik" style="width:100%">
                <div id="nik_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="name" name="name" style="width:100%">
                <div id="name_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="dob" name="dob" style="width:100%">
                <div id="dob_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="gender" name="gender" style="width:100%">
                <div id="gender_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="phone" name="phone" style="width:100%">
                <div id="phone_error" class='form_error'></div>
            </div>
            <div class="col-12 mb-3">
                <input class="" type="text" id="address" name="address" style="width:100%">
                <div id="address_error" class='form_error'></div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="schedule_date" name="schedule_date" style="width:100%">
                <div id="schedule_date_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="actual_date" name="actual_date" style="width:100%">
                <div id="actual_date_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="mcu_format_package_id" name="mcu_format_package_id" style="width:100%"></select>
                <div id="mcu_format_package_id_error" class='form_error'></div>
            </div>
        </div>
        </br>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <input class="" type="text" id="status" name="status" style="width:100%">
                <div id="status_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="" type="text" id="status_note" name="status_note" style="width:100%">
                <div id="status_note_error" class='form_error'></div>
            </div>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#type').combobox({
            required:true,
            label:"Patient Type",
            labelPosition:'top',
            prompt:"Choose Patient Type",
            editable:true,
            valueField:'id',
            textField:'title',
            panelHeight:'auto',
            data:[
                {id:0,title:"PERSONAL", selected:true},
                {id:1,title:"CORPORATE"},
            ],
            onChange:function(newValue,oldValue){
                if(newValue == 0){
                    $eu('#corporate_id').combobox({'required':false});
                }else if(newValue == 1){
                    $eu('#corporate_id').combobox({'required':true});
                }
            }
        });
        $eu('#corporate_id').combobox({
            required:false,
            label:"Corporate",
            labelPosition:'top',
            prompt:"Choose Corporate",
            method: "GET",
            url:'<?php echo e($url); ?>corporateCombo?laboratory_id=<?php echo e($eventData->laboratory_id); ?>',
            editable:true,
            valueField:'id',
            textField:'title',
            panelHeight:150
        });
        $eu("#corporate_emp_id").combogrid({
            required:false,
            label:"Corporate Employee",
            labelPosition:'top',
            border:true,
            striped: true,
            pagination: true,
            fit: true,
            fitColumns: false,
            rownumbers: true,
            checkbox: true,
            singleSelect: true,
            selectOnCheck: false,
            checkOnSelect: false,
            nowrap: false,
            pageList: [10, 20, 25, 50, 100],
            pageSize: 100,
            method: "GET",
            idField: "id",
            textField : "name",
            mode:"remote",
            delay:500,
            // url: "<?php echo e($url); ?>b2Read",
            editable:true,
            panelHeight:200,
            frozenColumns: [[
                
            ]],
            columns: [[
                // {field: "corporate_name",title: "<b>Corporate</b>",align: "left",width: 200},
                {field: "emp_no",title: "<b>Emp. Number</b>",align: "center",width: 150},
                {field: "nik",title: "<b>ID (NIK / Passport)</b>",align: "center",width: 150},
                {field: "name",title: "<b>Fullname</b>",align: "left",width: 200},
                {field: "gender",title: "<b>Gender</b>",align: "center",width: 70},
                {field: "dob_dmY_slash",title: "<b>DOB</b>",align: "center",width: 100},
                {field: "phone",title: "<b>Phone</b>",align: "center",width: 150},
                {field: "address",title: "<b>Address</b>",align: "left",width: 200},
                {field: "area",title: "<b>Job Area</b>",align: "left",width: 150},
                {field: "division",title: "<b>Job Division</b>",align: "left",width: 150},
                {field: "position",title: "<b>Job Position</b>",align: "left",width: 150},
            ]],
            onSelect:function(index,row){
                
            },
            onHidePanel:function(){
                var sel = $eu("#corporate_emp_id").combogrid('grid').datagrid('getSelected');
                if(sel){
                    preloader_block()
                    setTimeout(() => {
                        $eu("#cp<?php echo e($_idb_); ?>dGridForm").form('load', sel);
                        preloader_none();
                    }, 500);
                }
            }
        })
        $eu('#emp_no').textbox({
            required:false,
            label:"Emp. Number",
            labelPosition:'top',
            prompt:"Emp. Number",
        })

        $eu('#area').textbox({
            required:false,
            label:"Job Area",
            labelPosition:'top',
            prompt:"Job Area"
        })
        $eu('#division').textbox({
            required:false,
            label:"Job Division",
            labelPosition:'top',
            prompt:"Job Division"
        })
        $eu('#position').textbox({
            required:false,
            label:"Job Position",
            labelPosition:'top',
            prompt:"Job Position"
        })

        $eu('#nik').textbox({
            required:false,
            label:"ID (NIK / Passport)",
            labelPosition:'top',
            prompt:"ID (NIK / Passport)"
        })
        $eu('#name').textbox({
            required:true,
            label:"Fullname",
            labelPosition:'top',
            prompt:"Fullname"
        })
        $eu('#gender').combobox({
            required:true,
            label:"Gender",
            labelPosition:'top',
            prompt:"Choose Gender",
            editable:true,
            valueField:'id',
            textField:'title',
            panelHeight:'auto',
            data:[
                {id:'M',title:"MALE", selected:true},
                {id:'F',title:"FEMALE"},
            ]
        });
        $eu('#dob').datebox({
            required:true,
            label:"Date of birth",
            labelPosition:'top',
            prompt:"dd/mm/yyyy",
            formatter:datebox_formatter_ddmmyyyy,
            parser:datebox_parser_ddmmyyyy
        });

        $eu('#phone').textbox({
            required:false,
            label:"Phone Number",
            labelPosition:'top',
            prompt:"Phone Number"
        });
        $eu('#address').textbox({
            required:false,
            label:"Address",
            labelPosition:'top',
            prompt:"Address",
            multiline:true,
            height:100
        });


        // $eu('#laboratory_id').combobox({
        //     required:true,
        //     label:"Provider",
        //     labelPosition:'top',
        //     prompt:"Choose Provider",
        //     method: "GET",
        //     url:'<?php echo e($url); ?>laboratoryCombo',
        //     editable:false,
        //     valueField:'id',
        //     textField:'title',
        //     panelHeight:150,
        //     value:"<?php echo e($eventData->laboratory_id); ?>"
        // });
        $eu('#schedule_date').datebox({
            required:true,
            label:"Schedule Date",
            labelPosition:'top',
            prompt:"dd/mm/yyyy",
            formatter:datebox_formatter_ddmmyyyy,
            parser:datebox_parser_ddmmyyyy,
            value:' '
        });
        $eu('#actual_date').datebox({
            required:false,
            label:"Actual Date",
            labelPosition:'top',
            prompt:"dd/mm/yyyy",
            formatter:datebox_formatter_ddmmyyyy,
            parser:datebox_parser_ddmmyyyy,
            value:' '
        });
        $eu('#mcu_format_package_id').combobox({
            required:true,
            label:"Pakcage",
            labelPosition:'top',
            prompt:"Choose Package",
            method: "GET",
            // url:'<?php echo e($urlb); ?>readFormatPackageCombo',
            editable:false,
            valueField:'id',
            textField:'title',
            panelHeight:115
        });
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
                {id:'sPresented',title:"Present", selected:true},
                {id:'sCanceled',title:"Cancel"},
            ]
        });
        $eu('#status_note').textbox({
            required:true,
            label:"Note",
            labelPosition:'top',
            prompt:"Note",
            multiline:true,
            height:150
        });
    })

    function jf<?php echo e($_idb_); ?>dGridSave(){
        preloader_block();
        $eu("#cp<?php echo e($_idb_); ?>dGridForm").form("submit", {
            url: "<?php echo e(@$url_save); ?>",
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

                    cpGlobalModalClose("<?php echo e($cpModalId); ?>");
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('reload');
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearSelections');
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearChecked');
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
            $eu('#corporate_id').combobox({
                onChange:function(newValue,oldValue){
                    $eu("#corporate_emp_id").combogrid({url:'<?php echo e($url); ?>readCorporateEmp?corporate_id='+newValue})
                }
            });
            $eu("#mcu_format_package_id").combobox({url:'<?php echo e($urlb); ?>readFormatPackageCombo?mcu_format_id=<?php echo e($eventData->mcu_format_id); ?>'});
            setTimeout(() => {
                $eu('#type').combobox('textbox').focus();
            }, 500);
        })
    </script>
<?php } else if(@$mode == 'edit' || @$mode == 'present') { ?>
    <script>
        $(function(){
            var selData = <?php echo $selData; ?>;
            $eu('#corporate_id').combobox({
                onChange:function(newValue,oldValue){
                    $eu("#corporate_emp_id").combogrid({url:'<?php echo e($url); ?>readCorporateEmp?id='+selData.corporate_emp_id})
                }
            });
            $eu("#mcu_format_package_id").combobox({url:'<?php echo e($urlb); ?>readFormatPackageCombo?mcu_format_id=<?php echo e($eventData->mcu_format_id); ?>'});

            $eu("#cp<?php echo e($_idb_); ?>dGridForm").form('load', selData);
            $eu('#type').combobox('disable',true);
            $eu('#corporate_id').combobox('disable',true);
            $eu('#corporate_emp_id').combogrid('disable',true);
            $eu('#dob').datebox('setValue', selData.dob_dmY_slash);
            $eu('#schedule_date').datebox('setValue', selData.schedule_date_dmY_slash);
            $eu('#actual_date').datebox('setValue', selData.actual_date_dmY_slash);
            $eu('#status').combobox('setValue','');
            $eu('#status_note').textbox('setValue','');
            setTimeout(() => {
                $eu('#emp_no').textbox('textbox').focus();
            }, 500);
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp<?php echo e($_idb_); ?>dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?><?php /**PATH C:\Users\Lenovo\Documents\Envato\viho_all\Viho-Laravel-8\theme\app\Modules/MCUEvent/v2Form.blade.php ENDPATH**/ ?>