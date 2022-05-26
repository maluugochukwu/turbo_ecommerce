<?php
include_once("libs/dbfunctions.php");
$dbobject = new dbobject();
$count = $dbobject->db_query("SELECT id FROM about_us WHERE type ='WELCOME_MESSAGE'",false);
$count2 = $dbobject->db_query("SELECT id FROM about_us WHERE type ='BANNER_IMAGE'",false);
?>
   <div class="card">
    <div class="card-header">
        <h5 class="card-title">About List</h5>
        <h6 class="card-subtitle text-muted">The report contains about us page that have been setup in the system.</h6>
    </div>
    <div class="card-body">
        <?php
        if($count == 0)
        {
        ?>
        <a class="btn btn-primary" onclick="getModal('setup/about_setup.php','modal_div')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Create Welcome Message</a>
        <?php
        }
        ?>
        <?php
        if($count2 == 0)
        {
        ?>
        <a class="btn btn-success" onclick="getModal('setup/about_image.php','modal_div')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Create Banner Image</a>
        <?php
        }
        ?>
      
        <div id="datatables-basic_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-3">
                    <label for=""></label>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 table-responsive">
                    <table id="page_list" class="table table-striped" >
                        <thead>
                            <tr role="row">
                                <th>S/N</th>
                               <th>Type</th>
                                <th>Value</th>
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
  var op = "About.aboutList";
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