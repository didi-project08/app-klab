<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Add New Package</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idbf_); ?>dGridSave()">Submit</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idbf_); ?>dGridSave(1)">Submit & Create New</button>
    </cpModalFooter>
<?php } else if(@$mode == 'edit') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Edit Package</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idbf_); ?>dGridSave()">Update</button>
    </cpModalFooter>
<?php } ?>

<div id="cp<?php echo e($_idbf_); ?>dGridFormContent">
    <form id="cp<?php echo e($_idbf_); ?>dGridForm" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php if(@$mode == 'edit') { ?>
            <?php echo method_field('PUT'); ?>
        <?php } ?>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>title" name="title" style="width:100%">
                <div id="title_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>price_lab" name="price_lab" style="width:100%; text-align:right">
                <div id="price_lab_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>price_corporate" name="price_corporate" style="width:100%; text-align:right">
                <div id="price_corporate_error" class='form_error'></div>
            </div>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#i<?php echo e($_idbf_); ?>title').textbox({
            required:true,
            label:"Package Name",
            labelPosition:'top',
            prompt:"Package Name"
        });
        $eu('#i<?php echo e($_idbf_); ?>price_lab').numberbox({
            required:true,
            label:"Price (Provider)",
            labelPosition:'top',
            prompt:"Price (Provider)",
            min:0,
            precision:2,
            groupSeparator:".",
            decimalSeparator:","
        });
        $eu('#i<?php echo e($_idbf_); ?>price_corporate').numberbox({
            required:true,
            label:"Price (Corporate)",
            labelPosition:'top',
            prompt:"Price (Corporate)",
            min:0,
            precision:2,
            groupSeparator:".",
            decimalSeparator:","
        });
        setTimeout(() => {
            $eu('#i<?php echo e($_idbf_); ?>title').textbox('textbox').focus();
        }, 500);
    })

    function jf<?php echo e($_idbf_); ?>dGridSave(createNew = 0){
        preloader_block();
        $eu("#cp<?php echo e($_idbf_); ?>dGridForm").form("submit", {
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

                    if(createNew == 1){
                        setTimeout(() => {
                            jf<?php echo e($_idb_); ?>dGridAdd();
                        }, 1000);
                    }
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
<?php } else if(@$mode == 'edit') { ?>
    <script>
        $(function(){
            var selData = <?php echo $selData; ?>;
            $eu("#cp<?php echo e($_idbf_); ?>dGridForm").form('load', selData);
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp<?php echo e($_idbf_); ?>dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?><?php /**PATH /home/n1731643/public_sys/devapp-mcu/app/Modules/MCUFormat/v3Form.blade.php ENDPATH**/ ?>