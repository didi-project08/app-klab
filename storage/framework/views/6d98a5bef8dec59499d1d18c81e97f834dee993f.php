<?php if(@$SEGMENT == 'MPDF_CONFIG') { ?>
    <?php
        $GLOBALS['MPDF_CONFIG'] = [
            'mode'=>'utf-8',
            'format'=>"A4",
            'margin_header'=>'5',
            'margin_top'=>'65',
            'margin_bottom'=>'5',
            'margin_left'=>'0',
            'margin_right'=>'0',
            'margin_footer'=>'0',
        ];
        $GLOBALS['MPDF_CONFIG_PAGE'] = [
            'margin-header'=>'5',
            'margin-top'=>'65',
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

    if(!function_exists("loopItemVar01")){
        function loopItemVar01($itemArray = []){
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
                        loopItemVar01($vItem->children);
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

                    
                    $GLOBALS['RS_ITEM'] .=  '<tr>';
                        $GLOBALS['RS_ITEM'] .=  '<td style="padding-left:'.$itemPadding.'"><b>' . $vItem->name . ' :</b></td>';
                        if(trim(strtoupper($vItem->input_type)) == 'IMAGE'){
                            $GLOBALS['RS_ITEM'] .=  '<td>';
                                if($showResult != null && $showResult != ""){
                                    $GLOBALS['RS_ITEM'] .=  '<img src="'.$showResult.'" alt="" style="height:70px">';
                                }
                            $GLOBALS['RS_ITEM'] .=  '</td>';
                        }else{
                            $GLOBALS['RS_ITEM'] .=  '<td>'.$showResult.'</td>';
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
    </div>
</htmlpageheader>
<sethtmlpageheader name="header<?php echo e($mcuItem->id); ?>" page="ALL" value="1" show-this-page="1" />
<htmlpagefooter name="footer<?php echo e($mcuItem->id); ?>">
</htmlpagefooter>
<sethtmlpagefooter name="footer<?php echo e($mcuItem->id); ?>">

<div style="width:100%; padding: 0px 30px">
    <table style="font-size:12px">
        <tr>
            <td style="width:100%"></td>
        </tr>
        <tr>
            <td style="border:1px solid; border-radius:5px">
                <!-- <div style="width:100%; border:solid black 1px; border-radius:5px;"> -->
                    <table style="font-size:12px; margin:5px 45px 10px 45px" cellspacing="0">
                        <tr>
                            <td style="width:25%"></td>
                            <td style="width:50%"></td>
                            <td style="width:25%"></td>
                        </tr>
                        <tr>
                            <td style="background:#bebebe;"></td>
                            <td style="padding-top:4px; padding-bottom:4px; background:#bebebe; text-align:center;"><b>FIT STATUS</b></td>
                            <td style="background:#bebebe;"></td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                        </tr>
                        <?php if(trim(strtolower(@$mcuPatient->SYS___FITSTATUS)) == "fit to work") { ?>
                            <tr>
                                <td style="text-align:center"><b>Fit to Work</b></td>
                                <td style="text-align:center"><s>Fit To Work With Recommendation</s></td>
                                <td style="text-align:center"><s>Unfit</s></td>
                            </tr>
                        <?php } ?>
                        <?php if(trim(strtolower(@$mcuPatient->SYS___FITSTATUS)) == "fit to work with recommendation") { ?>
                            <tr>
                                <td style="text-align:center"><s>Fit to Work</s></td>
                                <td style="text-align:center"><b>Fit To Work With Recommendation</b></td>
                                <td style="text-align:center"><s>Unfit</s></td>
                            </tr>
                        <?php } ?>
                        <?php if(trim(strtolower(@$mcuPatient->SYS___FITSTATUS)) == "unfit") { ?>
                            <tr>
                                <td style="text-align:center"><s>Fit to Work</s></td>
                                <td style="text-align:center"><s>Fit To Work With Recommendation</s></td>
                                <td style="text-align:center"><b>Unfit</b></td>
                            </tr>
                        <?php } ?>
                    </table>
                <!-- </div> -->
            </td>
        </tr>
        <tr><td><br></td></tr>
        <tr>
            <td><b>KESIMPULAN :</b></td>
        </tr>
        <tr>
            <td style="padding-left:10px"><?php echo nl2br(@$mcuPatient->SYS___CONCLUSION); ?></td>
        </tr>
        <tr><td><br></td></tr>
        <tr>
            <td><b>SARAN :</b></td>
        </tr>
        <tr>
            <td style="padding-left:10px"><?php echo nl2br(@$mcuPatient->SYS___SUGGESTION); ?></td>
        </tr>
    </table>
    <br>
    <p style="font-size:11px"><i>* Lakukan pemeriksaan kesehatan secara berkala setidaknya setiap 1 tahun sekali</i></p>
    
    <?php if(1==2) { ?>
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
                <div>Penanggung Jawab MCU</div>
            </td>
        </tr>
    </table>
    <?php } ?>

</div>


<?php } ?><?php /**PATH /home/u1598413/public_sys/dkdmcu/app/Modules/MCU2Onsite/v2mcuESVar01.blade.php ENDPATH**/ ?>