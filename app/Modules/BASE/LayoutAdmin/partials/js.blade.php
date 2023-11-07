<script src="{{asset('assets/easyui/jquery.min.js')}}"></script>
<script src="{{asset('assets/easyui/jquery.easyui.min.js')}}"></script>
<script>
    var $eu = $.noConflict(true)
    // var base_url = "https://ebmndev000.ackcloud.net/";
</script>

<script src="{{asset('assets/js/jquery-3.5.1.min.js')}}"></script>
<!-- feather icon js-->
<script src="{{asset('assets/js/icons/feather-icon/feather.min.js')}}"></script>
<script src="{{asset('assets/js/icons/feather-icon/feather-icon.js')}}"></script>
<!-- Sidebar jquery-->
<script src="{{asset('assets/js/sidebar-menu.js')}}"></script>
<script src="{{asset('assets/js/config.js')}}"></script>
<!-- Bootstrap js-->
<script src="{{asset('assets/js/bootstrap/popper.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap/bootstrap.min.js')}}"></script>

<script>
    $(function(){
        // $('.modal').on('shown.bs.modal', function (e) {
        //     euDoLayout();
        // })
        euDoLayout();
    })

    $(document).ajaxStart(function() {
        preloader_block();
    });
    $(document).ajaxStop(function() {
        preloader_none();
    });
    $eu(document).ajaxStart(function() {
        preloader_block();
    });
    $eu(document).ajaxStop(function() {
        preloader_none();
    });
    
    function euDoLayout(){
        setTimeout(() => {
            $eu('body').panel('doLayout');
        }, 500);
    }
    function preloader_block(){
        $(".preloader").css("display","block");
    }
    function preloader_none(){
        $(".preloader").css("display","none");
    }

    function datebox_formatter_ddmmyyyy(date){
        var y = date.getFullYear();
        var m = date.getMonth()+1;
        var d = date.getDate();
        return (d<10?('0'+d):d)+'/'+(m<10?('0'+m):m)+'/'+y;
    }
    function datebox_parser_ddmmyyyy(s){
        if (!s) return new Date();
        var ss = (s.split('/'));
        var y = parseInt(ss[2],10);
        var m = parseInt(ss[1],10);
        var d = parseInt(ss[0],10);
        if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
            return new Date(y,m-1,d);
        } else {
            return new Date();
        }
    }

    function cpGlobalModal(cpModalId, cpModalUrl){
        if($('div#cpModalFrame').length == 0){
            $("body").append("<div id='cpModalFrame'></div>");
        }

        let cpModalFrame = $("#cpModalFrame");
        let cpModal = "";

        cpModal += '<div class="modal fade" id="'+cpModalId+'" tabindex="-1" role="dialog" aria-labelledby="'+cpModalId+'" aria-hidden="true">';
            cpModal += '<div class="modal-dialog modal-dialog-centered modal-sm" role="document">';
                cpModal += '<div class="modal-content">';
                    cpModal += '<div class="modal-header">';
                        cpModal += '<h5 class="modal-title txt-primary" style="font-weight:bold">Title</h5>';
                        cpModal += '<button class="btn-close" type="button" onclick="cpGlobalModalClose(\''+cpModalId+'\')" aria-label="Close"></button>';
                    cpModal += '</div>';
                    cpModal += '<div class="modal-body">';
                        cpModal += 'Loading Content...';
                        cpModal += '</div>';
                    cpModal += '<div class="modal-footer">';
                        cpModal += 'Footer';
                    cpModal += '</div>';
                cpModal += '</div>';
            cpModal += '</div>';
        cpModal += '</div>';

        $("#"+cpModalId).remove();
        cpModalFrame.append(cpModal);

        cpModalUrl = qstringUpdate(cpModalUrl, "cpModalId", cpModalId);

        preloader_block();
        $("#"+cpModalId+" .modal-dialog .modal-content .modal-body").load(cpModalUrl, function(){
            preloader_none();
            let cpModalSize = $("cpModalSize").html();
            $("#"+cpModalId+" cpModalSize").remove();
            $("#"+cpModalId+" .modal-dialog").removeClass("modal-sm");
            $("#"+cpModalId+" .modal-dialog").removeClass("modal-md");
            $("#"+cpModalId+" .modal-dialog").removeClass("modal-lg");
            $("#"+cpModalId+" .modal-dialog").addClass(cpModalSize);

            let cpModalTitle = $("cpModalTitle").html();
            $("#"+cpModalId+" cpModalTitle").remove();
            $("#"+cpModalId+" .modal-dialog .modal-content .modal-header .modal-title").html(cpModalTitle);

            let cpModalFooter = $("#"+cpModalId+" cpModalFooter").html();
            $("#"+cpModalId+" cpModalFooter").remove();
            $("#"+cpModalId+" .modal-dialog .modal-content .modal-footer").html(cpModalFooter);

            // window.history.pushState(null, null, "#modal-open");
            // preloader_none();
            // setTimeout(function(){
            //     $(".my_btn_request_camera_permissions").trigger("click");
            // },1000)
        });

    }
    function cpGlobalModalOpen(cpModalId){
        $('#'+cpModalId).modal('show');
        euDoLayout();
    }
    function cpGlobalModalClose(cpModalId, destroy = false){
        $('#'+cpModalId).modal('hide');
        if(destroy){
            $("#"+cpModalId).remove();
        }
        if($('.modal.show').length > 0){
            setTimeout(() => {
                $("body").addClass("modal-open");
            }, 500);
        }
    }
    function qstringUpdate(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        }
        else {
            return uri + separator + key + "=" + value;
        }
    }
</script>

<!-- Plugins JS start-->
@stack('scripts')
<!-- Plugins JS Ends-->
<!-- Theme js-->
<script src="{{asset('assets/js/script.js')}}"></script>
<!-- <script src="{{asset('assets/js/theme-customizer/customizer.js')}}"></script> -->
<!-- Plugin used-->
@stack('scripts2')