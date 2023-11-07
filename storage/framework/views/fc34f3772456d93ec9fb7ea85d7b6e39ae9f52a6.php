<?php if($confSize == '110x52-2' && 1==1) { ?>
    <p style="margin: 0; font-size:8px; color:white">.</p>
    <div style="width:100%" class="font-6px; background-color:none;">
        <?php for ($i=1; $i <= 3; $i++) { ?>
                <?php if($i == 2) { ?>
                    <div style="float:left; width:11%">&nbsp;</div>
                <?php }else{ ?>
                    <div style="float:left; width:44%;">
                        <div style="background-color:none; border:none; padding:0px 0px 0px 5px">
                            <div class="row" style="background-color:none; padding:0px">
                                <div style="width:30%; float:left; font-size:8px; text-align: left; margin-left:5pt;">
                                    <p style="margin: 0px; margin-bottom: 1pt;"><?php echo @$barcode; ?></p>
                                </div>
                                <div style="width:65%; float:left; font-size:8px; text-align: left; margin-left:5pt;">
                                    <p style="margin: 0px; margin-bottom: 1pt;"><?php echo @$data->$confField; ?></p>
                                    <p style="margin: 0px; margin-bottom: 1pt;"><?php echo strtoupper(@$data->name); ?></p>
                                    <p style="margin: 0px; margin-bottom: 1pt;"><?php echo @$data->gender." / ".@$data->dob_dmY_slash; ?></p>
                                    <p style="margin: 0px; margin-bottom: 1pt;"><?php echo @$data->actual_date_dmY_slash; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>  
        <?php } ?>
    </div>
<?php } ?>
<?php if($confSize == '110x52-2' && 1==2) { ?>
    <p style="margin: 0; font-size:8px; color:white">.</p>
    <div style="width:100%" class="font-6px; background-color:none;">
        <?php for ($i=1; $i <= 3; $i++) { ?>
                <?php if($i == 2) { ?>
                    <div style="float:left; width:11%">&nbsp;</div>
                <?php }else{ ?>
                    <div style="float:left; width:44%;">
                        <div style="background-color:none; border:none; padding:0px 0px 0px 5px">
                            <div class="row" style="background-color:none; padding:0px">
                                <div class="col-12" style="font-size:8px; text-align: left; margin-left:5pt;">
                                    <p style="margin: 0px; margin-bottom: 1pt;"><?php echo @$barcode; ?></p>
                                    <p style="margin: 0px; margin-bottom: 1pt;"><?php echo @$data->$confField; ?></p>
                                    <p style="margin: 0px; margin-bottom: 1pt;"><?php echo strtoupper(@$data->name); ?></p>
                                    <p style="margin: 0px; margin-bottom: 1pt;"><?php echo @$data->gender." / ".@$data->dob_dmY_slash; ?></p>
                                    <p style="margin: 0px; margin-bottom: 1pt;"><?php echo @$data->actual_date_dmY_slash; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>  
        <?php } ?>
    </div>
<?php } ?><?php /**PATH /home/u1598413/public_sys/dkdmcu_klab__dev/app/Modules/MCU2Onsite/v2Label.blade.php ENDPATH**/ ?>