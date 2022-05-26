<?php
include_once("libs/dbfunctions.php");
//var_dump($_SESSION);
?>
   <div class="card">
    <div class="card-header">
        <h5 class="card-title">Job Category List</h5>
        <h6 class="card-subtitle text-muted">The report contains job category that have been setup in the system.</h6>
    </div>
    <div class="card-body">
      <a class="btn btn-info" onclick="getModal('setup/job_category_setup.php','modal_div')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Create New Category</a>
        <div id="datatables-basic_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
<!--
            <div class="row">
                <div class="col-sm-3">
                    <label for="">Create Role</label>
                </div>
            </div>
-->
            <div class="row">
                <div class="col-sm-12 table-responsive">
                    <table id="page_list" class="table table-striped " >
                        <thead>
                            <tr role="row">
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Status</th>
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
  var op = "JobCategory.category_list";
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
    
    
    function getModal(url,div)
    {
//        alert('dfd');
        $('#'+div).html("<h2>Loading....</h2>");
//        $('#'+div).block({ message: null });
        $.post(url,{},function(re){
            $('#'+div).html(re);
        })
    }
    function changeCatStatus(stat,id)
    {
        let cnf = confirm("Are you sure you want to change status this job category?");
        if(cnf == true)
        {
            $.blockUI();
            $.post("utilities.php",{op:"JobCategory.changeCatStatus",id:id,status:stat},function(re){
                $.unblockUI();
                alert(re.response_message);
                getpage('job_category_list.php',"page");
            },'json')
        }
    }
</script>