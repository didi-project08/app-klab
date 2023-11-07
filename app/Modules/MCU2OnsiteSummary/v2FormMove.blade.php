<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Move Patient to Other Event</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idbf_}}dGridSave()">Submit</button>
    </cpModalFooter>
<?php } ?>

<div id="cp{{$_idbf_}}dGridFormContent">
    <form id="cp{{$_idbf_}}dGridForm" method="post" enctype="multipart/form-data">
        @csrf
        <?php if(@$mode == 'edit' || @$mode == 'present') { ?>
            @method('PUT')
        <?php } ?>
        <div style="height:300px">
            <table id="cp{{$_idbf_}}FormMovedGrid"></table>
        </div>
        <br>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="cp{{$_idbf_}}mcu_format_package_id" name="mcu_format_package_id" style="width:100%"></select>
                <div id="mcu_format_package_id_error" class='form_error'></div>
            </div>
        </div>
        <br>
        <div id="otherMCUEventId_error" class='form_error' style="font-size:120%"></div>
        <div id="patientIdList_error" class='form_error' style="font-size:120%"></div>
    </form>
</div>

<script>
    $eu(function(){
        $eu("#cp{{$_idbf_}}FormMovedGrid").datagrid({
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
            url: "{{ $urlb }}readMCUEvent?corporate_id={{ $eventData->corporate_id }}",
            frozenColumns: [[
                
            ]],
            columns: [[
                {field: "eventnum",title: "<b>Number</b>",align: "center",width: 80},
                // {field: "corporate_name",title: "<b>Corporate</b>",align: "left",width: 150},
                // {field: "corporate_client_name",title: "<b>Client</b>",align: "left",width: 150},
                {field: "corporateClientName",title: "<b>Corporate</b>",align: "left",width: 200},
                // {field: "laboratory_name",title: "<b>Provider (Lab)</b>",align: "left",width: 150},
                // {field: "mcu_format_name",title: "<b>Format</b>",align: "left",width: 150},
                {field: "laboratoryFormatName",title: "<b>Provider (Lab)</b>",align: "left",width: 200},
                {field: "title",title: "<b>Project Title</b>",align: "left",width: 250},
                {field: "date_from_dmY_slash",title: "<b>From</b>",align: "center",width: 100},
                {field: "date_to_dmY_slash",title: "<b>To</b>",align: "center",width: 100},
                {field: "status_name",title: "<b>Status</b>",align: "center",width: 100},
                {field: "confirm_by",title: "<b>Confirm By</b>",align: "center",width: 100},
                {field: "confirm_at",title: "<b>Confirm At</b>",align: "center",width: 100},
                {field: "confirm_note",title: "<b>Confirm Note</b>",align: "center",width: 200},
                {field: "owner_name",title: "<b>Owner</b>",align: "center",width: 100},
            ]],
            rowStyler: function(index, row){
                return 'color:'+row.status_color;
            },
            onSelect:function(index,row){
                $eu("#cp{{$_idbf_}}mcu_format_package_id").combobox({url:'{{$urlb}}readFormatPackageCombo?mcu_format_id='+row.mcu_format_id});
            }
        })
        $eu('#cp{{$_idbf_}}mcu_format_package_id').combobox({
            required:true,
            label:"Pakcage",
            labelPosition:'top',
            prompt:"Choose Package",
            method: "GET",
            // url:'{{$urlb}}readFormatPackageCombo',
            editable:false,
            valueField:'id',
            textField:'title',
            panelHeight:105,
            onLoadSuccess:function(){
                $eu('#cp{{$_idbf_}}mcu_format_package_id').combobox('setValue','');
            }
        });
        euDoLayout();
    })

    function jf{{$_idbf_}}dGridSave(){
        let sel = $eu("#cp{{$_idbf_}}FormMovedGrid").datagrid('getSelected');
        if(sel){
            let checkedData = $eu("#cp{{$_idb_}}dGrid").datagrid('getChecked'); 
            let patientIdList = [];
            for (let i = 0; i < checkedData.length; i++) {
                patientIdList.push(checkedData[i].id);
            }

            preloader_block();

            $eu("#cp{{$_idbf_}}dGridForm").form("submit", {
                url: "{{ @$url_save }}",
                onSubmit: function() {
                    if(!$eu(this).form('validate')){
                        preloader_none();
                        return $eu(this).form('validate');
                    }
                },
                queryParams : { 
                    otherMCUEventId : sel.id,
                    patientIdList : patientIdList
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
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: "Please Select Other MCU Project.",
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }        
    }
</script>

<?php if(@$mode == 'add') { ?>
    <!-- no action -->
<?php } else if(@$mode == 'edit' || @$mode == 'present') { ?>
    <!-- no action -->
<?php } else { ?>
    <script>
        $(function(){
            $("#cp{{$_idbf_}}dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?>