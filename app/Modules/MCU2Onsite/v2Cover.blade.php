<div style="margin: 0 80px;">
    <div class="row" style="font-size: 9pt">
        <div class="colp-59">
            <div class="row">
                <div class="col-12" style="font-size: 20pt;">GENERAL REPORT</div>
                <div class="col-12" style="font-size: 14pt; font-weight: bold;">{{ @$data->name }}</div>
                <div class="col-12" style="font-size: 20pt;"><br></div>
                <div class="col-12" style="font-size: 12pt;">
                    <div class="row" style="font-size: 12pt; margin-bottom:8px">
                        <div class="colp-29">No. MCU</div>
                        <div class="colp-9 text-center">:</div>
                        <div class="colp-59">{{ @$data->presentnumFull }}</div>
                    </div>
                    <div class="row" style="font-size: 12pt; margin-bottom:8px">
                        <div class="colp-29">Tanggal MCU</div>
                        <div class="colp-9 text-center">:</div>
                        <div class="colp-59">{{ @$data->actual_date_dmY_slash }}</div>
                    </div>
                    <div class="row" style="font-size: 12pt; margin-bottom:8px">
                        <div class="colp-29">Paket MCU</div>
                        <div class="colp-9 text-center">:</div>
                        <div class="colp-59">{{ @$data->mcu_format_package_name }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="colp-39">
            <img src="{{ @$data->patient_photo }}" width="100%" style="padding:0">
        </div>
    </div>

    <div class="row">
        <br>
        <br>
        <br>
        <br>
        <br>
    </div>

    <div class="row" style="font-size: 12pt; margin-bottom:10px">
        <div class="colp-29">Nomor Karyawan</div>
        <div class="colp-9 text-center">:</div>
        <div class="colp-59">{{ @$data->emp_no }}</div>
    </div>
    <div class="row" style="font-size: 12pt; margin-bottom:10px">
        <div class="colp-29">Nama Lengkap</div>
        <div class="colp-9 text-center">:</div>
        <div class="colp-59">{{ @$data->name }}</div>
    </div>
    <div class="row" style="font-size: 12pt; margin-bottom:10px">
        <div class="colp-29">Tangal Lahir</div>
        <div class="colp-9 text-center">:</div>
        <div class="colp-59">{{ @$data->dob_dmY_slash }}</div>
    </div>
    <div class="row" style="font-size: 12pt; margin-bottom:10px">
        <div class="colp-29">Jenis Kelamin</div>
        <div class="colp-9 text-center">:</div>
        <div class="colp-59">{{ @$data->genderMod }}</div>
    </div>
    <div class="row" style="font-size: 12pt; margin-bottom:10px">
        <div class="colp-29">Perusahaan</div>
        <div class="colp-9 text-center">:</div>
        <div class="colp-59">{{ @$data->corpName }}</div>
    </div>
    <div class="row" style="font-size: 12pt; margin-bottom:10px">
        <div class="colp-29">Divisi</div>
        <div class="colp-9 text-center">:</div>
        <div class="colp-59">{{ @$data->division }}</div>
    </div>
    <div class="row" style="font-size: 12pt; margin-bottom:10px">
        <div class="colp-29">Posisi</div>
        <div class="colp-9 text-center">:</div>
        <div class="colp-59">{{ @$data->position }}</div>
    </div>
</div>