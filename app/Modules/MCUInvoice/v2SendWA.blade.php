<cpModalSize>modal-md</cpModalSize>
<cpModalTitle>SEND WA</cpModalTitle>
<cpModalFooter>
    <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
    <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idbf_}}dGridSednWAProcess()">Start</button>
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
            </tbody>
        </table>
    </form>
</div>

<script>
    let sendWAStop = 0;
    let sendWAProcess = 0;
    let sendWASuccess = 0;
    let sendWAFailed = 0;

    function jf{{$_idbf_}}dGridSednWAUpdateTotal(){
        $("#count{{$_idbf_}}process").text(sendWAProcess);
        $("#count{{$_idbf_}}success").text(sendWASuccess);
        $("#count{{$_idbf_}}failed").text(sendWAFailed);
    }
    function jf{{$_idbf_}}dGridSednWAProcessStop(){
        sendWAStop = 1;
    }
    function jf{{$_idbf_}}dGridSednWAProcess(){
        sendWAStop = 0;
        sendWAProcess = 0;
        sendWASuccess = 0;
        sendWAFailed = 0;

        let checkedData = $eu("#cp{{$_idb_}}dGrid").datagrid('getChecked'); 
        let patientIdList = [];
        for (let i = 0; i < checkedData.length; i++) {
            patientIdList.push(checkedData[i].id);
        }
        console.log(patientIdList);

        index = 0;
        jf{{$_idbf_}}dGridSednWAProcess2(patientIdList, index);
    }
    function jf{{$_idbf_}}dGridSednWAProcess2(patientIdList = [], index = 0){
        if(sendWAStop == 0){
            if(patientIdList.length > index){
                preloader_none();
                $.ajax({
                    type: 'POST',
                    dataType: "JSON",
                    url:"{{ $url_save }}"+patientIdList[index],
                    data:{
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(r) {
                        if (r.success) {
                            sendWAProcess = sendWAProcess + 1;
                            sendWASuccess = sendWASuccess + 1;
                            jf{{$_idbf_}}dGridSednWAUpdateTotal();

                            index = index + 1;
                            jf{{$_idbf_}}dGridSednWAProcess2(patientIdList, index);
                        }else{
                            sendWAProcess = sendWAProcess + 1;
                            sendWAFailed = sendWAFailed + 1;
                            jf{{$_idbf_}}dGridSednWAUpdateTotal();

                            index = index + 1;
                            jf{{$_idbf_}}dGridSednWAProcess2(patientIdList, index);
                        }
                    },
                    error:function(){
                        sendWAProcess = sendWAProcess + 1;
                        sendWAFailed = sendWAFailed + 1;
                        jf{{$_idbf_}}dGridSednWAUpdateTotal();

                        index = index + 1;
                        jf{{$_idbf_}}dGridSednWAProcess2(patientIdList, index);
                    }
                })
            }else{
                
            }
        }else{
            // action to stop;
        }
    }
</script>