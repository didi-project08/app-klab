<?php if(@$mode == 'updateResult') { ?>
    <cpModalSize>modal-lg</cpModalSize>
    <cpModalTitle>Update Result</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idb_); ?>dGridSave()">Update Result</button>
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
                            if($v->input_type == "PARAGRAPH"){
                                $GLOBALS['II'] .= '<textarea class="" type="text" name="iiData['.$GLOBALS['II_INDEX'].'][result]" id="'.$iiID.'" style="width:100%">'.@$resultData[$v->code].'</textarea>';
                            }else{
                                $GLOBALS['II'] .= '<input class="" type="text" name="iiData['.$GLOBALS['II_INDEX'].'][result]" id="'.$iiID.'" style="width:100%">';
                            }
                            $GLOBALS['II'] .= '<div id="'.$iiID.'_error" class="form_error"></div>';
                        $GLOBALS['II'] .= '</div>';
                    }
                    
                }
            }
        }
    }
?>

<div id="cp<?php echo e($_idb_); ?>dGridFormMainContent">
    <form id="cp<?php echo e($_idb_); ?>dGridFormMain" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

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
                    <input class="" type="hidden" name="iiData[<?php echo e($GLOBALS['II_INDEX']); ?>][id]" value="<?php echo e($vID); ?>" style="width:100%">
                    <input class="" type="hidden" name="iiData[<?php echo e($GLOBALS['II_INDEX']); ?>][code]" value="<?php echo e($vCode); ?>" style="width:100%">
                    <input class="" type="hidden" name="iiData[<?php echo e($GLOBALS['II_INDEX']); ?>][name]" value="<?php echo e($vName); ?>" style="width:100%">
                    <input class="" type="text"   name="iiData[<?php echo e($GLOBALS['II_INDEX']); ?>][result]" id="<?php echo e($iiID); ?>" style="width:100%">
                    <div id="<?php echo e($iiID); ?>_error" class="form_error"></div>
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
                    $eu('#<?php echo e($iiID); ?>').textbox({
                        required:false,
                        label:"<small><?php echo e($v->name); ?></small>",
                        labelPosition:'top',
                        prompt:"<?php echo e($v->name); ?>",
                        value:"<?php echo @$resultData[$v->code]; ?>"
                    })
                <?php } else if($v->input_type == "PARAGRAPH") { ?>
                    $eu('#<?php echo e($iiID); ?>').textbox({
                        required:false,
                        label:"<small><?php echo e($v->name); ?></small>",
                        labelPosition:'top',
                        prompt:"<?php echo e($v->name); ?>",
                        multiline:true,
                        height:150
                    })
                <?php } else if($v->input_type == "NUMBER") { ?>
                    $eu('#<?php echo e($iiID); ?>').numberbox({
                        required:false,
                        label:"<small><?php echo e($v->name); ?></small>",
                        labelPosition:'top',
                        prompt:"<?php echo e($v->name); ?>",
                        value:"<?php echo @$resultData[$v->code]; ?>",
                        // min:0,
                        precision:4,
                        groupSeparator:",",
                        decimalSeparator:"."
                    })
                <?php } else if($v->input_type == "RANGE_NUMBER") { ?>
                    // $eu('#<?php echo e($iiID); ?>').numberbox({
                    //     required:false,
                    //     label:"<small><?php echo e($v->name); ?></small>",
                    //     labelPosition:'top',
                    //     prompt:"<?php echo e($v->name); ?>",
                    //     value:"<?php echo @$resultData[$v->code]; ?>",
                    //     // min:0,
                    //     precision:4,
                    //     groupSeparator:",",
                    //     decimalSeparator:"."
                    // })
                    $eu('#<?php echo e($iiID); ?>').textbox({
                        required:false,
                        label:"<small><?php echo e($v->name); ?></small>",
                        labelPosition:'top',
                        prompt:"<?php echo e($v->name); ?>",
                        value:"<?php echo @$resultData[$v->code]; ?>"
                    })
                <?php } else if($v->input_type == "OPTION") { ?>
                    var dataOption = <?php echo json_encode(@$v->input_option); ?>

                    // alert(dataOption[]);
                    if(dataOption == "" || dataOption == null){
                        dataOption = "[]";
                    }
                    var dataOptionObj = JSON.parse(dataOption);
                    $eu('#<?php echo e($iiID); ?>').combobox({
                        required:false,
                        label:"<small><?php echo e($v->name); ?></small>",
                        labelPosition:'top',
                        prompt:"<?php echo e($v->name); ?>",
                        value:"<?php echo @$resultData[$v->code]; ?>",
                        panelHeight:'auto',
                        editable:false,
                        valueField:'value',
                        textField:'value',
                        data:dataOptionObj
                    })
                <?php } else if($v->input_type == "IMAGE") { ?>
                    $eu('#<?php echo e($iiID); ?>').filebox({
                        required:false,
                        label:"<small><?php echo e($v->name); ?></small>",
                        labelPosition:'top',
                        prompt:"<?php echo e($v->name); ?>",
                        value:"<?php echo @$resultData[$v->code]; ?>",
                        accept: 'image/*'
                    })
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
            $eu('#<?php echo e($iiID); ?>').combobox({
                required:false,
                label:"<small><?php echo e($vName); ?></small>",
                labelPosition:'top',
                prompt:"<?php echo e($vName); ?>",
                value:"<?php echo e(@$resultData[$vCode]); ?>",
                panelHeight:'auto',
                editable:false,
                valueField:'id',
                textField:'name',
                data:<?php echo $formatTeam; ?>

            })
        <?php } ?>

        euDoLayout();
    })

    function jf<?php echo e($_idb_); ?>dGridSave(){
        preloader_block();
        $eu("#cp<?php echo e($_idb_); ?>dGridFormMain").form("submit", {
            url: "<?php echo e(@$url_save); ?>",
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

                    cpGlobalModalClose("<?php echo e($cpModalId); ?>");
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('reload');
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearSelections');
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearChecked');
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
            $("#cp<?php echo e($_idb_); ?>dGridFormMainContent").html("Request not valid.");
        })
    </script>
<?php } ?><?php /**PATH /home/u1598413/public_sys/dkdmcu/app/Modules/MCU2Onsite/v2FormResult.blade.php ENDPATH**/ ?>