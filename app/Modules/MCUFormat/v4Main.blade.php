<div id="cp{{$_idb_}}dGridToolbar">
    <div class="row p-3">
        <div class="col-12 col-md-12">
            <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_idb_}}dGridPackageItemUpdate()"><i class="fas fa-add fa-fw"></i> Save</button>
        </div>
        <div class="col-12 col-md-12 d-none">
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
            pageList: [1000],
            pageSize: 1000,
            rownumbers: true,
            lines:true,
            checkbox: true,
            method: "GET",
            idField: "id",
            treeField: "name_custome",
            // url: "{{ $urlb }}read",
            columns: [[
                {field: "name_custome",title: "<b>Name</b>",align: "left",width: 350},
                {field: "level",title: "<b>Level</b>",align: "center",width: 50},
                {field: "header_name",title: "<b>Type</b>",align: "center",width: 90},
                {field: "unit",title: "<b>Unit</b>",align: "center",width: 90},
                {field: "ref_m",title: "<b>Normal (Male)</b>",align: "center",width: 150},
                {field: "ref_f",title: "<b>Normal (Female)</b>",align: "center",width: 150},
                {field: "code",title: "<b>Code</b>",align: "center",width: 120},
            ]],
            onSelect:function(row){
                // var root = $eu("#cp{{$_idb_}}dGrid").treegrid('getRoot');
                // var roots = $eu("#cp{{$_idb_}}dGrid").treegrid('getRoots');
                // var parent = $eu("#cp{{$_idb_}}dGrid").treegrid('getParent', row.id);
                // console.log(root);
                // console.log(roots);
                // console.log(parent);
            },
            onLoadSuccess:function(row, data){
                var rowsChecked = data;
                for (let i = 0; i < rowsChecked.length; i++) {
                    $eu("#cp{{$_idb_}}dGrid").treegrid("uncheckNode", rowsChecked[i].id);
                }
                var rowsChecked = data[0].rowsChecked;
                for (let i = 0; i < rowsChecked.length; i++) {
                    $eu("#cp{{$_idb_}}dGrid").treegrid("checkNode", rowsChecked[i].id);
                }
            },
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
            dGridId = sel.mcu_format_id;
            dGridId2 = sel.id;
        }
        filterParams = filterParams+"&mcu_format_id="+dGridId+"&mcu_format_package_id="+dGridId2;

        return filterParams;
    }
    function jf{{$_idb_}}dGridFilter_p(){
        var filterParams = jf{{$_idb_}}dGridFilter_g();
        // $eu("#cp{{$_idb_}}dGrid").treegrid("load", "{{ $urlb }}read/?"+filterParams);
        $eu("#cp{{$_idb_}}dGrid").treegrid({
            url: "{{ $urlb }}read/?"+filterParams
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

    function jf{{$_idb_}}dGridPackageItemUpdate(header = 0){
        let sel = $eu("#cp{{$_id_}}b3dGrid").datagrid('getSelected');
        if(sel){
            let checkedData = $eu("#cp{{$_idb_}}dGrid").treegrid('getCheckedNodes');
            let packageItemIdList = [];
            for (let i = 0; i < checkedData.length; i++) {
                if(checkedData[i].id != -1){
                    packageItemIdList.push(checkedData[i].id);
                }
            }
            if(packageItemIdList.length > 0){
                preloader_block();
                $.ajax({
                    type: 'POST',
                    dataType: "JSON",
                    url:"{{ $urlb }}packageItemUpdate/"+sel.id,
                    data:{
                        _token: "{{ csrf_token() }}",
                        packageItemIdList:packageItemIdList
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
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please checklist some Package Item.',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 2000);
            }
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select Package first.',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 2000);
        }
    }   
</script>