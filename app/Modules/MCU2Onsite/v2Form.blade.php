<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Add New Patient</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idb_}}dGridSave()">Create</button>
        <!-- <button class="btn btn-outline-primary" type="button" onclick="jf{{$_id_}}dGridAdd()">Open </button> -->
    </cpModalFooter>
<?php } else if(@$mode == 'edit') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Edit Patient</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idb_}}dGridSave()">Update</button>
    </cpModalFooter>
<?php } else if(@$mode == 'present') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Update Status</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idb_}}dGridSave()">Present</button>
    </cpModalFooter>
<?php } else if(@$mode == 'updateStatus') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Update Status</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idb_}}dGridSave()">Update Status</button>
    </cpModalFooter>
<?php } else if(@$mode == 'updateResult') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>UPDATE RESULT</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idb_}}dGridSave()">Submit Result</button>
    </cpModalFooter>
<?php } ?>

<div id="cp{{$_idb_}}dGridFormMainContent">

    <div class="row">
        <div class="col-12 col-md-12 mb-3">
            <center>
            <label class="fs-6 fw-bold mb-2">Photo Shoot</label>
            <div id="my_camera" style="width:100%"></div>
            </center>
        </div>
        <div class="col-12 col-md-12 mb-3">
            <center>
            <button onclick="jf{{$_idb_}}_take_photo()" class="btn btn-success btn-sm me-2"><i class="fas fa-plus"></i> Take Photo</button>
            <button onclick="jf{{$_idb_}}_reset_photo()" class="btn btn-danger btn-sm me-2"><i class="fas fa-trash"></i> Reset Photo</button>
            </center>
        </div>
        <div class="col-12 col-md-12 mb-3">
            <center>
            <label class="fs-6 fw-bold mb-2">Photo Result</label>
            <div style="width:100%">
                <img id="photo_data_uri" src=""/>
            </div>
            <input type="hidden" name="patient_photo" id="patient_photo">
            <input type="hidden" name="patient_photo_new" id="patient_photo_new" value=0>
            <div id="patient_photo_error" class='form_error'></div>
            </center>
        </div>
    </div>
    <form id="cp{{$_idb_}}dGridFormMain" method="post" enctype="multipart/form-data">
        @csrf
        <?php if(@$mode == 'edit' || @$mode == 'present' || @$mode == 'updateStatus' || @$mode == 'updateResult') { ?>
            @method('PUT')
        <?php } ?>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <input class="" type="text" id="unique" name="unique" style="width:100%">
                <div style="color:blue"><i><small>* Unique for every patient in this event.</small></i></div>
                <div style="color:blue"><i><small>* You Can Use (Lab. Number / KTP / Passport / Empoyee Number)</small></i></div>
                <div id="unique_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
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
        </div>
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="schedule_date" name="schedule_date" style="width:100%">
                <div id="schedule_date_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <select class="" id="mcu2_format_package_code" name="mcu2_format_package_code" style="width:100%"></select>
                <div id="mcu2_format_package_code_error" class='form_error'></div>
            </div>
        </div>

        <br><br>
        <div class="row">
            <div class="col-12 col-md-12">
                <center>
                <label class="fs-6 fw-bold mb-2 txt-primary">ADDITIONAL INFORMATION</label>
                </center>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="" type="text" id="ktp" name="ktp" style="width:100%">
                <div id="ktp_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="phone" name="phone" style="width:100%">
                <div id="phone_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="email" name="email" style="width:100%">
                <div id="email_error" class='form_error'></div>
            </div>
            <div class="col-12 mb-3">
                <input class="" type="text" id="address" name="address" style="width:100%">
                <div id="address_error" class='form_error'></div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="emp_no" name="emp_no" style="width:100%">
                <div id="emp_no_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="emp_area" name="emp_area" style="width:100%">
                <div id="emp_area_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="emp_div" name="emp_div" style="width:100%">
                <div id="emp_div_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="emp_pos" name="emp_pos" style="width:100%">
                <div id="emp_pos_error" class='form_error'></div>
            </div>
        </div>


        <?php if(@$mode == 'present') { ?>
            </br><br>
            <div class="row">
                <div class="col-12 col-md-12">
                    <center>
                    <label class="fs-6 fw-bold mb-2 txt-primary">PRESENT</label>
                    </center>
                </div>
                <div class="col-12 col-md-12 mb-3">
                    <input class="" type="number" id="forcePresentNum" name="forcePresentNum" style="width:100%">
                    <div id="forcePresentNum_error" class='form_error'></div>
                </div>
                <div class="col-12 col-md-12 mb-3">
                    <input class="" type="text" id="status" name="status" style="width:100%">
                    <div id="status_error" class='form_error'></div>
                </div>
                <div class="col-12 col-md-12 mb-3">
                    <input class="" type="text" id="status_note" name="status_note" style="width:100%">
                    <div id="status_note_error" class='form_error'></div>
                </div>
            </div>
        <?php } ?>
        <?php if(@$mode == 'updateStatus') { ?>
            </br><br>
            <div class="row">
                <div class="col-12 col-md-12">
                    <center>
                    <label class="fs-6 fw-bold mb-2 txt-primary">UPDATE STATUS</label>
                    </center>
                </div>
                <div class="col-12 col-md-12 mb-3">
                    <input class="" type="text" id="status" name="status" style="width:100%">
                    <div id="status_error" class='form_error'></div>
                </div>
                <div class="col-12 col-md-12 mb-3">
                    <input class="" type="text" id="status_note" name="status_note" style="width:100%">
                    <div id="status_note_error" class='form_error'></div>
                </div>
            </div>
        <?php } ?>

        <?php if(@$mode == 'updateResult') { ?>
        </br>
        <center><h5><b>MCU RESULT</b></h5></center>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <input class="" type="text" id="status" name="status" style="width:100%">
                <div id="status_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="" type="text" id="fit_date" name="fit_date" style="width:100%">
                <div id="fit_date_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="" type="text" id="fit_cate" name="fit_cate" style="width:100%">
                <div id="fit_cate_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="" type="text" id="fit_note" name="fit_note" style="width:100%">
                <div id="fit_note_error" class='form_error'></div>
            </div>
        </div>
        <?php } ?>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#emp_no').textbox({
            required:false,
            label:"Emp. Number",
            labelPosition:'top',
            prompt:"Emp. Number",
        })
        $eu('#emp_area').textbox({
            required:false,
            label:"Job Area",
            labelPosition:'top',
            prompt:"Job Area"
        })
        $eu('#emp_div').textbox({
            required:false,
            label:"Job Division",
            labelPosition:'top',
            prompt:"Job Division"
        })
        $eu('#emp_pos').textbox({
            required:false,
            label:"Job Position",
            labelPosition:'top',
            prompt:"Job Position"
        })


        $eu('#unique').textbox({
            required:true,
            label:"Unique Code",
            labelPosition:'top',
            prompt:"Unique Code"
        })
        $eu('#ktp').textbox({
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
        $eu('#email').textbox({
            required:false,
            label:"Email",
            labelPosition:'top',
            prompt:"Email"
        });
        $eu('#address').textbox({
            required:false,
            label:"Address",
            labelPosition:'top',
            prompt:"Address",
            multiline:true,
            height:100
        });
        $eu('#schedule_date').datebox({
            required:false,
            label:"MCU Schedule",
            labelPosition:'top',
            prompt:"dd/mm/yyyy",
            formatter:datebox_formatter_ddmmyyyy,
            parser:datebox_parser_ddmmyyyy,
            value:' '
        });
        $eu('#mcu2_format_package_code').combobox({
            required:true,
            label:"MCU Package",
            labelPosition:'top',
            prompt:"Choose MCU Package",
            method: "GET",
            // url:'{{$urlb}}readMcu2FormatPackageCombo',
            editable:false,
            valueField:'code',
            textField:'title_mod',
            panelHeight:150
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
                {id:'sPresented',title:"PRESENT", selected:true},
                {id:'sCanceled',title:"CANCEL"},
                {id:'sHalfFinished',title:"HALF FINISH"},
                {id:'sFinished',title:"FINISH"},
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

        $eu('#forcePresentNum').textbox({
            required:false,
            label:"FORCE PRESENT NUMBER",
            labelPosition:'top',
            prompt:"set present number OR keep blank to automaticaly"
        })
        euDoLayout();
    })

    function jf{{$_idb_}}dGridSave(){
        preloader_block();
        $eu("#cp{{$_idb_}}dGridFormMain").form("submit", {
            url: "{{ @$url_save }}",
            queryParams:{
                patient_photo:$("#patient_photo").val(),
                patient_photo_new:$("#patient_photo_new").val()
            },
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

<script src="{{ asset('assets') }}/webcam.min.js"></script>
<script language="JavaScript">
    Webcam.set({
        width: 320,
        height: 240,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
    Webcam.attach('#my_camera');

    function jf{{$_idb_}}_take_photo(){
        Webcam.snap(function (photo_data_uri) {
            $("#photo_data_uri").attr('src', photo_data_uri);
            $("#patient_photo").val(photo_data_uri);
            $("#patient_photo_new").val(1);
        });
    }
    function jf{{$_idb_}}_reset_photo(){
        <?php if(@$mode == 'add') { ?>
            $("#photo_data_uri").attr('src', '');
            $("#patient_photo").val('');
        <?php } else if(@$mode == "edit" || @$mode == "present") { ?>
            var selData = {!! $selData !!};
            $("#photo_data_uri").attr('src', selData.patient_photo);
            $("#patient_photo").val(selData.patient_photo);
        <?php } ?>
        $("#patient_photo_new").val(0);
    }
</script>

<?php if(@$mode == 'add') { ?>
    <!-- no action -->
    <script>
        $(function(){
            $eu('#mcu2_format_package_code').combobox({
                url:'{{$url}}readMcu2FormatPackageComboo?selectFirst=0&mcu_format_id={{ $eventData->mcu2_format_id }}',
            });
            setTimeout(() => {
                $eu('#unique').textbox('textbox').focus();
            }, 500);
        })
    </script>
<?php } else if(@$mode == 'edit' || @$mode == 'present' || @$mode == 'updateStatus') { ?>
    <script>
        $(function(){
            var selData = {!! $selData !!};

            $eu('#mcu2_format_package_code').combobox({
                url:'{{$url}}readMcu2FormatPackageComboo?selectFirst=0&mcu_format_id={{ $eventData->mcu2_format_id }}',
            });

            $("#photo_data_uri").attr('src', selData.patient_photo);
            $("#patient_photo").val(selData.patient_photo);

            $eu("#cp{{$_idb_}}dGridFormMain").form('load', selData);
            $eu('#dob').datebox('setValue', selData.dob_dmY_slash);
            $eu('#schedule_date').datebox('setValue', selData.schedule_date_dmY_slash);
            
            <?php if(@$mode == 'edit') { ?>
                setTimeout(() => {
                    $eu('#unique').textbox('textbox').focus();
                }, 500);
            <?php } ?>

            <?php if(@$mode == 'present') { ?>
                $eu('#status').combobox({
                    data:[
                        {id:'sPresented',title:"PRESENT", selected:true}
                    ]
                });
                $eu('#status_note').textbox({
                    require:false
                })
                setTimeout(() => {
                    $eu('#status_note').textbox('textbox').focus();
                }, 500);
            <?php } ?>

            <?php if(@$mode == 'updateStatus') { ?>
                $eu('#status').combobox({
                    data:[
                        {id:'sCanceled',title:"CANCEL"},
                        {id:'sHalfFinished',title:"HALF FINISH"},
                        {id:'sFinished',title:"FINISH"}
                    ]
                });
                $eu('#status_note').textbox({
                    require:false
                })
                setTimeout(() => {
                    $eu('#status_note').textbox('textbox').focus();
                }, 500);
            <?php } ?>
        })
    </script>
<?php } else if(@$mode == 'updateResult') { ?>
    <script>
        $(function(){
            var selData = {!! $selData !!};
            $eu("#cp{{$_idb_}}dGridFormMain").form('load', selData);
            $eu('#dob').datebox('setValue', selData.dob_dmY_slash);
            $eu('#schedule_date').datebox('setValue', selData.schedule_date_dmY_slash);
            $eu('#actual_date').datebox('setValue', selData.actual_date_dmY_slash);

            $eu('#status').combobox('setValue','sFinished');
            $eu('#fit_date').datebox('setValue', selData.fit_date_dmY_slash);
            if(selData.fit_date_dmY_slash == null){
                $eu('#fit_date').datebox('setValue', " ");
            }
            setTimeout(() => {
                $eu('#fit_cate').textbox('textbox').focus();
            }, 500);
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp{{$_idb_}}dGridFormMainContent").html("Request not valid.");
        })
    </script>
<?php } ?>