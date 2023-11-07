<div id="cp{{$_idb_}}dGridToolbar">
    <div class="row p-3">
        <div class="col-12 col-md-12">
            <?php if(isset($userScope['roleAction']['/b4/roleModuleAccessUpdate'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_idb_}}dGridRoleModuleAccessUpdate()"><i class="fas fa-add fa-fw"></i> Save</button>
            <?php } ?>
        </div>
        <div class="col-12 col-md-6 d-none">
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
        $eu("#cp{{$_idb_}}dGrid").treegrid({
            toolbar: "#cp{{$_idb_}}dGridToolbar",
            fit:true,
            pagination:true,
            rownumbers: true,
            checkbox: true,
            method: "GET",
            idField: "id",
            treeField: "name",
            // url: "{{ $urlb }}read",
            columns: [[
                {field: "name",title: "<b>Module Access</b>",align: "left",width: 350},
                {field: "f_module_access",title: "<b>URL</b>",align: "left",width: 200},
                {field: "f_xml_http_request",title: "<b>HTTP</b>",align: "center",width: 100},
            ]],
            // onBeforeLoad:function(){
            //     $eu("#cp{{$_idb_}}dGrid").treegrid('loadData'{});
            // },
            onLoadSuccess:function(row, data){
                for (let i = 0; i < data.length; i++) {
                    $eu("#cp{{$_idb_}}dGrid").treegrid("uncheckNode", data[i].id);
                }
                var idList = data[0].idList;
                var rowsChecked = data[0].rowsChecked;
                for (let i = 0; i < rowsChecked.length; i++) {
                    if(idList.hasOwnProperty(rowsChecked[i].f_module_access_id)){
                        $eu("#cp{{$_idb_}}dGrid").treegrid("checkNode", rowsChecked[i].f_module_access_id);
                    }
                }
            },
            onCheckNode:function(row,checked){
                // console.log($eu("#cp{{$_idb_}}dGrid").treegrid('getCheckedNodes'));
            },
            onSelect:function(row){
                // $eu("#cp{{$_idb_}}dGrid").treegrid('checkNode', row.id);
            },
            onUnselect:function(row){
                // $eu("#cp{{$_idb_}}dGrid").treegrid('uncheckNode', row.id);
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

        var sel = $eu("#cp{{$_id_}}b3dGrid").treegrid('getSelected');
        var dGridId = 0;
        var dGridId2 = 0;
        if(sel){
            dGridId = sel.corporate_id;
            dGridId2 = sel.f_role_id;
        }
        filterParams = filterParams+"&corporate_id="+dGridId+"&f_role_id="+dGridId2;

        return filterParams;
    }
    function jf{{$_idb_}}dGridFilter_p(){
        var filterParams = jf{{$_idb_}}dGridFilter_g();
        $eu("#cp{{$_idb_}}dGrid").treegrid({
            url:"{{ $urlb }}read/?"+filterParams
        });
        
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

    function jf{{$_idb_}}dGridRoleModuleAccessUpdate(){
        let sel = $eu("#cp{{$_id_}}b3dGrid").treegrid('getSelected');
        if(sel){
            let checkedData = $eu("#cp{{$_idb_}}dGrid").treegrid('getCheckedNodes');
            // if(checkedData.length > 0){
                let roleModuleIdList = [];
                let roleModuleAccessIdList = [];
                for (let i = 0; i < checkedData.length; i++) {
                    if(checkedData[i].f_module_access == '/index'){
                        roleModuleIdList.push(checkedData[i].f_module_id)
                    }
                    roleModuleAccessIdList.push(checkedData[i].id);
                }

                preloader_block();
                $.ajax({
                    type: 'POST',
                    dataType: "JSON",
                    url:"{{ $urlb }}roleModuleAccessUpdate/"+sel.f_role_id,
                    data:{
                        _token: "{{ csrf_token() }}",
                        roleModuleIdList:roleModuleIdList,
                        roleModuleAccessIdList:roleModuleAccessIdList
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

                            $eu("#cp{{$_idb_}}dGrid").treegrid('reload');
                            // $eu("#cp{{$_idb_}}dGrid").treegrid('clearSelections');
                            // $eu("#cp{{$_idb_}}dGrid").treegrid('clearChecked');
                        }else{
                            Swal.fire({
                                width:'300px',
                                icon: 'error',
                                title: 'Oops...',
                                text: r.message,
                                showConfirmButton:false
                            })
                            setTimeout(() => { Swal.close(); }, 2000);
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
                        setTimeout(() => { Swal.close(); }, 2000);
                    }
                })
            // }else{
            //     Swal.fire({
            //         width:'300px',
            //         icon: 'error',
            //         title: 'Oops...',
            //         text: 'Please some client first.',
            //         showConfirmButton:false
            //     })
            //     setTimeout(() => { Swal.close(); }, 2000);
            // }
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select User first.',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 2000);
        }
    }   
</script>