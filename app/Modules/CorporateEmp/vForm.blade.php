<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Add New Employee</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_id_}}dGridSave()">Create</button>
    </cpModalFooter>
<?php } else if(@$mode == 'edit') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Edit Employee</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_id_}}dGridSave()">Update</button>
    </cpModalFooter>
<?php } ?>

<div id="cp{{$_id_}}dGridFormContent">
    <form id="cp{{$_id_}}dGridForm" method="post" enctype="multipart/form-data">
        @csrf
        <?php if(@$mode == 'edit') { ?>
            @method('PUT')
        <?php } ?>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="corporate_id" name="corporate_id" style="width:100%"></select>
                <div id="corporate_id_error" class='form_error'></div>
            </div>
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
            // url:'{{$url}}readCorporateCombo',
            editable:false,
            valueField:'id',
            textField:'title',
            panelHeight:150
        });
        $eu('#emp_no').textbox({
            required:false,
            label:"Emp. Number",
            labelPosition:'top',
            prompt:"Emp. Number",
        })
        setTimeout(() => {
            $eu('#emp_no').textbox('textbox').focus();
        }, 500);
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
            label:"ID (KTP / Passport)",
            labelPosition:'top',
            prompt:"ID (KTP / Passport)"
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
            label:"Date of birth (dd/mm/yyyy)",
            labelPosition:'top',
            prompt:"dd/mm/yyyy",
            formatter:datebox_formatter_ddmmyyyy,
            parser:datebox_parser_ddmmyyyy

        });
        $eu('#phone').textbox({
            required:true,
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
        euDoLayout();
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

<?php if(@$mode == 'add') { ?>
    <!-- no action -->
    <script>
        $(function(){
            $eu('#corporate_id').combobox("reload",'{{$url}}readCorporateCombo?selectFirst=1');
        })
    </script>
<?php } else if(@$mode == 'edit') { ?>
    <script>
        $(function(){
            $eu('#corporate_id').combobox("reload",'{{$url}}readCorporateCombo');
            var selData = {!! $selData !!};
            $eu("#cp{{$_id_}}dGridForm").form('load', selData);
            $eu('#corporate_id').textbox('disable',true);
            $eu('#dob').datebox('setValue', selData.dob_dmY_slash);
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp{{$_id_}}dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?>