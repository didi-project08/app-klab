<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Add New MCU Event</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_id_); ?>dGridSave()">Create</button>
    </cpModalFooter>
<?php } else if(@$mode == 'edit') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Edit MCU Event</cpModalTitle>
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
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="laboratory_id" name="laboratory_id" style="width:100%"></select>
                <div id="laboratory_id_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="laboratory_id_refer" name="laboratory_id_refer" style="width:100%"></select>
                <div id="laboratory_id_refer_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <input class="" type="text" id="title" name="title" style="width:100%">
                <div id="title_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="date_from" name="date_from" style="width:100%">
                <div id="date_from_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input class="" type="text" id="date_to" name="date_to" style="width:100%">
                <div id="date_to_error" class='form_error'></div>
            </div>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#laboratory_id').combobox({
            required:true,
            label:"Provider",
            labelPosition:'top',
            prompt:"Choose Provider",
            method: "GET",
            url:'<?php echo e($url); ?>laboratoryCombo',
            editable:false,
            valueField:'id',
            textField:'title',
            panelHeight:150,
            onChange:function(newValue,oldValue){
                $eu('#laboratory_id_refer').combobox('reload','<?php echo e($url); ?>laboratoryReferCombo?laboratory_id='+newValue);
            }
        });
        $eu('#laboratory_id').textbox('textbox').focus();
        $eu('#laboratory_id_refer').combobox({
            required:true,
            label:"Refer-To (Execute By)",
            labelPosition:'top',
            prompt:"Choose Provider",
            method: "GET",
            // url:'<?php echo e($url); ?>laboratoryReferCombo',
            editable:false,
            valueField:'id',
            textField:'title',
            panelHeight:150
        });

        $eu('#title').textbox({
            required:true,
            label:"Event Title",
            labelPosition:'top',
            prompt:"Event Title",
        });

        $eu('#date_from').datebox({
            required:true,
            label:"Event From",
            labelPosition:'top',
            prompt:"dd/mm/yyyy",
            formatter:datebox_formatter_ddmmyyyy,
            parser:datebox_parser_ddmmyyyy,
            value:" "
        });
        $eu('#date_to').datebox({
            required:true,
            label:"Event To",
            labelPosition:'top',
            prompt:"dd/mm/yyyy",
            formatter:datebox_formatter_ddmmyyyy,
            parser:datebox_parser_ddmmyyyy,
            value:" "

        });
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
    <script>
        $eu(function(){
            $eu('#laboratory_id').combobox({
                onChange:function(newValue,oldValue){
                    $eu('#laboratory_id_refer').combobox('reload','<?php echo e($url); ?>laboratoryReferCombo?laboratory_id='+newValue);
                    setTimeout(() => {
                        $eu('#laboratory_id_refer').combobox('setValue', newValue);
                    }, 500);
                }
            });
        })
    </script>
<?php } else if(@$mode == 'edit') { ?>
    <script>
        $eu(function(){
            var selData = <?php echo $selData; ?>;
            $eu("#cp<?php echo e($_id_); ?>dGridForm").form('load', selData);
            $eu('#laboratory_id').textbox('disable',true);
            $eu('#laboratory_id_refer').textbox('disable',true);
            $eu('#date_from').datebox('setValue', selData.date_from_dmY_slash);
            $eu('#date_to').datebox('setValue', selData.date_to_dmY_slash);
            $eu('#title').textbox('textbox').focus();
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp<?php echo e($_id_); ?>dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?><?php /**PATH C:\Users\Lenovo\Documents\Envato\viho_all\Viho-Laravel-8\theme\app\Modules/MCUEvent/vForm.blade.php ENDPATH**/ ?>