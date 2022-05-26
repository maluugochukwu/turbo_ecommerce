<?php
include_once("libs/dbfunctions.php");
//var_dump($_SESSION);
?>
   <div class="card">
    <div class="card-header">
        <h5 class="card-title">Banner List</h5>
        <h6 class="card-subtitle text-muted">The report contains banners that have you have created.</h6>
    </div>
    <div class="card-body">
        <div id="datatables-basic_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-3">
                    <button  id="search" class="btn btn-info btn-block" onclick="getModal('setup/banner_setup.php','modal_div')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Create Banner</button>
                    <br>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 table-responsive">
                    <table id="page_list" class="table table-striped" >
                        <thead>
                            <tr role="row">
                                <th>S/N</th>
                                <th>Title</th>
                                <th>Image</th>
                                <th>SubTitle</th>
                                <th>Page</th>
                                <th>CTA Text</th>
                                <th>CTA Link</th>
                                <th>Action</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--<script src="../js/sweet_alerts.js"></script>-->
<!--<script src="../js/jquery.blockUI.js"></script>-->
<script>
  var table;
  var editor;
  var op = "Banner.bannerList";
  $(document).ready(function() {
    table = $("#page_list").DataTable({
      processing: true,
      columnDefs: [{
        orderable: false,
        targets: 0
      }],
      serverSide: true,
      paging: true,
      oLanguage: {
        sEmptyTable: "No record was found, please try another query"
      },

      ajax: {
        url: "utilities.php",
        type: "POST",
        data: function(d, l) {
          d.op = op;
          d.li = Math.random();
//          d.start_date = $("#start_date").val();
//          d.end_date = $("#end_date").val();
        }
      }
    });
  });

  function do_filter() {
    table.draw();
  }
    
    function deleteMenu(id)
    {
        let cnf = confirm("Are you sure you want to delete menu?");
        if(cnf == true)
            {
                $.blockUI();
                $.post("utilities.php",{op:"Menu.deleteMenu",menu_id:id},function(re){
                    $.unblockUI();
                    alert(re.response_message);
                    getpage('menu_list.php',"page");
                },'json')
            }
        
    }
    function deleteBanner(id){
        var cnf = confirm("Are you sure you want to delete this banner ?");
        if(cnf)
           {
                $.blockUI();
               $.post('utilities.php',{op:'Banner.deleteBanner',id:id},function(resp)
               {
                   $.unblockUI();
                   if(resp.response_code == 0)
                       {
                          getpage('banner_list.php','page'); 
                       }
                   
               },'json')
           }
    }
    function getModal(url,div)
    {
//        alert('dfd');
        $('#'+div).html("<h2>Loading....</h2>");
//        $('#'+div).block({ message: null });
        $.post(url,{},function(re){
            $('#'+div).html(re);
        })
    }
</script>