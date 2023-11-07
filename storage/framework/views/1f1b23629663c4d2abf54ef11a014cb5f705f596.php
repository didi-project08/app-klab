<?php if(@$SEGMENT == 'MPDF_CONFIG') { ?>
    <?php
        $GLOBALS['MPDF_CONFIG'] = [
            'mode'=>'utf-8',
            'format'=>"A4",
            'margin_header'=>'5',
            'margin_top'=>'70',
            'margin_bottom'=>'5',
            'margin_left'=>'0',
            'margin_right'=>'0',
            'margin_footer'=>'0',
        ];
        $GLOBALS['MPDF_CONFIG_PAGE'] = [
            'margin-header'=>'5',
            'margin-top'=>'70',
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
    $GLOBALS['LAST_ITEM_HEADER'] = "";
    $GLOBALS['LAST_ITEM_CHILD_TOTAL'] = 0;

    if(!function_exists("loopItemVar05")){
        function loopItemVar05($itemArray = []){
            $mcuPatient = $GLOBALS['mcuPatient'];
            $mcuRef = $GLOBALS['mcuRef'];
            $mcuResult = $GLOBALS['mcuResult'];

            foreach ($itemArray as $kItem => $vItem) {
                $itemPadding = $GLOBALS['RS_ITEM_PADDING'] * $vItem->level . "px";

                if($vItem->header * 1 == 1){
                    $GLOBALS['RS_ITEM'] .=  '<tr>';
                        if($vItem->level == 1){
                            $GLOBALS['RS_ITEM'] .=  '<td colspan=5 style="padding-left:'.$itemPadding.'"><b>' . $vItem->name.'</b></td>';
                        }else{
                            $GLOBALS['RS_ITEM'] .=  '<td colspan=5 style="padding-left:'.$itemPadding.'">' . $vItem->name.'</td>';
                        }
                    $GLOBALS['RS_ITEM'] .=  '</tr>';

                    if(isset($vItem->children) && count($vItem->children) > 0){
                        loopItemVar05($vItem->children);
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
                    $showResult = trim(@$mcuPatient->$itemCode);
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

                    
                    $GLOBALS['RS_ITEM'] .=  '<tr>';
                        $GLOBALS['RS_ITEM'] .=  '<td style="padding-left:'.$itemPadding.'">' . $vItem->name . '</td>';
                        $GLOBALS['RS_ITEM'] .= '<td style="text-align:center">:</td>';
                        if(trim(strtoupper($vItem->input_type)) == 'IMAGE'){
                            $GLOBALS['RS_ITEM'] .=  '<td>';
                                if($showResult != null && $showResult != ""){
                                    $GLOBALS['RS_ITEM'] .=  '<img src="'.htmlspecialchars_decode($showResult).'" alt="" style="height:300px">';
                                }
                            $GLOBALS['RS_ITEM'] .=  '</td>';
                        }else{
                            $GLOBALS['RS_ITEM'] .=  '<td>'.htmlspecialchars_decode($showResult).'</td>';
                        }
                    $GLOBALS['RS_ITEM'] .=  '</tr>';
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
        <table style="font-size:10px">
            <tr>
                <td style="width:15%">Nama Lengkap</td>
                <td style="width:2%; text-align:center;">:</td>
                <td style="width:33%"><?php echo e($mcuPatient->name); ?></td>
                <td style="width:3%"></td>
                <td style="width:12%">Departemen</td>
                <td style="width:2%; text-align:center;">:</td>
                <td style="width:33%"><?php echo e($mcuPatient->emp_pos); ?></td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td style="text-align:center;">:</td>
                <td><?php echo e($mcuPatient->genderModIndo); ?></td>
                <td></td>
                <td>No. MCU</td>
                <td style="text-align:center;">:</td>
                <td><?php echo e($mcuPatient->reg_numFull); ?></td>
            </tr>
            <tr>
                <td>Tanggal Lahir / Usia</td>
                <td style="text-align:center;">:</td>
                <td><?php echo e(date('d/m/Y', strtotime($mcuPatient->dob))); ?> / <?php echo e($mcuPatient->age_y); ?> Tahun <?php echo e($mcuPatient->age_m); ?> Bulan</td>
                <td></td>
                <td>Tanggal MCU</td>
                <td style="text-align:center;">:</td>
                <td><?php echo e(date('d/m/Y', strtotime($mcuPatient->schedule_date))); ?></td>
            </tr>
        </table>
    </div>
    <div style="width:100%; padding: 0px 30px">
        <hr style="padding:0; margin:2px;">
            <div style="width:100%; text-align:center; font-size:12px"><b><?= strtoupper($mcuItem->name) ?></b></div>
        <hr style="padding:0; margin:2px;">
        <table style="font-size:12px">
            <tr>
                <td style="width:25%"><b>Pemeriksaan</b></td>
                <td style="width:2%"></td>
                <td style="width:73%"><b>Hasil</b></td>
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
    <table style="font-size:13px">
        <tr>
            <td style="width:25%"></td>
            <td style="width:2%"></td>
            <td style="width:73%"></td>
        </tr>
        <?php if(isset($mcuItem->children)) { ?>
            <?php 
                $GLOBALS['RS_ITEM'] = "";
                $GLOBALS['RS_ITEM_PADDING'] = 10;
            ?>
            <?php loopItemVar05($mcuItem->children) ?>
            <?php echo $GLOBALS['RS_ITEM']; ?>
        <?php } ?>
    </table>

    <?php if(1==1) { ?>
    <table style="font-size:12px">
        <tr>
            <td style="width:70%"></td>
            <td style="width:30%; text-align:center">
            <h1><br></h1>
                <?php
                    $teamID = 0;
                    $teamData = [];
                    $teamIDCode = "SYS___".$mcuItem->main_parent."_EXAMINER_ID";
                    if(isset($mcuPatient->$teamIDCode)){
                        $teamID = $mcuPatient->$teamIDCode;
                    }
                    if(isset($mcuTeam[$teamID])){
                        $teamData = (array) $mcuTeam[$teamID];
                    }
                    // dd($teamData);
                ?>
                <?php if(@$teamData['sign'] != "" && @$teamData['sign'] != null) { ?>
                    <img src="<?php echo e($teamData['sign']); ?>" alt="" style="text-align:center; height:100px; margin-bottom:-10px">
                <?php } ?>
                <div><?php echo e(@$teamData['name']); ?></div>
                <hr>
                <div>Dokter Pemeriksa</div>
            </td>
        </tr>
    </table>
    <?php } ?>

</div>

<?php } ?><?php /**PATH /home/u1598413/public_sys/dkdmcu/app/Modules/MCU2Onsite/v2mcuESVar05.blade.php ENDPATH**/ ?>