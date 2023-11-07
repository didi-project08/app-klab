<?php if(@$SEGMENT == 'MPDF_CONFIG') { ?>
    <?php
        $GLOBALS['MPDF_CONFIG'] = [
            'mode'=>'utf-8',
            'format'=>"A4",
            'margin_header'=>'5',
            'margin_top'=>'68',
            'margin_bottom'=>'5',
            'margin_left'=>'0',
            'margin_right'=>'0',
            'margin_footer'=>'0',
        ];
        $GLOBALS['MPDF_CONFIG_PAGE'] = [
            'margin-header'=>'5',
            'margin-top'=>'68',
            'margin-bottom'=>'5',
            'margin-left'=>'0',
            'margin-right'=>'0',
            'margin-footer'=>'0',
        ];
    ?>
<?php } else { ?>

<?php $carbon = app('Carbon\Carbon'); ?>
<?php $MainHelper = app('App\Helpers\Main'); ?>

<style>
    table{
        width: 100%;
        overflow: wrap;
    }
    table td, table td * {
        vertical-align: top;
    }
    table {
        font-size:12px;
    }
</style>

<?php
    $GLOBALS['mcuPatient'] = @$mcuPatient;
    $GLOBALS['mcuRef'] = @$mcuRef;
    $GLOBALS['mcuResult'] = @$mcuResult;

    $GLOBALS['ROW_CLOSE'] = 1;
    $GLOBALS['SPLIT'] = 2;
    $GLOBALS['COLUMN'] = 0;

    if(!function_exists("loopItem")){
        function loopItem($itemArray = []){
            $mcuPatient = $GLOBALS['mcuPatient'];
            $mcuRef = $GLOBALS['mcuRef'];
            $mcuResult = $GLOBALS['mcuResult'];

            foreach ($itemArray as $kItem => $vItem) {
                $itemPadding = $GLOBALS['RS_ITEM_PADDING'] * $vItem->level . "px";

                if($vItem->header * 1 == 1){
                    if($GLOBALS['ROW_CLOSE'] == 0){
                        $GLOBALS['RS_ITEM'] .=  '</tr>';
                        $GLOBALS['ROW_CLOSE'] = 1;
                        $GLOBALS['COLUMN'] = 0;
                    }

                    $GLOBALS['RS_ITEM'] .=  '<tr>';
                        if($vItem->level == 1){
                            $GLOBALS['RS_ITEM'] .=  '<td colspan=5 style="padding-left:'.$itemPadding.'"><b>' . $vItem->name.'</b></td>';
                        }else{
                            $GLOBALS['RS_ITEM'] .=  '<td colspan=5 style="padding-left:'.$itemPadding.'">' . $vItem->name.'</td>';
                        }
                    $GLOBALS['RS_ITEM'] .=  '</tr>';

                    if(isset($vItem->children) && count($vItem->children) > 0){
                        loopItem($vItem->children);
                    }
                }else{
                    $itemRef = [];
                    if(isset($mcuRef[$vItem->code])){
                        $itemRef = $mcuRef[$vItem->code];
                    }
                    
                    $showUnit = @$itemRef['unit'];
                    $showRefMode = @$itemRef['ref_mode'];
                    $showRef = "";
                    if($showRefMode == 1){
                        $showRef = @$itemRef['ref_from'];
                    }else if($showRefMode == 2){
                        $showRef = @$itemRef['ref_from'];
                    }else if($showRefMode == 3){
                        $showRef = @$itemRef['ref_from'] . "-" . @$itemRef['ref_to'];
                    }

                    $itemCode = $vItem->code;
                    $showResult = @$mcuPatient->$itemCode;
                    
                    if(trim(strtoupper($vItem->input_type)) == 'PARAGRAPH'){
                        $showResult = nl2br($showResult);
                    }else if(trim(strtoupper($vItem->input_type)) == 'NUMBER'){
                        if(is_numeric($showResult)){
                            if(is_float($showResult + 0)){
                                $showResult = number_format($showResult,"2",",",".");
                            }else{
                                $showResult = number_format($showResult,"0",",",".");
                            }
                        }
                    }else if(trim(strtoupper($vItem->input_type)) == 'RANGE NUMBER'){
                        $showResultSplit = explode("|", $showResult);
                        if(is_numeric($showResultSplit[0]) && is_numeric($showResultSplit[1])){
                            if(is_float($showResultSplit[0] + 0) || is_float($showResultSplit[1] + 0)){
                                $showResult = number_format($showResultSplit[0],"2", ",", ".") . " - " . number_format($showResultSplit[1],"2", ",", ".");
                            }else{
                                $showResult = number_format($showResultSplit[0],"0", ",", ".") . " - " . number_format($showResultSplit[1],"0", ",", ".");
                            }
                        }
                    }else if(trim(strtoupper($vItem->input_type)) == 'IMAGE'){
                        // no dev
                        // $path = $showResult;
                        // $showResultURL = Storage::disk('s3')->temporaryUrl(
                        //     $path,
                        //     now()->addMinutes(15),
                        //     ['ResponseContentDisposition' => 'attachment']
                        // );
                    }

                    
                    $GLOBALS['COLUMN'] = $GLOBALS['COLUMN'] + 1;
                    if($GLOBALS['COLUMN'] == 1){
                        $GLOBALS['RS_ITEM'] .=  '<tr>';
                        $GLOBALS['ROW_CLOSE'] = 0;
                    }

                    $GLOBALS['RS_ITEM'] .=  '<td style="padding-left:'.$itemPadding.'">' . $vItem->name . '</td>';
                    if(trim(strtoupper($vItem->input_type)) == 'IMAGE'){
                        $GLOBALS['RS_ITEM'] .=  '<td>';
                            $GLOBALS['RS_ITEM'] .=  '<img src="'.$showResultURL.'" alt="" style="height:70px">';
                        $GLOBALS['RS_ITEM'] .=  '</td>';
                    }else{
                        $GLOBALS['RS_ITEM'] .=  '<td> : '.$showResult.'</td>';
                    }
                    if($GLOBALS['COLUMN'] == 1){
                        $GLOBALS['RS_ITEM'] .=  '<td></td>';
                    }

                    if($GLOBALS['COLUMN'] == 2){
                        $GLOBALS['RS_ITEM'] .=  '</tr>';
                        $GLOBALS['ROW_CLOSE'] = 1;
                        $GLOBALS['COLUMN'] = 0;
                    }
                }
            }
        }
    }
?>

<?php
    $appImage = "mcues_header";
    $headerImage = $MainHelper::image($appImage);
?>

<htmlpageheader name="header<?php echo e($mcuItem->id); ?>">
    <div style="width:100%; padding: 0px 30px">
        <?php if($headerImage != "" && $headerImage != null) { ?>
            <img src="<?php echo e($headerImage); ?>" alt="" style="width:100%">
        <?php } ?>
    </div>
    <div style="width:100%; padding: 0px 30px; margin-top:10px">
        <table style="font-size:9px">
            <tr>
                <td style="width:13%">Nama Lengkap</td>
                <td style="width:2%; text-align:center;">:</td>
                <td style="width:30%"><?php echo e($mcuPatient->name); ?></td>
                <td style="width:3%"></td>
                <td style="width:15%">No. RM</td>
                <td style="width:2%; text-align:center;">:</td>
                <td style="width:35%"><?php echo e($mcuPatient->reg_numFull); ?></td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td style="text-align:center;">:</td>
                <td><?php echo e($mcuPatient->genderModIndo); ?></td>
                <td></td>
                <td>Tanggal Pemeriksaan</td>
                <td style="text-align:center;">:</td>
                <td><?php echo e(date('d/m/Y', strtotime($mcuPatient->dob))); ?></td>
            </tr>
            <tr>
                <td>Tanggal Lahir</td>
                <td style="text-align:center;">:</td>
                <td><?php echo e(date('d/m/Y', strtotime($mcuPatient->schedule_date))); ?></td>
                <td></td>
                <td>Paket MCU</td>
                <td style="text-align:center;">:</td>
                <td><?php echo e($mcuPatient->mcu_format_package_name); ?></td>
            </tr>
        </table>
    </div>
    <div style="width:100%; padding: 0px 30px">
        <hr style="padding:0; margin:2px;">
            <div style="width:100%; text-align:center; font-size:11px"><b><?= strtoupper($mcuItem->name) ?></b></div>
        <hr style="padding:0; margin:2px;">
        <table style="font-size:10px">
            <tr>
                <td style="width:29%"><b>Pemeriksaan</b></td>
                <td style="width:20%"><b>Hasil</b></td>
                <td style="width:2%"></td>
                <td style="width:29%"><b>Pemeriksaan</b></td>
                <td style="width:20%"><b>Hasil</b></td>
            </tr>
        </table>
        <hr style="padding:0; margin:2px;">
    </div>
</htmlpageheader>
<sethtmlpageheader name="header<?php echo e($mcuItem->id); ?>" page="ALL" value="1" show-this-page="1" />
<htmlpagefooter name="footer<?php echo e($mcuItem->id); ?>">
</htmlpagefooter>
<sethtmlpagefooter name="footer<?php echo e($mcuItem->id); ?>">

<div style="width:100%; padding: 0px 30px">
    <table style="font-size:9px">
        <tr>
            <td style="width:27%"></td>
            <td style="width:22%"></td>
            <td style="width:2%"></td>
            <td style="width:27%"></td>
            <td style="width:22%"></td>
        </tr>
        <?php if(isset($mcuItem->children)) { ?>
            <?php 
                $GLOBALS['RS_ITEM'] = "";
                $GLOBALS['RS_ITEM_PADDING'] = 10;
            ?>
            <?php loopItem($mcuItem->children) ?>
            <?php
                if($GLOBALS['ROW_CLOSE'] == 0){
                    $GLOBALS['RS_ITEM'] .=  '</tr>';
                    $GLOBALS['ROW_CLOSE'] = 1;
                    $GLOBALS['COLUMN'] = 0;
                }
            ?>
            <?php echo $GLOBALS['RS_ITEM']; ?>
        <?php } ?>
    </table>
    <!-- <table style="font-size:10px">
        <tr>
            <td style="width:70%"></td>
            <td style="width:30%; text-align:center">
                <h1><br><br><br></h1>
                <div><?php echo e(@$vPackageParent['emp_doctor_name']); ?></div>
                <hr>
                <div>Dokter Pemeriksa</div>
            </td>
        </tr>
    </table> -->
</div>

<?php } ?><?php /**PATH /home/u1598413/public_sys/dkdmcu/app/Modules/MCU2Onsite/v2mcuESVar02.blade.php ENDPATH**/ ?>