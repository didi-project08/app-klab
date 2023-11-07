<cpModalSize>modal-md</cpModalSize>
<cpModalTitle>SEND WA</cpModalTitle>
<cpModalFooter>
    <button id="btn<?php echo e($_idbf_); ?>Close" class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">C L O S E</button>
    <button id="btn<?php echo e($_idbf_); ?>Start" class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idbf_); ?>dGridSendWAProcess()">S T A R T</button>
    <button id="btn<?php echo e($_idbf_); ?>Stop" class="btn btn-outline-danger d-none" type="button" onclick="jf<?php echo e($_idbf_); ?>dGridSendWAProcessStop()">S T O P</button>
</cpModalFooter>

<div id="cp<?php echo e($_idbf_); ?>dGridFormContent">
    <form id="cp<?php echo e($_idbf_); ?>dGridForm" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php if(@$mode == 'edit' || @$mode == 'present') { ?>
            <?php echo method_field('PUT'); ?>
        <?php } ?>
        <table class="table table-striped table-border">
            <tbody>
                <tr>
                    <td><b>Process</b></td>
                    <td>:</td>
                    <td id="count<?php echo e($_idbf_); ?>process">0</td>
                </tr>
                <tr>
                    <td><b>Success</b></td>
                    <td>:</td>
                    <td id="count<?php echo e($_idbf_); ?>success">0</td>
                </tr>
                <tr>
                    <td><b>Failed</b></td>
                    <td>:</td>
                    <td id="count<?php echo e($_idbf_); ?>failed">0</td>
                </tr>
                <tr>
                    <td><b>Processing Time</b></td>
                    <td>:</td>
                    <td id="count<?php echo e($_idbf_); ?>ProcessingTime">0</td>
                </tr>
            </tbody>
        </table>
    </form>
</div>

<script>
    let sendWAStop = 0;
    let sendWAProcess = 0;
    let sendWASuccess = 0;
    let sendWAFailed = 0;
    let sendWAProcessingTime = 0;

    function jf<?php echo e($_idbf_); ?>dGridSendWAUpdateProcessingTime(){
        sendWAProcessingTime = sendWAProcessingTime + 1;
        $("#count<?php echo e($_idbf_); ?>ProcessingTime").text(sendWAProcessingTime);
        if(sendWAStop == 0){
            setTimeout(() => {
                jf<?php echo e($_idbf_); ?>dGridSendWAUpdateProcessingTime();
            }, 1000);
        }
    }
    function jf<?php echo e($_idbf_); ?>dGridSendWAUpdateTotal(){
        $("#count<?php echo e($_idbf_); ?>process").text(sendWAProcess);
        $("#count<?php echo e($_idbf_); ?>success").text(sendWASuccess);
        $("#count<?php echo e($_idbf_); ?>failed").text(sendWAFailed);
    }
    function jf<?php echo e($_idbf_); ?>dGridSendWAProcessStop(){
        sendWAStop = 1;
        $("#btn<?php echo e($_idbf_); ?>Close").removeClass('d-none');
        $("#btn<?php echo e($_idbf_); ?>Start").removeClass('d-none');
        $("#btn<?php echo e($_idbf_); ?>Stop").removeClass('d-none');
        $("#btn<?php echo e($_idbf_); ?>Stop").addClass('d-none');
        $("#<?php echo e($_GET['cpModalId']); ?> .modal-dialog .modal-content .modal-header .btn-close").removeClass('d-none');
    }
    function jf<?php echo e($_idbf_); ?>dGridSendWAProcess(){
        $("#btn<?php echo e($_idbf_); ?>Close").removeClass('d-none');
        $("#btn<?php echo e($_idbf_); ?>Start").removeClass('d-none');
        $("#btn<?php echo e($_idbf_); ?>Stop").removeClass('d-none');
        $("#btn<?php echo e($_idbf_); ?>Close").addClass('d-none');
        $("#btn<?php echo e($_idbf_); ?>Start").addClass('d-none');
        $("#<?php echo e($_GET['cpModalId']); ?> .modal-dialog .modal-content .modal-header .btn-close").removeClass('d-none');
        $("#<?php echo e($_GET['cpModalId']); ?> .modal-dialog .modal-content .modal-header .btn-close").addClass('d-none');

        sendWAStop = 0;
        sendWAProcess = 0;
        sendWASuccess = 0;
        sendWAFailed = 0;
        sendWAProcessingTime = 0;

        jf<?php echo e($_idbf_); ?>dGridSendWAUpdateProcessingTime();

        let checkedData = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getChecked'); 
        let patientIdList = [];
        for (let i = 0; i < checkedData.length; i++) {
            patientIdList.push(checkedData[i].id);
        }
        // console.log(patientIdList);

        index = 0;
        jf<?php echo e($_idbf_); ?>dGridSendWAProcess2(patientIdList, index);
    }
    function jf<?php echo e($_idbf_); ?>dGridSendWAProcess2(patientIdList = [], index = 0){
        if(sendWAStop == 0){
            if(patientIdList.length > index){
                $.ajax({
                    type: 'POST',
                    dataType: "JSON",
                    url:"<?php echo e($url_save); ?>"+patientIdList[index],
                    data:{
                        "_token": "<?php echo e(csrf_token()); ?>"
                    },
                    beforeSend: function() {
                        preloader_none();
                    },
                    success: function(r) {
                        if (r.success) {
                            sendWAProcess = sendWAProcess + 1;
                            sendWASuccess = sendWASuccess + 1;
                            jf<?php echo e($_idbf_); ?>dGridSendWAUpdateTotal();

                            index = index + 1;
                            jf<?php echo e($_idbf_); ?>dGridSendWAProcess2(patientIdList, index);
                        }else{
                            sendWAProcess = sendWAProcess + 1;
                            sendWAFailed = sendWAFailed + 1;
                            jf<?php echo e($_idbf_); ?>dGridSendWAUpdateTotal();

                            index = index + 1;
                            jf<?php echo e($_idbf_); ?>dGridSendWAProcess2(patientIdList, index);
                        }

                        if(patientIdList.length == index+1){
                            sendWAStop == 1;
                        }

                    },
                    error:function(){
                        sendWAProcess = sendWAProcess + 1;
                        sendWAFailed = sendWAFailed + 1;
                        jf<?php echo e($_idbf_); ?>dGridSendWAUpdateTotal();

                        index = index + 1;
                        jf<?php echo e($_idbf_); ?>dGridSendWAProcess2(patientIdList, index);
                    }
                })
            }else{
                jf<?php echo e($_idbf_); ?>dGridSendWAProcessStop();
            }
        }else{
            jf<?php echo e($_idbf_); ?>dGridSendWAProcessStop();
        }
    }
</script><?php /**PATH /home/n1731643/public_sys/devapp-mcu/app/Modules/MCUEvent/v2SendWA.blade.php ENDPATH**/ ?>