<cpModalSize>modal-md</cpModalSize>
<cpModalTitle>Filter</cpModalTitle>
<cpModalFooter>
    <button class="btn btn-outline-secondary" type="button" onclick="jf{{$_id_}}dGridFilter_r()">Reset</button>
    <button class="btn btn-outline-primary" type="button" onclick="jf{{$_id_}}dGridFilter_p()">Filter</button>
</cpModalFooter>

<form id="cp{{$_id_}}dGridFilterForm" method="get" enctype="multipart/form-data">
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <input class="form-controls" type="text" id="cp{{$_id_}}dGridFilterSrcEvt" name="srcEvt" style="width:100%">
        </div>
    </div>
</form>

<script>
    $eu(function(){
        $eu('#cp{{$_id_}}dGridFilterSrcEvt').textbox({
            required:false,
            label:"Search",
            labelPosition:'top',
            prompt:"Type to search.",
        })
        $eu('#cp{{$_id_}}dGridFilterSrcEvt').textbox('textbox').focus();

        jf{{$_id_}}dGridFilter_p();
    })
</script>