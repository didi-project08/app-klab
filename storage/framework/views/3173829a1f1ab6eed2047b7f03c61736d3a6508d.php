<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Add (Input)</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idbf_); ?>dGridSave()">Create</button>
    </cpModalFooter>
<?php } else if(@$mode == 'edit') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Edit (Input)</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idbf_); ?>dGridSave()">Update</button>
    </cpModalFooter>
<?php } ?>

<div id="cp<?php echo e($_idbf_); ?>dGridFormContent">
    <form id="cp<?php echo e($_idbf_); ?>dGridForm" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php if(@$mode == 'edit') { ?>
            <?php echo method_field('PUT'); ?>
        <?php } ?>
        <input class="d-none" type="text" name="main_parent" value="<?php echo e(@$formData['main_parent']); ?>">
        <input class="d-none" type="text" name="parent" value="<?php echo e(@$formData['parent']); ?>">
        <input class="d-none" type="text" name="level" value="<?php echo e(@$formData['level']); ?>">
        <input class="d-none" type="text" name="header" value="<?php echo e(@$formData['header']); ?>">
        <input class="d-none" type="text" name="sort" value="<?php echo e(@$formData['sort']); ?>">
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>code" name="code" style="width:100%">
                <div id="code_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>name" name="name" style="width:100%">
                <div id="name_error" class='form_error'></div>
            </div>

            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>input_type" name="input_type" style="width:100%">
                <div id="input_type_error" class='form_error'></div>
            </div>

            <div class="col-12 col-md-12 mb-3 d-none" id="i<?php echo e($_idbf_); ?>input_default__TEXT_frame">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>input_default__TEXT" name="" style="width:100%">
                <!-- <div id="input_default__TEXT_error" class='form_error'></div> -->
            </div>
            <div class="col-12 col-md-12 mb-3 d-none" id="i<?php echo e($_idbf_); ?>input_default__PARAGRAPH_frame">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>input_default__PARAGRAPH" name="" style="width:100%">
                <!-- <div id="input_default__PARAGRAPH_error" class='form_error'></div> -->
            </div>
            <div class="col-12 col-md-12 mb-3 d-none" id="i<?php echo e($_idbf_); ?>input_default__NUMBER_frame">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>input_default__NUMBER" name="" style="width:100%">
                <!-- <div id="input_default__NUMBER_error" class='form_error'></div> -->
            </div>
            <div class="col-12 col-md-6 mb-3 d-none" id="i<?php echo e($_idbf_); ?>input_default__RANGEFROM_frame">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>input_default__RANGEFROM" name="" style="width:100%">
                <!-- <div id="input_default__RANGEFROM_error" class='form_error'></div> -->
            </div>
            <div class="col-12 col-md-6 mb-3 d-none" id="i<?php echo e($_idbf_); ?>input_default__RANGETO_frame">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>input_default__RANGETO" name="" style="width:100%">
                <!-- <div id="input_default__RANGETO_error" class='form_error'></div> -->
            </div>
            <div class="col-12 col-md-12 mb-3 d-none" id="i<?php echo e($_idbf_); ?>input_default__OPTION_frame">
                <div class="mb-2">
                    <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>input_default__OPTION" name="" style="width:100%">
                    <!-- <div id="input_default__OPTION_error" class='form_error'></div> -->
                </div>
                <div class="d-none">
                    <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>input_option" name="input_option" style="width:100%">
                </div>
                <div class="mb-1">
                    <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>input_default__OPTION_input" name="" style="width:100%">
                </div>
                <div style="height:150px">
                    <table id="i<?php echo e($_idbf_); ?>input_default__OPTION_dGrid"></table>
                </div>
            </div>
            <div class="col-12 col-md-12 d-none" id="i<?php echo e($_idbf_); ?>input_default__IMAGE_frame">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>input_default__IMAGE" name="" style="width:100%">
                <!-- <div id="input_default__IMAGE_error" class='form_error'></div> -->
            </div>
            <div class="col-12 col-md-12 d-none">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>input_default" name="input_default" style="width:100%">
            </div>
            <div class="col-12 col-md-12 mb-3">
                <div id="input_default_error" class='form_error'></div>
            </div>

            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>input_desc" name="input_desc" style="width:100%">
                <div id="input_desc_error" class='form_error'></div>
            </div>

            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>unit" name="unit" style="width:100%">
                <div id="unit_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>ref_m" name="ref_m" style="width:100%">
                <div id="ref_m_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>ref_f" name="ref_f" style="width:100%">
                <div id="ref_f_error" class='form_error'></div>
            </div>
        </div>
    </form>
</div>

<script>

    function i<?php echo e($_idbf_); ?>input_default__OPTION_dGrid_data(){
        var inputType = $eu('#i<?php echo e($_idbf_); ?>input_type').combobox('getValue');
        if(inputType == "OPTION"){
            var dataOption = $eu("#i<?php echo e($_idbf_); ?>input_option").textbox('getValue');
            if(dataOption == null || dataOption == ""){
                dataOption = "[]";
            }
            dataOptionObj = JSON.parse(dataOption);

            dGridData = [];
            for (let i = 0; i < dataOptionObj.length; i++) {
                const element = dataOptionObj[i];
                dGridData.push({value:element.value, action:"<a style='color:red; cursor:pointer' onclick='i<?php echo e($_idbf_); ?>input_default__OPTION_dGrid_delete("+i+")'>X</a>"})
            }

            $eu("#i<?php echo e($_idbf_); ?>input_default__OPTION_dGrid").datagrid({
                data:dGridData
            })
        }
    }
    function i<?php echo e($_idbf_); ?>input_default__OPTION_dGrid_delete(index){
        var dataOption = $eu("#i<?php echo e($_idbf_); ?>input_option").textbox('getValue');
        if(dataOption == null || dataOption == ""){
            dataOption = "[]";
        }
        dataOptionObj = JSON.parse(dataOption);
        dataOptionObj.splice(index, 1);

        $eu('#i<?php echo e($_idbf_); ?>input_option').textbox('setValue', JSON.stringify(dataOptionObj));

        i<?php echo e($_idbf_); ?>input_default__OPTION_dGrid_data();
    }

    $eu(function(){
        $eu('#i<?php echo e($_idbf_); ?>code').textbox({
            required:true,
            label:"Item Code",
            labelPosition:'top',
            prompt:"Item Code"
        });
        $eu('#i<?php echo e($_idbf_); ?>name').textbox({
            required:true,
            label:"Item Name",
            labelPosition:'top',
            prompt:"Item Name"
        });

        $eu('#i<?php echo e($_idbf_); ?>input_type').combobox({
            required:true,
            label:"Input Type",
            labelPosition:'top',
            prompt:"Input Type",
            panelHeight:'auto',
            valueField:"value",
            textField:"value",
            editable:false,
            data:[
                {value:"TEXT"},
                {value:"PARAGRAPH"},
                {value:"NUMBER"},
                {value:"RANGE_NUMBER"},
                {value:"OPTION"},
                {value:"IMAGE"}
            ],
            onChange:function(newValue, oldValue){
                $('#i<?php echo e($_idbf_); ?>input_default__TEXT_frame').addClass("d-none");
                $('#i<?php echo e($_idbf_); ?>input_default__PARAGRAPH_frame').addClass("d-none");
                $('#i<?php echo e($_idbf_); ?>input_default__NUMBER_frame').addClass("d-none");
                $('#i<?php echo e($_idbf_); ?>input_default__RANGEFROM_frame').addClass("d-none");
                $('#i<?php echo e($_idbf_); ?>input_default__RANGETO_frame').addClass("d-none");
                $('#i<?php echo e($_idbf_); ?>input_default__OPTION_frame').addClass("d-none");
                $('#i<?php echo e($_idbf_); ?>input_default__IMAGE_frame').addClass("d-none");

                $eu('#i<?php echo e($_idbf_); ?>input_default').textbox({ required:false });
                $eu('#i<?php echo e($_idbf_); ?>input_default__TEXT').textbox({ required:false });
                $eu('#i<?php echo e($_idbf_); ?>input_default__PARAGRAPH').textbox({ required:false });
                $eu('#i<?php echo e($_idbf_); ?>input_default__NUMBER').numberbox({ required:false });
                $eu('#i<?php echo e($_idbf_); ?>input_default__RANGEFROM').numberbox({ required:false });
                $eu('#i<?php echo e($_idbf_); ?>input_default__RANGETO').numberbox({ required:false });
                $eu('#i<?php echo e($_idbf_); ?>input_default__OPTION').textbox({ required:false });
                $eu('#i<?php echo e($_idbf_); ?>input_default__IMAGE').filebox({ required:false });
                
                if(newValue == "TEXT"){
                    $('#i<?php echo e($_idbf_); ?>input_default__TEXT_frame').removeClass("d-none");
                }else if(newValue == "PARAGRAPH"){
                    $('#i<?php echo e($_idbf_); ?>input_default__PARAGRAPH_frame').removeClass("d-none");
                }else if(newValue == "NUMBER"){
                    $('#i<?php echo e($_idbf_); ?>input_default__NUMBER_frame').removeClass("d-none");
                }else if(newValue == "RANGE_NUMBER"){
                    $('#i<?php echo e($_idbf_); ?>input_default__RANGEFROM_frame').removeClass("d-none");
                    $('#i<?php echo e($_idbf_); ?>input_default__RANGETO_frame').removeClass("d-none");
                }else if(newValue == "OPTION"){
                    $('#i<?php echo e($_idbf_); ?>input_default__OPTION_frame').removeClass("d-none");
                    i<?php echo e($_idbf_); ?>input_default__OPTION_dGrid_data();
                }else if(newValue == "IMAGE"){
                    // $('#i<?php echo e($_idbf_); ?>input_default__IMAGE_frame').removeClass("d-none");
                }
                euDoLayout();
            }
        })


        $eu('#i<?php echo e($_idbf_); ?>input_default').textbox({
            required:false,
            label:"Default Value",
            labelPosition:'top',
            prompt:"Default Value",
            multiline:true,
            height:95
        });

        $eu('#i<?php echo e($_idbf_); ?>input_default__TEXT').textbox({
            required:false,
            label:"Default Value",
            labelPosition:'top',
            prompt:"Default Value"
        });
        $eu('#i<?php echo e($_idbf_); ?>input_default__PARAGRAPH').textbox({
            required:false,
            label:"Default Value",
            labelPosition:'top',
            prompt:"Default Value",
            multiline:true,
            height:95
        });
        $eu('#i<?php echo e($_idbf_); ?>input_default__NUMBER').numberbox({
            required:false,
            label:"Default Value",
            labelPosition:'top',
            prompt:"Default Value",
            // min:0,
            precision:2,
            groupSeparator:".",
            decimalSeparator:","
        });
        $eu('#i<?php echo e($_idbf_); ?>input_default__RANGEFROM').numberbox({
            required:false,
            label:"Default From",
            labelPosition:'top',
            prompt:"Default From",
            // min:0,
            precision:2,
            groupSeparator:".",
            decimalSeparator:","
        });
        $eu('#i<?php echo e($_idbf_); ?>input_default__RANGETO').numberbox({
            required:false,
            label:"Default To",
            labelPosition:'top',
            prompt:"Default To",
            // min:0,
            precision:2,
            groupSeparator:".",
            decimalSeparator:","
        });
        $eu('#i<?php echo e($_idbf_); ?>input_default__OPTION').textbox({
            required:false,
            label:"Default Value",
            labelPosition:'top',
            prompt:"Default Value",
        });
        $eu('#i<?php echo e($_idbf_); ?>input_option').textbox({
            required:false,
            label:"Input Option",
            labelPosition:'top',
            prompt:"Input Option",
            multiline:true,
            height:95
        });
        $eu('#i<?php echo e($_idbf_); ?>input_default__OPTION_input').textbox({
            required:false,
            labelPosition:'top',
            prompt:"Add Option...",
        });
        $eu('#i<?php echo e($_idbf_); ?>input_default__OPTION_input').textbox('textbox').bind('keydown', function(e){
            if (e.keyCode == 13){
                var addOption = $eu('#i<?php echo e($_idbf_); ?>input_default__OPTION_input').textbox('getValue');
                $eu('#i<?php echo e($_idbf_); ?>input_default__OPTION_input').textbox('setValue','');
                // alert(addOption);

                var dataOption = $eu("#i<?php echo e($_idbf_); ?>input_option").textbox('getValue');
                if(dataOption == null || dataOption == ""){
                    dataOption = "[]";
                }
                dataOptionObj = JSON.parse(dataOption);
                dataOptionObj.push({value:addOption});

                $eu('#i<?php echo e($_idbf_); ?>input_option').textbox('setValue', JSON.stringify(dataOptionObj));

                i<?php echo e($_idbf_); ?>input_default__OPTION_dGrid_data();
            }
        });	

        $eu("#i<?php echo e($_idbf_); ?>input_default__OPTION_dGrid").datagrid({
            toolbar: "#i<?php echo e($_idbf_); ?>input_default__OPTION_dGridToolbar",
            border:true,
            striped: true,
            // pagination: true,
            fit: true,
            fitColumns: true,
            rownumbers: true,
            singleSelect: true,
            selectOnCheck: false,
            checkOnSelect: false,
            nowrap: false,
            // pageList: [10, 20, 25, 50, 100],
            // pageSize: 100,
            // method: "GET",
            idField: "value",
            columns: [[
                {field: "value",title: "<b>Options</b>",align: "left",width: 200},
                {field: "action",title: "<b>DELETE</b>",align: "center"},
            ]],
        });
        $eu('#i<?php echo e($_idbf_); ?>input_default__IMAGE').filebox({
            required:false,
            label:"Default Value",
            labelPosition:'top',
            prompt:"Default Value",
            accept: 'image/*'
        });


        $eu('#i<?php echo e($_idbf_); ?>input_desc').combobox({
            required:true,
            label:"Description Box",
            labelPosition:'top',
            prompt:"Description Box",
            panelHeight:'auto',
            valueField:"value",
            textField:"text",
            editable:false,
            data:[
                {value:"0", text:"Hide Description Box"},
                {value:"1", text:"Show Description Box"}
            ],
        })


        $eu('#i<?php echo e($_idbf_); ?>unit').textbox({
            required:true,
            label:"Unit",
            labelPosition:'top',
            prompt:"Unit"
        });
        $eu('#i<?php echo e($_idbf_); ?>ref_m').textbox({
            required:true,
            label:"Normal Value (Male)",
            labelPosition:'top',
            prompt:"Normal Value (Male)"
        });
        $eu('#i<?php echo e($_idbf_); ?>ref_f').textbox({
            required:true,
            label:"Normal Value (Female)",
            labelPosition:'top',
            prompt:"Normal Value (Female)"
        });
        euDoLayout();
    })

    function jf<?php echo e($_idbf_); ?>dGridSave(){
        preloader_block();

        var inputType = $eu('#i<?php echo e($_idbf_); ?>input_type').combobox('getValue');
        var inputDefault = "";
        if(inputType == "TEXT"){
            inputDefault = $eu('#i<?php echo e($_idbf_); ?>input_default__TEXT').textbox('getValue');
        }else if(inputType == "PARAGRAPH"){
            inputDefault = $eu('#i<?php echo e($_idbf_); ?>input_default__PARAGRAPH').textbox('getValue');
        }else if(inputType == "NUMBER"){
            inputDefault = $eu('#i<?php echo e($_idbf_); ?>input_default__NUMBER').numberbox('getValue');
        }else if(inputType == "RANGE_NUMBER"){
            inputDefault += $eu('#i<?php echo e($_idbf_); ?>input_default__RANGEFROM').numberbox('getValue');
            inputDefault += "|";
            inputDefault += $eu('#i<?php echo e($_idbf_); ?>input_default__RANGETO').numberbox('getValue');
        }else if(inputType == "OPTION"){
            inputDefault = $eu('#i<?php echo e($_idbf_); ?>input_default__OPTION').textbox('getValue');
        }else if(inputType == "IMAGE"){
            inputDefault = $eu('#i<?php echo e($_idbf_); ?>input_default__IMAGE').filebox('getValue');
        }
        $eu('#i<?php echo e($_idbf_); ?>input_default').textbox('setValue', inputDefault);

        // return true;


        $eu("#cp<?php echo e($_idbf_); ?>dGridForm").form("submit", {
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
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('reload');
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('clearSelections');
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('clearChecked');
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
        $eu(function(){
            $eu('#i<?php echo e($_idbf_); ?>input_type').combobox('setValue','TEXT');
            $eu('#i<?php echo e($_idbf_); ?>input_desc').combobox('setValue','0');
            setTimeout(() => {
                $eu('#i<?php echo e($_idbf_); ?>code').textbox('textbox').focus();
            }, 500);
        })
    </script>
<?php } else if(@$mode == 'edit') { ?>
    <script>
        $eu(function(){
            var selData = <?php echo $selData; ?>;
            $eu("#cp<?php echo e($_idbf_); ?>dGridForm").form('load', selData);
            // $eu('#i<?php echo e($_idbf_); ?>code').textbox('disable',true);

            if(selData.input_type == "TEXT"){
                $eu('#i<?php echo e($_idbf_); ?>input_default__TEXT').textbox('setValue', selData.input_default);
            }else if(selData.input_type == "PARAGRAPH"){
                $eu('#i<?php echo e($_idbf_); ?>input_default__PARAGRAPH').textbox('setValue', selData.input_default);
            }else if(selData.input_type == "NUMBER"){
                $eu('#i<?php echo e($_idbf_); ?>input_default__NUMBER').numberbox('setValue', selData.input_default);
            }else if(selData.input_type == "RANGE_NUMBER"){
                var inputDefault = selData.input_default;
                var inputDefaultSplit = inputDefault.split("|");
                $eu('#i<?php echo e($_idbf_); ?>input_default__RANGEFROM').numberbox('setValue', inputDefaultSplit[0]);
                $eu('#i<?php echo e($_idbf_); ?>input_default__RANGETO').numberbox('setValue', inputDefaultSplit[1]);
            }else if(selData.input_type == "OPTION"){
                $eu('#i<?php echo e($_idbf_); ?>input_default__OPTION').textbox('setValue', selData.input_default);
                i<?php echo e($_idbf_); ?>input_default__OPTION_dGrid_data();
            }else if(selData.input_type == "IMAGE"){
                // $eu('#i<?php echo e($_idbf_); ?>input_default__IMAGE').filebox('setValue', selData.input_default);
            }


            setTimeout(() => {
                $eu('#i<?php echo e($_idbf_); ?>code').textbox('textbox').focus();
            }, 500);
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp<?php echo e($_idbf_); ?>dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?><?php /**PATH /home/u1598413/public_sys/dkdmcu/app/Modules/MCU2Format/v2FormInput.blade.php ENDPATH**/ ?>