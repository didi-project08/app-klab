<?php if(@$SEGMENT == 'MPDF_CONFIG') { ?>
    <?php
        $GLOBALS['MPDF_CONFIG'] = [
            'mode'=>'utf-8',
            'format'=>"A4",
            'margin_header'=>'5',
            'margin_top'=>'80',
            'margin_bottom'=>'5',
            'margin_left'=>'0',
            'margin_right'=>'0',
            'margin_footer'=>'0',
        ];
        $GLOBALS['MPDF_CONFIG_PAGE'] = [
            'margin-header'=>'5',
            'margin-top'=>'80',
            'margin-bottom'=>'5',
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
    $appImage = "mcues_header";
    $headerImage = $MainHelper::image($appImage);
?>

<htmlpageheader name="headerCover"></htmlpageheader>
<sethtmlpageheader name="headerCover" show-this-page="1">
<htmlpagefooter name="footerCover"></htmlpagefooter>
<sethtmlpagefooter name="footerCover">

<div style="width:100%; padding: 0px 50px">
    <table style="font-size:16px; padding:20px 40px" cellpadding="5">
        <tr>
            <td style="width:35%"></td>
            <td style="width:5%"></td>
            <td style="width:60%"></td>
        </tr>
        <tr>
            <td><b>Nama Lengkap</b></td>
            <td style="text-align:center">:</td>
            <td>{{ $mcuPatient->name }}</td>
        </tr>
        <tr>
            <td><b>Jenis Kelamin</b></td>
            <td style="text-align:center">:</td>
            <td>{{ $mcuPatient->genderModIndo }}</td>
        </tr>
        <tr>
            <td><b>Tanggal Lahir / Usia</b></td>
            <td style="text-align:center">:</td>
            <td>{{ date('d/m/Y', strtotime($mcuPatient->dob)) }} / {{ $mcuPatient->age_y }} Tahun {{ $mcuPatient->age_m }} Bulan</td>
        </tr>
        <tr>
            <td><b>Nomor Karyawan</b></td>
            <td style="text-align:center">:</td>
            <td>{{ $mcuPatient->emp_no }}</td>
        </tr>
        <tr>
            <td><b>Departemen</b></td>
            <td style="text-align:center">:</td>
            <td>{{ $mcuPatient->emp_pos }}</td>
        </tr>
        <tr>
            <td><b>Perusahaan</b></td>
            <td style="text-align:center">:</td>
            <td>{{ $mcuEvent->client_name }}</td>
        </tr>
        <tr>
            <td><b>No. MCU</b></td>
            <td style="text-align:center">:</td>
            <td>{{ $mcuPatient->reg_numFull }}</td>
        </tr>
        <tr>
            <td><b>Tanggal MCU</b></td>
            <td style="text-align:center">:</td>
            <td>{{ date('d/m/Y', strtotime($mcuPatient->schedule_date)) }}</td>
        </tr>
    </table>
    <br><br><br>
    <table style="font-size:18px; padding:20px 40px" cellpadding="5">
        <tr>
            <td style="width:100%; text-align:center"><b>Tim Medical Check-Up</b></td>
        </tr>
    </table>
</div>

<?php } ?>


