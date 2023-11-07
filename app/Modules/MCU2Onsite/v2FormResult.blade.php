<?php if(@$mode == 'updateResult') { ?>
    <cpModalSize>modal-lg</cpModalSize>
    <cpModalTitle>Update Result</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idb_}}dGridSave()">Update Result</button>
    </cpModalFooter>
<?php } ?>


<?php
    $GLOBALS['resultData'] = $resultData;

    if(!function_exists("formLoop")){
        function formLoop($itemTree){
            $resultData = $GLOBALS['resultData'];

            foreach ($itemTree as $k => $v) {
                if($v->header == 1){
                    if($v->level == 0){
                        $GLOBALS['II'] .= '<div class="col-12 col-md-12 mt-3">';
                            $GLOBALS['II'] .= '<center>';
                                $GLOBALS['II'] .= '<label class="fs-5 fw-bold txt-primary">'.$v->name.'</label>';
                            $GLOBALS['II'] .= '</center>';
                        $GLOBALS['II'] .= '</div>';
                    }else{
                        $GLOBALS['II'] .= '<div class="col-12 col-md-12 mt-4">';
                            $GLOBALS['II'] .= '<label class="fs-6 fw-bold txt-primary mb-0"><small>Lv'.$v->level.":".$v->name.'</small></label>';
                        $GLOBALS['II'] .= '</div>';
                    }
                    if(isset($v->children) && count($v->children) > 0){
                        formLoop($v->children);
                    }
                }else{
                    if($v->input_type != "FUNCTION"){
                        $GLOBALS['II_INDEX'] = $GLOBALS['II_INDEX'] + 1;
                        $iiID = "II".$v->id;

                        // $GLOBALS['II'] .= '<div class="col-12 col-md-6 mb-2">';
                        if($v->input_type == "PARAGRAPH"){
                            $GLOBALS['II'] .= '<div class="col-12 col-md-12 mb-2">';
                        }else{
                            $GLOBALS['II'] .= '<div class="col-12 col-md-6 mb-2">';
                        }
                            $GLOBALS['II'] .= '<input class="" type="hidden" name="iiData['.$GLOBALS['II_INDEX'].'][id]" value="'.$v->id.'" style="width:100%">';
                            $GLOBALS['II'] .= '<input class="" type="hidden" name="iiData['.$GLOBALS['II_INDEX'].'][code]" value="'.$v->code.'" style="width:100%">';
                            $GLOBALS['II'] .= '<input class="" type="hidden" name="iiData['.$GLOBALS['II_INDEX'].'][name]" value="'.$v->name.'" style="width:100%">';
                            $GLOBALS['II'] .= '<input class="" type="hidden" name="iiData['.$GLOBALS['II_INDEX'].'][input_type]" value="'.$v->input_type.'" style="width:100%">';
                            $GLOBALS['II'] .= '<input class="" type="hidden" name="iiData['.$GLOBALS['II_INDEX'].'][input_image_folder]" value="'.$v->input_image_folder.'" style="width:100%">';
                            if($v->input_type == "PARAGRAPH"){
                                $GLOBALS['II'] .= '<textarea class="" type="text" name="iiData['.$GLOBALS['II_INDEX'].'][result]" id="'.$iiID.'" style="width:100%">'.@$resultData[$v->code].'</textarea>';
                            }else{
                                $GLOBALS['II'] .= '<input class="" type="text" name="iiData['.$GLOBALS['II_INDEX'].'][result]" id="'.$iiID.'" style="width:100%">';
                            }
                            if($v->input_type == "IMAGE"){
                                $GLOBALS['II'] .= '<div id="'.$iiID.'_file_exist"></div>';
                            }
                            $GLOBALS['II'] .= '<div id="'.$iiID.'_error" class="form_error"></div>';
                        $GLOBALS['II'] .= '</div>';
                    }
                    
                }
            }
        }
    }
?>

<div id="cp{{$_idb_}}dGridFormMainContent">
    <form id="cp{{$_idb_}}dGridFormMain" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <?php 
                $GLOBALS['II'] = "";
                $GLOBALS['II_INDEX'] = -1;
                $GLOBALS['II_PADDING'] = 10;
            ?>
            <?php formLoop($formatItemTree); ?>
            <?php echo $GLOBALS['II']; ?>

            <div class="col-12 col-md-12 mt-3">
                <center>
                    <label class="fs-5 fw-bold txt-primary">MCU TEAM</label>
                </center>
            </div>

            <?php if($GLOBALS['II_INDEX'] >= 0 ) { ?>
                <?php $itemSel = $formatItemTree[0]; ?>
                <?php
                    $GLOBALS['II_INDEX'] = $GLOBALS['II_INDEX'] + 1;
                    $iiID = "SYS___".$itemSel->main_parent."_EXAMINER_ID";
                    $vID = 0;
                    $vCode = $iiID;
                    $vName = "Doctor / Nurse";
                ?>
                <div class="col-12 col-md-6 mb-2">
                    <input class="" type="hidden" name="iiData[{{ $GLOBALS['II_INDEX'] }}][id]" value="{{ $vID }}" style="width:100%">
                    <input class="" type="hidden" name="iiData[{{ $GLOBALS['II_INDEX'] }}][code]" value="{{ $vCode }}" style="width:100%">
                    <input class="" type="hidden" name="iiData[{{ $GLOBALS['II_INDEX'] }}][name]" value="{{ $vName }}" style="width:100%">
                    <input class="" type="hidden" name="iiData[{{ $GLOBALS['II_INDEX'] }}][input_type]" value="TEXT" style="width:100%">
                    <input class="" type="hidden" name="iiData[{{ $GLOBALS['II_INDEX'] }}][input_image_folder]" value="" style="width:100%">
                    <input class="" type="text"   name="iiData[{{ $GLOBALS['II_INDEX'] }}][result]" id="{{ $iiID }}" style="width:100%">
                    <div id="{{ $iiID }}_error" class="form_error"></div>
                </div>
            <?php } ?>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        <?php foreach ($formatItem as $k => $v) { ?>
            <?php if($v->header == 0 && $v->input_type != "FUNCTION") { ?>
                <?php $iiID = "II".$v->id; ?>

                <?php
                    if($v->input_type == "" && $v->input_type == null) {
                        $v->input_type = "TEXT";
                    }
                ?>

                <?php if($v->input_type == "TEXT") { ?>
                    $eu('#{{ $iiID }}').textbox({
                        required:false,
                        label:"<small>{{ $v->name }}</small>",
                        labelPosition:'top',
                        prompt:"{{ $v->name }}",
                        value:"{!! @$resultData[$v->code] !!}"
                    })
                <?php } else if($v->input_type == "PARAGRAPH") { ?>
                    $eu('#{{ $iiID }}').textbox({
                        required:false,
                        label:"<small>{{ $v->name }}</small>",
                        labelPosition:'top',
                        prompt:"{{ $v->name }}",
                        multiline:true,
                        height:150
                    })
                <?php } else if($v->input_type == "NUMBER") { ?>
                    $eu('#{{ $iiID }}').numberbox({
                        required:false,
                        label:"<small>{{ $v->name }}</small>",
                        labelPosition:'top',
                        prompt:"{{ $v->name }}",
                        value:"{!! @$resultData[$v->code] !!}",
                        // min:0,
                        precision:4,
                        groupSeparator:",",
                        decimalSeparator:"."
                    })
                <?php } else if($v->input_type == "RANGE_NUMBER") { ?>
                    // $eu('#{{ $iiID }}').numberbox({
                    //     required:false,
                    //     label:"<small>{{ $v->name }}</small>",
                    //     labelPosition:'top',
                    //     prompt:"{{ $v->name }}",
                    //     value:"{!! @$resultData[$v->code] !!}",
                    //     // min:0,
                    //     precision:4,
                    //     groupSeparator:",",
                    //     decimalSeparator:"."
                    // })
                    $eu('#{{ $iiID }}').textbox({
                        required:false,
                        label:"<small>{{ $v->name }}</small>",
                        labelPosition:'top',
                        prompt:"{{ $v->name }}",
                        value:"{!! @$resultData[$v->code] !!}"
                    })
                <?php } else if($v->input_type == "OPTION") { ?>
                    var dataOption = {!! json_encode(@$v->input_option) !!}
                    // alert(dataOption[]);
                    if(dataOption == "" || dataOption == null){
                        dataOption = "[]";
                    }
                    var dataOptionObj = JSON.parse(dataOption);
                    $eu('#{{ $iiID }}').combobox({
                        required:false,
                        label:"<small>{{ $v->name }}</small>",
                        labelPosition:'top',
                        prompt:"{{ $v->name }}",
                        value:"{!! @$resultData[$v->code] !!}",
                        panelHeight:'auto',
                        editable:false,
                        valueField:'value',
                        textField:'value',
                        data:dataOptionObj
                    })
                <?php } else if($v->input_type == "IMAGE") { ?>
                    $eu('#{{ $iiID }}').filebox({
                        required:false,
                        label:"<small>{{ $v->name }}</small>",
                        labelPosition:'top',
                        prompt:"{{ $v->name }}",
                        value:"",
                        accept: 'image/*'
                    })
                    $('#{{ $iiID }}_file_exist').text("Exist : {!! @$resultData[$v->code] !!}");
                <?php } ?>
            <?php } ?>
        <?php } ?>
        
        <?php if($GLOBALS['II_INDEX'] >= 0 ) { ?>
            <?php $itemSel = $formatItemTree[0]; ?>
            <?php
                $GLOBALS['II_INDEX'] = $GLOBALS['II_INDEX'] + 1;
                $iiID = "SYS___".$v->main_parent."_EXAMINER_ID";
                $vID = 0;
                $vCode = $iiID;
                $vName = "Doctor / Nurse";
            ?>
            $eu('#{{ $iiID }}').combobox({
                required:false,
                label:"<small>{{ $vName }}</small>",
                labelPosition:'top',
                prompt:"{{ $vName }}",
                value:"{{ @$resultData[$vCode] }}",
                panelHeight:'auto',
                editable:false,
                valueField:'id',
                textField:'name',
                data:{!! $formatTeam !!}
            })
        <?php } ?>

        euDoLayout();
    })

    function jf{{$_idb_}}dGridSave(){
        preloader_block();
        $eu("#cp{{$_idb_}}dGridFormMain").form("submit", {
            url: "{{ @$url_save }}",
            queryParams:{
                patient_photo:$("#patient_photo").val()
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

<?php if(@$mode == 'updateResult') { ?>
    <script>
        $(function(){
            
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp{{$_idb_}}dGridFormMainContent").html("Request not valid.");
        })
    </script>
<?php } ?>