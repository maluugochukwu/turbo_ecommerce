<?php
include_once("libs/dbfunctions.php");
//include_once("class/pagination.php");
//$paginationObj = new Pagination();
//var_dump($_SESSION);
?>
   <div class="card">
    <div class="card-header">
        <h5 class="card-title">Job Posting List</h5>
        <h6 class="card-subtitle text-muted">The report contains Jobs that have been setup in the system.</h6>
    </div>
    <div class="card-body">
      <a class="btn btn-info" onclick="getModal('setup/job_setup.php','modal_div')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Create New Job</a>
        <div id="datatables-basic_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-3">
                    <label for=""></label>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 table-responsive">
                    <table id="page_listww" class="table table-striped " >
                        <thead>
                            <tr role="row">
                                <th>S/N</th>
<!--                                <th>Menu ID</th>-->
                                <th>Job Title</th>
                                <th>Location</th>
                                <th>Closing Date</th>
                                <th>Comapny Name</th>
                                <th>Job Description</th>
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
<?php
//    $sql    = "SELECT * FROM userdata";
//    $result = $paginationObj->paginate(10)->prepareData($sql);
//
//    echo $paginationObj->links("page_id");
?>
<!--<script src="../js/sweet_alerts.js"></script>-->
<!--<script src="../js/jquery.blockUI.js"></script>-->
<script>
  var table;
  var editor;
  var op = "Jobs.jobList";
  $(document).ready(function() {
    table = $("#page_listww").DataTable({
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
    
    function deleteJob(id)
    {
        let cnf = confirm("Are you sure you want to delete this job posting?");
        if(cnf == true)
        {
            $.blockUI();
            $.post("utilities.php",{op:"Jobs.deleteJob",id:id},function(re){
                $.unblockUI();
                alert(re.responseMessage);
                getpage('job_posting_list.php',"page");
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
    function setFeatureCat(stat,cat_id)
    {
        let cnf = confirm("Are you sure you want to update?");
        if(cnf == true)
            {
                $.blockUI();
                $.post('utilities.php',{op:"Category.setFeatureStatus",status:stat,id:cat_id},function(re){
                    $.unblockUI();
                    console.log(data);
                    if (re.responseCode == "0") 
                    {
                        swal({
                            'text':"Update complete!",
                            'icon':"success"
                        })
                        getpage('category_list.php',"page");
                    }
                },'json');
            }
    }
</script>