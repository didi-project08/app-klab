<cpModalSize>modal-md</cpModalSize>
<cpModalTitle>Import</cpModalTitle>
<cpModalFooter>
    <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
    <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idb_}}dGridSave()">Submit</button>
</cpModalFooter>

<div id="cp{{$_idb_}}dGridFormImportContent">
    <form id="cp{{$_idb_}}dGridFormImport" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <div style="float:right">
                    <a class="btn btn-outline-success" href="{{$urlb}}importTemplate" target="_blank"><i class="fas fa-file-excel fa-fw"></i>Download Template</a>
                </div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="corporate_id" name="corporate_id" style="width:100%">
                <div id="corporate_id_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="" type="text" id="cp{{$_idb_}}file" name="file" style="width:100%">
                <div id="file_error" class='form_error'></div>
                <br>
                <div id="file_error_excel" class='form_error' style="max-height:100px; overflow-y:auto; border:solid red thin; padding:3px;"></div>
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
        $eu('#cp{{$_idb_}}file').filebox({
            required:true,
            label:"File (Only .xlsx)",
            labelPosition:'top',
            buttonText: 'Choose File',
            buttonAlign: 'left'
        });
        euDoLayout();
    })

    $eu(function(){
        $eu('#corporate_id').combobox("reload",'{{$urlb}}readCorporateCombo?selectFirst=1');
    })

    function jf{{$_idb_}}dGridSave(){
        preloader_block();

        $eu("#cp{{$_idb_}}dGridFormImport").form("submit", {
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
                    setTimeout(() => { Swal.close(); }, 3000);

                    if (r.excel_error) {

                    }else{
                        cpGlobalModalClose("{{$cpModalId}}");
                        $eu("#cp{{$_idb_}}dGrid").datagrid('reload');
                        $eu("#cp{{$_idb_}}dGrid").datagrid('clearSelections');
                        $eu("#cp{{$_idb_}}dGrid").datagrid('clearChecked');
                    }
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

                    if (r.excel_error) {
                        var excel_error_array = r.excel_error_array;
                        $("#file_error_excel").append("EXCEL VALIDATION<br>");
                        for (key in excel_error_array) {
                            var excelRow = excel_error_array[key].excelRow;
                            var excelValidation = excel_error_array[key].excelValidation;
                            $("#file_error_excel").append("<br><u>Excel Row "+excelRow+"</u>");
                            for (key2 in excelValidation) {
                                $("#file_error_excel").append("<br>"+key2+" : ");
                                for (key3 in excelValidation[key2]) {
                                    if(key3 == 0){
                                        $("#file_error_excel").append(excelValidation[key2][key3]);
                                    }else{
                                        $("#file_error_excel").append(" | "+excelValidation[key2][key3]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        })
    }
</script>