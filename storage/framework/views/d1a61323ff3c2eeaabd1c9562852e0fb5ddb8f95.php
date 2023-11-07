<?php if(@$mode == 'add') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Add New MCU Project</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idb_); ?>dGridSave()">Create</button>
    </cpModalFooter>
<?php } else if(@$mode == 'edit') { ?>
    <cpModalSize>modal-md</cpModalSize>
    <cpModalTitle>Edit MCU Project</cpModalTitle>
    <cpModalFooter>
        <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
        <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idb_); ?>dGridSave()">Update</button>
    </cpModalFooter>
<?php } ?>

<div id="cp<?php echo e($_idb_); ?>dGridFormContent">
    <form id="cp<?php echo e($_idb_); ?>dGridForm" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php if(@$mode == 'edit') { ?>
            <?php echo method_field('PUT'); ?>
        <?php } ?>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="client_id" name="client_id" style="width:100%"></select>
                <div id="client_id_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="mcu2_format_id" name="mcu2_format_id" style="width:100%"></select>
                <div id="mcu2_format_id_error" class='form_error'></div>
            </div>
            <br>
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
            <div class="col-12 col-md-12 mb-3">
                <input class="" type="text" id="desc" name="desc" style="width:100%">
                <div id="desc_error" class='form_error'></div>
            </div>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#title').textbox({
            required:true,
            label:"Event Title",
            labelPosition:'top',
            prompt:"Event Title",
        });
        
        $eu('#client_id').combobox({
            required:true,
            label:"Client",
            labelPosition:'top',
            prompt:"Choose Client",
            method: "GET",
            // url:'<?php echo e($urlb); ?>readClientCombo',
            editable:false,
            valueField:'id',
            textField:'title_mod',
            panelHeight:150
        });
        $eu('#mcu2_format_id').combobox({
            required:true,
            label:"MCU Format",
            labelPosition:'top',
            prompt:"Choose MCU Format",
            method: "GET",
            // url:'<?php echo e($urlb); ?>readMcu2FormatCombo',
            editable:false,
            valueField:'id',
            textField:'title',
            panelHeight:150
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
        $eu('#desc').textbox({
            required:false,
            label:"Description",
            labelPosition:'top',
            prompt:"Description",
            multiline:true,
            height:150
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
    <script>
        $eu(function(){
            $eu('#client_id').combobox({
                url:'<?php echo e($url); ?>readClientCombo?selectFirst=0',
            });
            $eu('#mcu2_format_id').combobox({
                url:'<?php echo e($url); ?>readMcu2FormatCombo?selectFirst=0',
            });
        })
    </script>
<?php } else if(@$mode == 'edit') { ?>
    <script>
        $eu(function(){
            $eu('#client_id').combobox({
                url:'<?php echo e($url); ?>readClientCombo?delete=-1&selectFirst=0',
            });
            $eu('#mcu2_format_id').combobox({
                url:'<?php echo e($url); ?>readMcu2FormatCombo?delete=-1&selectFirst=0',
            });

            var selData = <?php echo $selData; ?>;
            $eu("#cp<?php echo e($_idb_); ?>dGridForm").form('load', selData);
            $eu('#client_id').textbox('disable',true);
            $eu('#mcu2_format_id').textbox('disable',true);
            $eu('#date_from').datebox('setValue', selData.date_from_dmY_slash);
            $eu('#date_to').datebox('setValue', selData.date_to_dmY_slash);
            $eu('#title').textbox('textbox').focus();
        })
    </script>
<?php } else { ?>
    <script>
        $(function(){
            $("#cp<?php echo e($_idb_); ?>dGridFormContent").html("Request not valid.");
        })
    </script>
<?php } ?><?php /**PATH /home/u1598413/public_sys/devapp-mcu/app/Modules/MCU2Onsite/v1Form.blade.php ENDPATH**/ ?>