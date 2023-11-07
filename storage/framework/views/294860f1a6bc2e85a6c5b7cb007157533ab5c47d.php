<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Add (Input)</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idbf_); ?>dGridSave()">Create</button>
    </cpModalFooter>
<?php } else if(@$mode == 'edit') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Edit (Input)</cpModalTitle>
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
        <input class="d-none" type="text" name="main_parent" value="<?php echo e(@$formData['main_parent']); ?>">
        <input class="d-none" type="text" name="parent" value="<?php echo e(@$formData['parent']); ?>">
        <input class="d-none" type="text" name="level" value="<?php echo e(@$formData['level']); ?>">
        <input class="d-none" type="text" name="header" value="<?php echo e(@$formData['header']); ?>">
        <input class="d-none" type="text" name="sort" value="<?php echo e(@$formData['sort']); ?>">
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>code" name="code" style="width:100%">
                <div id="code_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>name" name="name" style="width:100%">
                <div id="name_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>unit" name="unit" style="width:100%">
                <div id="unit_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>ref_m" name="ref_m" style="width:100%">
                <div id="ref_m_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="i<?php echo e($_idbf_); ?>ref_f" name="ref_f" style="width:100%">
                <div id="ref_f_error" class='form_error'></div>
            </div>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#i<?php echo e($_idbf_); ?>code').textbox({
            required:true,
            label:"Item Code",
            labelPosition:'top',
            prompt:"Item Code"
        });
        $eu('#i<?php echo e($_idbf_); ?>name').textbox({
            required:true,
            label:"Item Name",
            labelPosition:'top',
            prompt:"Item Name"
        });
        $eu('#i<?php echo e($_idbf_); ?>unit').textbox({
            required:true,
            label:"Unit",
            labelPosition:'top',
            prompt:"Unit"
        });
        $eu('#i<?php echo e($_idbf_); ?>ref_m').textbox({
            required:true,
            label:"Normal Value (Male)",
            labelPosition:'top',
            prompt:"Normal Value (Male)"
        });
        $eu('#i<?php echo e($_idbf_); ?>ref_f').textbox({
            required:true,
            label:"Normal Value (Female)",
            labelPosition:'top',
            prompt:"Normal Value (Female)"
        });
    })

    function jf<?php echo e($_idbf_); ?>dGridSave(){
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
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('reload');
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('clearSelections');
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('clearChecked');
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
    <script>
        $eu(function(){
            setTimeout(() => {
                $eu('#i<?php echo e($_idbf_); ?>code').textbox('textbox').focus();
            }, 500);
        })
    </script>
<?php } else if(@$mode == 'edit') { ?>
    <script>
        $eu(function(){
            var selData = <?php echo $selData; ?>;
            $eu("#cp<?php echo e($_idbf_); ?>dGridForm").form('load', selData);
            // $eu('#i<?php echo e($_idbf_); ?>code').textbox('disable',true);
            setTimeout(() => {
                $eu('#i<?php echo e($_idbf_); ?>code').textbox('textbox').focus();
            }, 500);
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp<?php echo e($_idbf_); ?>dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?><?php /**PATH /home/n1731643/public_sys/devapp-mcu/app/Modules/MCUFormat/v2FormInput.blade.php ENDPATH**/ ?>