<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Upload</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idb_); ?>dGridSave()">Create</button>
    </cpModalFooter>
<?php } else if(@$mode == 'showImage') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Show Image</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
    </cpModalFooter>
<?php } ?>

<div id="cp<?php echo e($_idb_); ?>dGridFormContent">
    <form id="cp<?php echo e($_idb_); ?>dGridForm" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="name" name="name" style="width:100%">
                <div id="name_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="str_number" name="str_number" style="width:100%">
                <div id="str_number_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="sip_number" name="sip_number" style="width:100%">
                <div id="sip_number_error" class='form_error'></div>
            </div>
            <?php if(@$mode == 'add') { ?>
                <div class="col-12 col-md-12 mb-3">
                    <input class="form-controls" type="text" id="sign" name="sign" style="width:100%">
                    <div id="sign_error" class='form_error'></div>
                </div>
            <?php } else if(@$mode == 'showImage') { ?>
                <img src="" alt="" id="imageData" style="width:auto">
            <?php } ?>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#name').textbox({
            required:true,
            label:"Fullname",
            labelPosition:'top',
            prompt:"Fullname"
        })
        $eu('#str_number').textbox({
            required:false,
            label:"STR Number",
            labelPosition:'top',
            prompt:"STR Number"
        })
        $eu('#sip_number').textbox({
            required:false,
            label:"SIP Number",
            labelPosition:'top',
            prompt:"SIP Number"
        })
        $eu('#sign').filebox({
            required:true,
            label:"Image (Only .png .jpg .jpeg)",
            labelPosition:'top',
            buttonText: 'Choose Image',
            buttonAlign: 'left'
        });
        euDoLayout();
    })

    function jf<?php echo e($_idb_); ?>dGridSave(){
        preloader_block();
        $eu("#cp<?php echo e($_idb_); ?>dGridForm").form("submit", {
            url: "<?php echo e(@$url_save); ?>",
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

                    cpGlobalModalClose("<?php echo e($cpModalId); ?>");
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('reload');
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearSelections');
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearChecked');
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
            var selData = <?php echo $selData; ?>;
            $eu("#cp<?php echo e($_idb_); ?>dGridForm").form('load', selData);
            $eu('#code').textbox({disabled:true});
            $("#imageData").attr('src', selData.data);
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp<?php echo e($_idb_); ?>dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?><?php /**PATH /home/u1598413/public_sys/dkdmcu_klab__dev/app/Modules/MCU2Format/v5Form.blade.php ENDPATH**/ ?>