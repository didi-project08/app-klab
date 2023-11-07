<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Add New Provider</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_id_); ?>dGridSave()">Create</button>
    </cpModalFooter>
<?php } else if(@$mode == 'edit') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Edit Provider</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_id_); ?>dGridSave()">Update</button>
    </cpModalFooter>
<?php } ?>

<div id="cp<?php echo e($_id_); ?>dGridFormContent">
    <form id="cp<?php echo e($_id_); ?>dGridForm" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php if(@$mode == 'edit') { ?>
            <?php echo method_field('PUT'); ?>
        <?php } ?>
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <input class="form-controls" type="text" id="code" name="code" style="width:100%">
                <div id="code_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="form-controls" type="text" id="title" name="title" style="width:100%">
                <div id="title_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="form-controls" type="text" id="prov" name="prov" style="width:100%">
                <div id="prov_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="form-controls" type="text" id="city" name="city" style="width:100%">
                <div id="city_error" class='form_error'></div>
            </div>
            <div class="col-12 mb-3">
                <input class="form-controls" type="text" id="address" name="address" style="width:100%">
                <div id="address_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="pic_name" name="pic_name" style="width:100%">
                <div id="pic_name_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="form-controls" type="text" id="pic_phone" name="pic_phone" style="width:100%">
                <div id="pic_phone_error" class='form_error'></div>
            </div>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#code').textbox({
            required:true,
            label:"Provider Code",
            labelPosition:'top',
            prompt:"Provider Code",
        })
        $eu('#code').textbox('textbox').css('text-transform','uppercase');
        $eu('#code').textbox('textbox').focus();

        $eu('#title').textbox({
            required:true,
            label:"Provider Name",
            labelPosition:'top',
            prompt:"Provider Name"
        })
        $eu('#prov').textbox({
            required:false,
            label:"Provice",
            labelPosition:'top',
            prompt:"Provice"
        })
        $eu('#city').textbox({
            required:false,
            label:"City",
            labelPosition:'top',
            prompt:"City"
        })
        $eu('#address').textbox({
            required:false,
            label:"Address",
            labelPosition:'top',
            prompt:"Address",
            multiline:true,
            height:100
        })
        $eu('#pic_name').textbox({
            required:true,
            label:"PIC Name",
            labelPosition:'top',
            prompt:"PIC Name"
        })
        $eu('#pic_phone').textbox({
            required:true,
            label:"PIC Phone (WA)",
            labelPosition:'top',
            prompt:"PIC Phone (WA)"
        })
        euDoLayout();
    })

    function jf<?php echo e($_id_); ?>dGridSave(){
        preloader_block();
        $eu("#cp<?php echo e($_id_); ?>dGridForm").form("submit", {
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
                    $eu("#cp<?php echo e($_id_); ?>dGrid").datagrid('reload');
                    $eu("#cp<?php echo e($_id_); ?>dGrid").datagrid('clearSelections');
                    $eu("#cp<?php echo e($_id_); ?>dGrid").datagrid('clearChecked');
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
            $eu("#cp<?php echo e($_id_); ?>dGridForm").form('load', selData);
            $eu('#code').textbox('disable',true);
            $eu('#title').textbox('textbox').focus();
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp<?php echo e($_id_); ?>dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?><?php /**PATH /home/n1731643/public_sys/devapp-mcu/app/Modules/Laboratory/vForm.blade.php ENDPATH**/ ?>