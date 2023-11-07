<cpModalSize>modal-md</cpModalSize>
<cpModalTitle>SEND WA</cpModalTitle>
<cpModalFooter>
    <button id="btn{{$_idbf_}}Close" class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">C L O S E</button>
    <button id="btn{{$_idbf_}}Start" class="btn btn-outline-primary" type="button" onclick="jf{{$_idbf_}}dGridSendWAProcess()">S T A R T</button>
    <button id="btn{{$_idbf_}}Stop" class="btn btn-outline-danger d-none" type="button" onclick="jf{{$_idbf_}}dGridSendWAProcessStop()">S T O P</button>
</cpModalFooter>

<div id="cp{{$_idbf_}}dGridFormContent">
    <form id="cp{{$_idbf_}}dGridForm" method="post" enctype="multipart/form-data">
        @csrf
        <?php if(@$mode == 'edit' || @$mode == 'present') { ?>
            @method('PUT')
        <?php } ?>
        <table class="table table-striped table-border">
            <tbody>
                <tr>
                    <td><b>Process</b></td>
                    <td>:</td>
                    <td id="count{{$_idbf_}}process">0</td>
                </tr>
                <tr>
                    <td><b>Success</b></td>
                    <td>:</td>
                    <td id="count{{$_idbf_}}success">0</td>
                </tr>
                <tr>
                    <td><b>Failed</b></td>
                    <td>:</td>
                    <td id="count{{$_idbf_}}failed">0</td>
                </tr>
                <tr>
                    <td><b>Processing Time</b></td>
                    <td>:</td>
                    <td id="count{{$_idbf_}}ProcessingTime">0</td>
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

    function jf{{$_idbf_}}dGridSendWAUpdateProcessingTime(){
        sendWAProcessingTime = sendWAProcessingTime + 1;
        $("#count{{$_idbf_}}ProcessingTime").text(sendWAProcessingTime);
        if(sendWAStop == 0){
            setTimeout(() => {
                jf{{$_idbf_}}dGridSendWAUpdateProcessingTime();
            }, 1000);
        }
    }
    function jf{{$_idbf_}}dGridSendWAUpdateTotal(){
        $("#count{{$_idbf_}}process").text(sendWAProcess);
        $("#count{{$_idbf_}}success").text(sendWASuccess);
        $("#count{{$_idbf_}}failed").text(sendWAFailed);
    }
    function jf{{$_idbf_}}dGridSendWAProcessStop(){
        sendWAStop = 1;
        $("#btn{{$_idbf_}}Close").removeClass('d-none');
        $("#btn{{$_idbf_}}Start").removeClass('d-none');
        $("#btn{{$_idbf_}}Stop").removeClass('d-none');
        $("#btn{{$_idbf_}}Stop").addClass('d-none');
        $("#{{ $_GET['cpModalId'] }} .modal-dialog .modal-content .modal-header .btn-close").removeClass('d-none');
    }
    function jf{{$_idbf_}}dGridSendWAProcess(){
        $("#btn{{$_idbf_}}Close").removeClass('d-none');
        $("#btn{{$_idbf_}}Start").removeClass('d-none');
        $("#btn{{$_idbf_}}Stop").removeClass('d-none');
        $("#btn{{$_idbf_}}Close").addClass('d-none');
        $("#btn{{$_idbf_}}Start").addClass('d-none');
        $("#{{ $_GET['cpModalId'] }} .modal-dialog .modal-content .modal-header .btn-close").removeClass('d-none');
        $("#{{ $_GET['cpModalId'] }} .modal-dialog .modal-content .modal-header .btn-close").addClass('d-none');

        sendWAStop = 0;
        sendWAProcess = 0;
        sendWASuccess = 0;
        sendWAFailed = 0;
        sendWAProcessingTime = 0;

        jf{{$_idbf_}}dGridSendWAUpdateProcessingTime();

        let checkedData = $eu("#cp{{$_idb_}}dGrid").datagrid('getChecked'); 
        let patientIdList = [];
        for (let i = 0; i < checkedData.length; i++) {
            patientIdList.push(checkedData[i].id);
        }
        // console.log(patientIdList);

        index = 0;
        jf{{$_idbf_}}dGridSendWAProcess2(patientIdList, index);
    }
    function jf{{$_idbf_}}dGridSendWAProcess2(patientIdList = [], index = 0){
        if(sendWAStop == 0){
            if(patientIdList.length > index){
                $.ajax({
                    type: 'POST',
                    dataType: "JSON",
                    url:"{{ $url_save }}"+patientIdList[index],
                    data:{
                        "_token": "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        preloader_none();
                    },
                    success: function(r) {
                        if (r.success) {
                            sendWAProcess = sendWAProcess + 1;
                            sendWASuccess = sendWASuccess + 1;
                            jf{{$_idbf_}}dGridSendWAUpdateTotal();

                            index = index + 1;
                            jf{{$_idbf_}}dGridSendWAProcess2(patientIdList, index);
                        }else{
                            sendWAProcess = sendWAProcess + 1;
                            sendWAFailed = sendWAFailed + 1;
                            jf{{$_idbf_}}dGridSendWAUpdateTotal();

                            index = index + 1;
                            jf{{$_idbf_}}dGridSendWAProcess2(patientIdList, index);
                        }

                        if(patientIdList.length == index+1){
                            sendWAStop == 1;
                        }

                    },
                    error:function(){
                        sendWAProcess = sendWAProcess + 1;
                        sendWAFailed = sendWAFailed + 1;
                        jf{{$_idbf_}}dGridSendWAUpdateTotal();

                        index = index + 1;
                        jf{{$_idbf_}}dGridSendWAProcess2(patientIdList, index);
                    }
                })
            }else{
                jf{{$_idbf_}}dGridSendWAProcessStop();
            }
        }else{
            jf{{$_idbf_}}dGridSendWAProcessStop();
        }
    }
</script>