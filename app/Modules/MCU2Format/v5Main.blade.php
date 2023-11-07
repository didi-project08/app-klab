<div id="cp{{$_idb_}}dGridToolbar">
    <div class="row p-3">
        <div class="col-12">
            <?php if(1==1 || isset($userScope['roleAction']['/create'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_idb_}}dGridAdd()"><i class="fas fa-add fa-fw"></i> Add</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/create'])) { ?>
                <button class="mb-1 btn btn-outline-secondary" onclick="jf{{$_idb_}}dGridEdit()"><i class="fas fa-edit fa-fw"></i> Edit</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/delete'])) { ?>
                <button class="mb-1 btn btn-outline-danger" onclick="jf{{$_idb_}}dGridDelete()"><i class="fas fa-trash fa-fw"></i> Delete</button>
            <?php } ?>

            <?php if(1==1 || isset($userScope['roleAction']['/delete'])) { ?>
                <button class="mb-1 btn btn-outline-warning" onclick="jf{{$_idb_}}dGridShowImage()"><i class="fas fa-eye fa-fw"></i> Show Image</button>
            <?php } ?>
        </div>
        <div class="col-12">
            <div style="float:right">
                <input type="text" id="cp{{$_idb_}}dGridFilterSrcEvt_front">
                <button class="btn btn-outline-primary" onclick="jf{{$_idb_}}dGridFilter_o()"><i class="fas fa-filter fa-fw"></i> Filter</button>
            </div>
        </div>
    </div>
</div>
<table id="cp{{$_idb_}}dGrid"></table>


<script>
    $eu(function(){
        $eu("#cp{{$_idb_}}dGrid").datagrid({
            toolbar: "#cp{{$_idb_}}dGridToolbar",
            border:true,
            striped: true,
            pagination: true,
            fit: true,
            fitColumns: true,
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
            // url: "{{ $urlb }}read",
            frozenColumns: [[
                
            ]],
            columns: [[
                {field: "id",title: "<b>ID</b>",align: "center",width: 100},
                {field: "name",title: "<b>Fullname</b>",align: "left",width: 300},
                {field: "str_number",title: "<b>STR Number</b>",align: "left",width: 200},
                {field: "sip_number",title: "<b>SIP Number</b>",align: "left",width: 200},
            ]],
            rowStyler: function(index, row){
                return 'color:black;';
            },
            onDblClickRow:function(){
                jf{{$_idb_}}dGridShowImage();
            }
        })

        $eu("#cp{{$_idb_}}dGridFilterSrcEvt_front").textbox({
            prompt:"Type to search.",
            onChange:function(){
                jf{{$_idb_}}dGridFilterFront_p();
            }
        })
        jf{{$_idb_}}dGridFilter_i();
    })

    function jf{{$_idb_}}dGridFilter_i(){
        let cpModalId = "cpModal{{$_idb_}}DGridFilter";
        let cpModalUrl = "{{$urlb}}filter";
        cpGlobalModal(cpModalId, cpModalUrl);
        var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
    }
    function jf{{$_idb_}}dGridFilter_o(){
        let cpModalId = "cpModal{{$_idb_}}DGridFilter";
        cpGlobalModalOpen(cpModalId);
    }
    function jf{{$_idb_}}dGridFilter_g(){
        var srcEvt = $eu("#cp{{$_idb_}}dGridFilterSrcEvt").textbox('getValue');
        $eu("#cp{{$_idb_}}dGridFilterSrcEvt_front").textbox('setValue',srcEvt);
        var filterParams = $("#cp{{$_idb_}}dGridFilterForm").serialize();

        var sel = $eu("#cp{{$_id_}}b1dGrid").treegrid('getSelected');
        var dGridId = 0;
        if(sel){
            dGridId = sel.id;
        }
        filterParams = filterParams+"&mcu_format_id="+dGridId;

        return filterParams;
    }
    function jf{{$_idb_}}dGridFilter_p(){
        var filterParams = jf{{$_idb_}}dGridFilter_g();
        $eu("#cp{{$_idb_}}dGrid").datagrid("load", "{{ $urlb }}read/?"+filterParams);
        
        let cpModalId = "cpModal{{$_idb_}}DGridFilter";
        cpGlobalModalClose(cpModalId);
    }
    function jf{{$_idb_}}dGridFilter_r(){
        $eu("#cp{{$_idb_}}dGridFilterForm").form('clear');
        // jf{{$_idb_}}dGridFilter_p();
    }
    function jf{{$_idb_}}dGridFilterFront_p(){
        var srcEvt = $eu("#cp{{$_idb_}}dGridFilterSrcEvt_front").textbox('getValue');
        $eu("#cp{{$_idb_}}dGridFilterSrcEvt").textbox('setValue',srcEvt);
        jf{{$_idb_}}dGridFilter_p();
    }

    function jf{{$_idb_}}dGridAdd(){
        let cpModalId = "cpModal{{$_idb_}}DGridForm";
        let cpModalUrl = "{{$urlb}}add";
        cpGlobalModal(cpModalId, cpModalUrl);
        var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
        cpModal.show();
    }   

    function jf{{$_idb_}}dGridShowImage(){
        let sel = $eu("#cp{{$_idb_}}dGrid").datagrid('getSelected');
        if(sel){
            let cpModalId = "cpModal{{$_idb_}}DGridForm";
            let cpModalUrl = encodeURI("{{$urlb}}showImage/"+sel.code);
            cpGlobalModal(cpModalId, cpModalUrl);
            var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
            cpModal.show();
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one row to Show Image',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }

    function jf{{$_idb_}}dGridDelete(){
        let sel = $eu("#cp{{$_idb_}}dGrid").datagrid('getSelected');
        if(sel){
            Swal.fire({
                // width:'300px',
                customClass: {
                    confirmButton: 'btn btn-outline-primary',
                    cancelButton: 'btn btn-outline-danger'
                },
                buttonsStyling: false,
                icon: 'warning',
                title: 'Are you sure ?',
                text: 'DELETE data '+sel.code+' ?',
                showCancelButton:true,
                showConfirmButton:true
            }).then((result) => {
                if(result.isConfirmed){
                    jf{{$_idb_}}dGridDeleteGo();
                }
            })
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one row to DELETE',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf{{$_idb_}}dGridDeleteGo(){
        let sel = $eu("#cp{{$_idb_}}dGrid").datagrid('getSelected');
        preloader_block();
        $.ajax({
            type: 'DELETE',
            dataType: "JSON",
            url:encodeURI("{{ $urlb }}delete/"+sel.code),
            data:{
                "_token": "{{ csrf_token() }}"
            },
            success: function(r) {
                preloader_none();
                if (r.success) {
                    Swal.fire({
                        width:'300px',
                        icon: 'success',
                        title: 'Success',
                        text: r.message,
                        showConfirmButton:false
                    })
                    setTimeout(() => { Swal.close(); }, 1000);

                    $eu("#cp{{$_idb_}}dGrid").datagrid('reload');
                    $eu("#cp{{$_idb_}}dGrid").datagrid('clearSelections');
                    $eu("#cp{{$_idb_}}dGrid").datagrid('clearChecked');
                }else{
                    Swal.fire({
                        width:'300px',
                        icon: 'error',
                        title: 'Oops...',
                        text: r.message,
                        showConfirmButton:false
                    })
                    setTimeout(() => { Swal.close(); }, 3000);
                }
            },
            error:function(){
                preloader_none();
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Request error, please try again.',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 3000);
            }
        })
    }

    function jf{{$_idb_}}dGridExport(){
        var filterParams = jf{{$_idb_}}dGridFilter_g();
        window.open("{{ $urlb }}export/?"+filterParams);
    }

    function jf{{$_idb_}}dGridImport(){
        let cpModalId = "cpModal{{$_idb_}}DGridForm";
        let cpModalUrl = "{{$urlb}}import";
        cpGlobalModal(cpModalId, cpModalUrl);
        var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
        cpModal.show();
    }
</script>