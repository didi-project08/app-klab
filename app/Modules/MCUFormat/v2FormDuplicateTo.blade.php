<cpModalSize>modal-md</cpModalSize>
<cpModalTitle>Duplicate To</cpModalTitle>
<cpModalFooter>
    <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
    <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idbf_}}dGridSave()">Update</button>
</cpModalFooter>

<div id="cp{{$_idbf_}}dGridFormContent">
    <form id="cp{{$_idbf_}}dGridForm" method="post" enctype="multipart/form-data">
        @csrf
        <div style="height:300px">
            <table id="cp{{$_idb_}}dGridMCUFormat"></table>
        </div>
        <br>
        <div id="idTo_error" class='form_error' style="font-size:120%"></div>
    </form>
</div>

<script>
    $eu(function(){
        $eu("#cp{{$_idb_}}dGridMCUFormat").datagrid({
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
            url: "{{ $urlb }}readMCUFormat",
            frozenColumns: [[
                
            ]],
            columns: [[
                {field: "laboratory_name",title: "<b>Provider</b>",align: "left",width: 200},
                {field: "title",title: "<b>Format Name</b>",align: "left",width: 300},
            ]],
            rowStyler: function(index, row){
                return 'color:black;';
            },
            onLoadSuccess:function(){
                $eu("#cp{{$_idb_}}dGridMCUFormat").datagrid('clearSelections');
                $eu("#cp{{$_idb_}}dGridMCUFormat").datagrid('clearChecked');
            }            
        })
        euDoLayout();
    })

    function jf{{$_idbf_}}dGridSave(){
        var sel = $eu("#cp{{$_idb_}}dGridMCUFormat").datagrid('getSelected');
        if(sel){
            if(sel.id != {{$mcuFormatData->id}}){
                preloader_block();
                $eu("#cp{{$_idbf_}}dGridForm").form("submit", {
                    url: "{{ @$url_save }}",
                    queryParams:{
                        idTo:sel.id
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
                            $eu("#cp{{$_idb_}}dGrid").treegrid('reload');
                            $eu("#cp{{$_idb_}}dGrid").treegrid('clearSelections');
                            $eu("#cp{{$_idb_}}dGrid").treegrid('clearChecked');
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
                    text: 'Please select other data',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 2000);
            }
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one row to Duplicate',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 2000);
        }
    }
</script>