<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Upload</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf{{$_idb_}}dGridSave()">Create</button>
    </cpModalFooter>
<?php } else if(@$mode == 'showImage') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Show Image</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('{{$cpModalId}}')">Close</button>
    </cpModalFooter>
<?php } ?>

<div id="cp{{$_idb_}}dGridFormContent">
    <form id="cp{{$_idb_}}dGridForm" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="code" name="code" style="width:100%">
                <div id="code_error" class='form_error'></div>
            </div>
            <?php if(@$mode == 'add') { ?>
                <div class="col-12 col-md-12 mb-3">
                    <input class="form-controls" type="text" id="data" name="data" style="width:100%">
                    <div id="data_error" class='form_error'></div>
                </div>
            <?php } else if(@$mode == 'showImage') { ?>
                <img src="" alt="" id="imageData" style="width:auto">
            <?php } ?>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#code').textbox({
            required:true,
            label:"Title",
            labelPosition:'top',
            prompt:"Title"
        })
        $eu('#data').filebox({
            required:true,
            label:"Image (Only .png .jpg .jpeg)",
            labelPosition:'top',
            buttonText: 'Choose Image',
            buttonAlign: 'left'
        });
        euDoLayout();
    })

    function jf{{$_idb_}}dGridSave(){
        preloader_block();
        $eu("#cp{{$_idb_}}dGridForm").form("submit", {
            url: "{{ @$url_save }}",
            onSubmit: function() {
                if(!$eu(this).form('validate')){
                    preloader_none();
                    return $eu(this).form('validate');
                }
            },
            success: function(res) {
                preloader_none();

                var r = JSON.parse(res);

                $(".form_error").html("");

                if (r.success) {
                    Swal.fire({
                        width:'300px',
                        icon: 'success',
                        title: 'Success',
                        text: r.message,
                        showConfirmButton:false
                    })
                    setTimeout(() => { Swal.close(); }, 1000);

                    cpGlobalModalClose("{{$cpModalId}}");
                    $eu("#cp{{$_idb_}}dGrid").datagrid('reload');
                    $eu("#cp{{$_idb_}}dGrid").datagrid('clearSelections');
                    $eu("#cp{{$_idb_}}dGrid").datagrid('clearChecked');
                } else {
                    Swal.fire({
                        width:'300px',
                        icon: 'error',
                        title: 'Oops...',
                        text: r.message,
                        showConfirmButton:false
                    })
                    setTimeout(() => { Swal.close(); }, 2000);

                    if (r.form_error) {
                        var form_error_array = r.form_error_array;
                        for (key in form_error_array) {
                            for (key2 in form_error_array[key]) {
                                if(key2 == 0){
                                    $("#" + key + "_error").append(form_error_array[key][key2]);
                                }else{
                                    $("#" + key + "_error").append("<br>"+form_error_array[key][key2]);
                                }
                            }
                        }
                    }
                }
            }
        })
    }
</script>

<?php if(@$mode == 'add') { ?>
    <!-- no action -->
<?php } else if(@$mode == 'showImage') { ?>
    <script>
        $(function(){
            var selData = {!! $selData !!};
            $eu("#cp{{$_idb_}}dGridForm").form('load', selData);
            $eu('#code').textbox({disabled:true});
            $("#imageData").attr('src', selData.data);
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp{{$_idb_}}dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?>