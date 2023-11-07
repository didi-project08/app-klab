<cpModalSize>modal-md</cpModalSize>
<cpModalTitle>Filter</cpModalTitle>
<cpModalFooter>
    <button class="btn btn-outline-secondary" type="button" onclick="jf{{$_idb_}}dGridFilter_r()">Reset</button>
    <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idb_}}dGridFilter_p()">Filter</button>
</cpModalFooter>

<form id="cp{{$_idb_}}dGridFilterForm" method="get" enctype="multipart/form-data">
    <div class="row">
        <div class="col-12 col-md-12 mb-3">
            <input class="form-controls" type="text" id="cp{{$_idb_}}dGridFilterSrcEvt" name="srcEvt" style="width:100%">
        </div>
        <div class="col-12 col-md-12 mb-3">
            <input class="form-controls" type="text" id="cp{{$_idb_}}dGridFilterScheduleDate" name="schedule_date" style="width:100%">
        </div>
        <div class="col-12 col-md-12 mb-3">
            <input class="form-controls" type="text" id="cp{{$_idb_}}dGridFilterStatus" name="status" style="width:100%">
        </div>
    </div>
</form>

<script>
    $eu(function(){
        $eu('#cp{{$_idb_}}dGridFilterSrcEvt').textbox({
            required:false,
            label:"Search",
            labelPosition:'top',
            prompt:"Type to search.",
        })
        $eu('#cp{{$_idb_}}dGridFilterSrcEvt').textbox('textbox').focus();

        $eu('#cp{{$_idb_}}dGridFilterScheduleDate').datebox({
            required:false,
            label:"MCU Schedule",
            labelPosition:'top',
            prompt:"dd/mm/yyyy",
            formatter:datebox_formatter_ddmmyyyy,
            parser:datebox_parser_ddmmyyyy,
            value:' '
        });
        $eu('#cp{{$_idb_}}dGridFilterStatus').combobox({
            required:true,
            label:"Status",
            labelPosition:'top',
            prompt:"Choose Status",
            editable:false,
            valueField:'id',
            textField:'title',
            panelHeight:'auto',
            data:[
                {id:'sAll',title:"- ALL -", selected:true},
                {id:'sScheduled',title:"REGISTERED"},
                {id:'sPresented',title:"PRESENT"},
                {id:'sCanceled',title:"CANCEL"},
                {id:'sHalfFinished',title:"HALF FINISH"},
                {id:'sFinished',title:"FINISH"},
            ]
        });
        $eu('#cp{{$_idb_}}dGridFilterStatus').combobox('setValue','sAll');

        jf{{$_idb_}}dGridFilter_p();
    })
</script>