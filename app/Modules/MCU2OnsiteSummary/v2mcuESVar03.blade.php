<?php if(@$SEGMENT == 'MPDF_CONFIG') { ?>
    <?php
        $GLOBALS['MPDF_CONFIG'] = [
            'mode'=>'utf-8',
            'format'=>"A4",
            'margin_header'=>'5',
            'margin_top'=>'69',
            'margin_bottom'=>'1',
            'margin_left'=>'0',
            'margin_right'=>'0',
            'margin_footer'=>'0',
        ];
        $GLOBALS['MPDF_CONFIG_PAGE'] = [
            'margin-header'=>'5',
            'margin-top'=>'69',
            'margin-bottom'=>'1',
            'margin-left'=>'0',
            'margin-right'=>'0',
            'margin-footer'=>'0',
        ];
    ?>
<?php } else { ?>

@inject('carbon', 'Carbon\Carbon')
@inject('MainHelper', 'App\Helpers\Main')

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

    $GLOBALS['ROW_CLOSE'] = 1;
    $GLOBALS['SPLIT'] = 2;
    $GLOBALS['COLUMN'] = 0;

    if(!function_exists("loopItemVar03")){
        function loopItemVar03($itemArray = []){
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

                    $GLOBALS['LAST_ITEM_HEADER'] = "";

                    $GLOBALS['RS_ITEM'] .=  '<tr>';
                    $GLOBALS['LAST_ITEM_HEADER'] .=  '<tr>';
                        if($vItem->level == 1){
                            $GLOBALS['RS_ITEM'] .=  '<td colspan=7 style="padding-left:'.$itemPadding.'"><b>' . $vItem->name.'</b></td>';
                            $GLOBALS['LAST_ITEM_HEADER'] .=  '<td colspan=7 style="padding-left:'.$itemPadding.'"><b>' . $vItem->name.'</b></td>';
                        }else{
                            $GLOBALS['RS_ITEM'] .=  '<td colspan=7 style="padding-left:'.$itemPadding.'">' . $vItem->name.'</td>';
                            $GLOBALS['LAST_ITEM_HEADER'] .=  '<td colspan=7 style="padding-left:'.$itemPadding.'">' . $vItem->name.'</td>';
                        }
                    $GLOBALS['RS_ITEM'] .=  '</tr>';
                    $GLOBALS['LAST_ITEM_HEADER'] .=  '</tr>';

                    if(isset($vItem->children) && count($vItem->children) > 0){
                        $GLOBALS['LAST_ITEM_CHILD_TOTAL'] = 0;
                        loopItemVar03($vItem->children);
                        if($GLOBALS['LAST_ITEM_CHILD_TOTAL'] == 0){
                            $GLOBALS['RS_ITEM'] = str_replace($GLOBALS['LAST_ITEM_HEADER'], "", $GLOBALS['RS_ITEM']);
                        }
                    }
                }else{
                    $itemRef = [];
                    if(isset($mcuRef[$vItem->code])){
                        $itemRef = $mcuRef[$vItem->code];
                    }
                    
                    $showRefUnit = @$itemRef->unit;
                    $showRefType = @$itemRef->type;
                    $showRef = "";
                    if($showRefType == "TEXT"){
                        $showRef = @$itemRef->from;
                    }else if($showRefType == "NUMBER"){
                        $showRef = @$itemRef->from;
                    }else if($showRefType == "RANGE_NUMBER"){
                        $showRef = @$itemRef->from . " - " . @$itemRef->to;
                    }

                    $itemCode = $vItem->code;
                    $showResult = trim(@$mcuPatient->$itemCode);
                    if(trim(strtoupper($vItem->input_type)) == 'PARAGRAPH'){
                        $showResult = nl2br($showResult);
                    }else if(trim(strtoupper($vItem->input_type)) == 'NUMBER'){
                        if(is_numeric($showResult)){
                            // if(is_float($showResult + 0)){
                            //     $decTotal = strlen(substr(strrchr((float)$showResult, "."), 1));
                            //     if((substr(strrchr($showResult, "."), 1) * 1) > 0){
                            //         $showResult = number_format($showResult,$decTotal,".",",");
                            //     }else{
                            //         $showResult = number_format($showResult,"0",".",",");
                            //     }
                            // }else{
                            //     $showResult = number_format($showResult,"0",".",",");
                            // }
                            $decTotal = $vItem->input_decimal;
                            if($decTotal > 0){
                                $showResult = round($showResult, $decTotal);
                                $showResult = number_format($showResult,$decTotal,".",",");
                            }else{
                                $showResult = number_format($showResult,"0",".",",");
                            }
                        }
                    }else if(trim(strtoupper($vItem->input_type)) == 'RANGE NUMBER'){
                        $showResultSplit = explode("-", str_replace(" ","",$showResult));
                        if(is_numeric($showResultSplit[0]) && is_numeric($showResultSplit[1])){
                            // if(is_float($showResultSplit[0] + 0) || is_float($showResultSplit[1] + 0)){
                            //     $showResultSplit_0decTotal = strlen(substr(strrchr((float)$showResultSplit[0], "."), 1));
                            //     $showResultSplit_1decTotal = strlen(substr(strrchr((float)$showResultSplit[1], "."), 1));
                            //     if((substr(strrchr($showResultSplit[0], "."), 1) * 1) > 0 || (substr(strrchr($showResultSplit[1], "."), 1) * 1) > 0){
                            //         $decTotal = $showResultSplit_0decTotal;
                            //         if($showResultSplit_0decTotal < $showResultSplit_1decTotal){
                            //             $decTotal = $showResultSplit_1decTotal;
                            //         }
                            //         $showResult = number_format($showResultSplit[0],$decTotal, ".", ",") . " - " . number_format($showResultSplit[1],$decTotal, ".", ",");
                            //     }else{
                            //         $showResult = number_format($showResultSplit[0],"0", ".", ",") . " - " . number_format($showResultSplit[1],"0", ".", ",");
                            //     }
                            // }else{
                            //     $showResult = number_format($showResultSplit[0],"0", ".", ",") . " - " . number_format($showResultSplit[1],"0", ".", ",");
                            // }
                            $decTotal = $vItem->input_decimal;
                            if($decTotal > 0){
                                $showResult = round($showResult, $decTotal);
                                $showResult = number_format($showResultSplit[0],$decTotal, ".", ",") . " - " . number_format($showResultSplit[1],$decTotal, ".", ",");
                            }else{
                                $showResult = number_format($showResultSplit[0],"0", ".", ",") . " - " . number_format($showResultSplit[1],"0", ".", ",");
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
                    }else if(trim(strtoupper($vItem->input_type)) == 'FUNCTION'){
                        if(trim($vItem->input_default) != null && trim($vItem->input_default) != ""){
                            eval("\$e_result = ".$vItem->input_default.";");
                            $showResult = $e_result;
                        }
                    }
                    $GLOBALS['ITEM_VALUE'][$vItem->code] = $showResult;

                    $showStar = "";
                    if($showRef != "" && $showRef != null && $showResult != "-" && $showResult != "" && $showResult  != null){
                        if($showRefType == "TEXT"){
                            if(strtoupper(str_replace(" ", "", @$itemRef->from)) != strtoupper(str_replace(" ", "", $showResult))){
                                $showStar = " *";
                            }
                        }else if($showRefType == "NUMBER"){
                            if(is_numeric(@$itemRef->from) && is_numeric($showResult)){
                                if((@$itemRef->from * 1) != ($showResult * 1)){
                                    $showStar = " *";
                                }
                            }
                        }else if($showRefType == "RANGE_NUMBER"){
                            if(trim(strtoupper($vItem->input_type)) == 'RANGE_NUMBER'){
                                if(is_numeric(@$itemRef->from) && is_numeric(@$itemRef->to)){
                                    $showResultSplit = explode("-", str_replace(" ","",@$mcuPatient->$itemCode));
                                    if(is_numeric($showResultSplit[0]) && is_numeric($showResultSplit[1])){
                                        if(($showResultSplit[0] * 1) < (@$itemRef->from * 1) || ($showResultSplit[1] * 1) > (@$itemRef->to * 1)){
                                            $showStar = " *";
                                        }
                                        if(($showResultSplit[0] * 1) < (@$itemRef->from * 1) || ($showResultSplit[1] * 1) > (@$itemRef->to * 1)){
                                            $showStar = " *";
                                        }
                                    }
                                }
                            }else{
                                if(is_numeric(@$itemRef->from) && is_numeric(@$itemRef->to)){
                                    if(is_numeric($showResult)){
                                        if(($showResult * 1) < (@$itemRef->from * 1) || ($showResult * 1) > (@$itemRef->to * 1)){
                                            $showStar = " *";
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $show = 0;
                    if($vItem->input_show == 1){
                        $show = 1;
                    }else if($vItem->input_show == 2){
                        if($showResult != "" && $showResult != null){
                            $show = 1;
                        }
                    }
                    if($show == 1){
                        $GLOBALS['LAST_ITEM_CHILD_TOTAL'] = $GLOBALS['LAST_ITEM_CHILD_TOTAL'] + 1;

                        $GLOBALS['COLUMN'] = $GLOBALS['COLUMN'] + 1;
                        if($GLOBALS['COLUMN'] == 1){
                            $GLOBALS['RS_ITEM'] .=  '<tr>';
                            $GLOBALS['ROW_CLOSE'] = 0;
                        }

                        $GLOBALS['RS_ITEM'] .=  '<td style="padding-left:'.$itemPadding.'">' . $vItem->name . '</td>';
                        $GLOBALS['RS_ITEM'] .=  '<td style="text-aling:center">:</td>';
                        if(trim(strtoupper($vItem->input_type)) == 'IMAGE'){
                            $GLOBALS['RS_ITEM'] .=  '<td>';
                                if($showResult != null && $showResult != ""){
                                    $GLOBALS['RS_ITEM'] .=  '<img src="'.htmlspecialchars_decode($showResult).'" alt="" style="height:70px">';
                                }
                            $GLOBALS['RS_ITEM'] .=  '</td>';
                        }else{
                            $GLOBALS['RS_ITEM'] .=  '<td>'.htmlspecialchars_decode($showResult).htmlspecialchars_decode($showStar).'</td>';
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
    }
?>

<?php
    $appImage = "mcues_header";
    $headerImage = $MainHelper::image($appImage);
?>

<htmlpageheader name="header{{ $mcuItem->id }}">
    <div style="width:100%; padding: 0px 30px">
        <?php if($headerImage != "" && $headerImage != null) { ?>
            <img src="{{ $headerImage }}" alt="" style="width:100%">
        <?php } ?>
    </div>
    <div style="width:100%; padding: 0px 30px; margin-top:10px">
        <table style="font-size:10px">
            <tr>
                <td style="width:15%">Nama Lengkap</td>
                <td style="width:2%; text-align:center;">:</td>
                <td style="width:33%">{{ $mcuPatient->name }}</td>
                <td style="width:3%"></td>
                <td style="width:12%">Departemen</td>
                <td style="width:2%; text-align:center;">:</td>
                <td style="width:33%">{{ $mcuPatient->emp_pos }}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td style="text-align:center;">:</td>
                <td>{{ $mcuPatient->genderModIndo }}</td>
                <td></td>
                <td>No. MCU</td>
                <td style="text-align:center;">:</td>
                <td>{{ $mcuPatient->reg_numFull }}</td>
            </tr>
            <tr>
                <td>Tanggal Lahir / Usia</td>
                <td style="text-align:center;">:</td>
                <td>{{ date('d/m/Y', strtotime($mcuPatient->dob)) }} / {{ $mcuPatient->age_y }} Tahun {{ $mcuPatient->age_m }} Bulan</td>
                <td></td>
                <td>Tanggal MCU</td>
                <td style="text-align:center;">:</td>
                <td>{{ date('d/m/Y', strtotime($mcuPatient->schedule_date)) }}</td>
            </tr>
        </table>
    </div>
    <div style="width:100%; padding: 0px 30px">
        <hr style="padding:0; margin:2px;">
            <div style="width:100%; text-align:center; font-size:12px"><b><?= strtoupper($mcuItem->name) ?></b></div>
        <hr style="padding:0; margin:2px;">
        <table style="font-size:12px">
            <tr>
                <td style="width:26%"><b>Pemeriksaan</b></td>
                <td style="width:23%"><b>Hasil</b></td>
                <td style="width:2%"></td>
                <td style="width:26%"><b>Pemeriksaan</b></td>
                <td style="width:23%"><b>Hasil</b></td>
            </tr>
        </table>
        <hr style="padding:0; margin:2px;">
    </div>
</htmlpageheader>
<sethtmlpageheader name="header{{ $mcuItem->id }}" page="ALL" value="1" show-this-page="1" />
<htmlpagefooter name="footer{{ $mcuItem->id }}">
</htmlpagefooter>
<sethtmlpagefooter name="footer{{ $mcuItem->id }}">

<div style="width:100%; padding: 0px 30px">
    <table style="font-size:9px">
        <tr>
            <td style="width:26%"></td>
            <td style="width:1%"></td>
            <td style="width:22%"></td>
            <td style="width:2%"></td>
            <td style="width:26%"></td>
            <td style="width:1%;"></td>
            <td style="width:22%"></td>
        </tr>
        <?php if(isset($mcuItem->children)) { ?>
            <?php 
                $GLOBALS['RS_ITEM'] = "";
                $GLOBALS['RS_ITEM_PADDING'] = 10;
            ?>
            <?php loopItemVar03($mcuItem->children) ?>
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
                    <img src="{{ $teamData['sign'] }}" alt="" style="text-align:center; height:100px; margin-bottom:-10px">
                <?php } ?>
                <div>{{ @$teamData['name'] }}</div>
                <hr>
                <div>Dokter Pemeriksa</div>
            </td>
        </tr>
    </table>
    <?php } ?>

</div>

<?php } ?>