<cpModalSize>modal-md</cpModalSize>
<cpModalTitle>Confirm MCU Event</cpModalTitle>
<cpModalFooter>
    <button class="btn btn-outline-danger" type="button" onclick="cpGlobalModalClose('<?php echo e($cpModalId); ?>')">Close</button>
    <button class="btn btn-outline-primary" type="button" onclick="jf<?php echo e($_idb_); ?>dGridSave()">Confirm</button>
</cpModalFooter>

<div id="cp<?php echo e($_idb_); ?>dGridFormContent">
    <form id="cp<?php echo e($_idb_); ?>dGridForm" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="i<?php echo e($_idb_); ?>status" name="status" style="width:100%"></select>
                <div id="status_error" class='form_error'></div>
            </div>
            <div class="col-12 col-md-12 mb-3">
                <select class="" id="i<?php echo e($_idb_); ?>confirm_note" name="confirm_note" style="width:100%"></select>
                <div id="confirm_note_error" class='form_error'></div>
            </div>
        </div>
    </form>
</div>

<script>
    $eu(function(){
        $eu('#i<?php echo e($_idb_); ?>status').combobox({
            required:true,
            label:"Status",
            labelPosition:'top',
            prompt:"Choose Status",
            editable:false,
            valueField:'id',
            textField:'title',
            panelHeight:'auto',
            data:[
                {id:'sAccepted',title:"Accept", selected:true},
                {id:'sDeclined',title:"Decline"},
            ]
        });
        $eu('#i<?php echo e($_idb_); ?>confirm_note').textbox({
            required:true,
            label:"Note",
            labelPosition:'top',
            prompt:"Note",
            multiline:true,
            height:150
        });
        $eu('#confirm_note').textbox('textbox').focus();
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
</script><?php /**PATH C:\Users\Lenovo\Documents\Envato\viho_all\Viho-Laravel-8\theme\app\Modules/MCUEvent/v1FormConfirm.blade.php ENDPATH**/ ?>