<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Import Patient</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idb_); ?>dGridImportTemplate()">TEMPLATE</button>|
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idb_); ?>dGridSave()">Submit</button>
    </cpModalFooter>
<?php } ?>

<div id="cp<?php echo e($_idb_); ?>dGridFormImportContent">
    <form id="cp<?php echo e($_idb_); ?>dGridFormImport" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <input class="" type="text" id="cp<?php echo e($_idb_); ?>filePatient" name="file" style="width:100%">
                <div id="file_error" class='form_error'></div>
            </div>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#cp<?php echo e($_idb_); ?>filePatient').filebox({
            required:true,
            label:"File (Only .xlsx)",
            labelPosition:'top',
            buttonText: 'Choose File',
            buttonAlign: 'left'
        });
        euDoLayout();
    })

    function jf<?php echo e($_idb_); ?>dGridImportTemplate(){
        window.open("<?php echo e($urlb); ?>patientImportTemplate");
    }

    function jf<?php echo e($_idb_); ?>dGridSave(){
        preloader_block();

        $eu("#cp<?php echo e($_idb_); ?>dGridFormImport").form("submit", {
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
                    setTimeout(() => { Swal.close(); }, 3000);

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
                    setTimeout(() => { Swal.close(); }, 3000);

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
<?php } else if(@$mode == 'edit' || @$mode == 'present') { ?>
    <!-- no action -->
<?php } else { ?>
    <script>
        $(function(){
            $("#cp<?php echo e($_idb_); ?>dGridFormImportContent").html("Request not valid.");
        })
    </script>
<?php } ?><?php /**PATH /home/u1598413/public_sys/dkdmcu_klab__dev/app/Modules/MCU2Onsite/v2FormImport.blade.php ENDPATH**/ ?>