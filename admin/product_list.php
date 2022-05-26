<?php
include_once("libs/dbfunctions.php");
$dbobject = new dbobject();
?>
   <div class="card">
    <div class="card-header">
        <h5 class="card-title">Product List</h5>
        <h6 class="card-subtitle text-muted">The report contains product that have been setup in the system.</h6>
    </div>
    <div class="card-body">
        
        <a class="btn btn-info" onclick="getModal('setup/product_setup.php','modal_div')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Create Product</a>
        
      
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
                                <th>Name</th>
                                <th>Image</th>
                                <th>Mascot</th>
                                <th>Category</th>
                                <th>Page</th>
                                <th>Slogan</th>
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
<script src="js/build/ckeditor.js"></script>
<script>
  var table;
  var editor;
  var op = "Product.productList";
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
function deleteService(service_id)
{
    let cnf = confirm("Are you sure you want to delete sevice?");
        if(cnf == true)
            {
                $.blockUI();
                $.post("utilities.php",{op:"Services.deleteService",id:service_id},function(re){
                    $.unblockUI();
                    alert(re.response_message);
                    do_filter();
                },'json')
            }
}
  function do_filter() {
    table.draw();
  }
    
    function deleteProduct(p_id)
    {
        let cnf = confirm("Are you sure you want to delete product?");
        if(cnf == true)
        {
            $.blockUI();
            $.post("utilities.php",{op:"Product.deleteProduct",id:p_id},function(re){
                $.unblockUI();
                alert(re.response_message);
                table.draw();
            },'json')
        }
    }
    function setFeature(current_state,blog_id)
    {
        let cnf = confirm("Are you sure you want to take this action?");
        if(cnf == true)
            {
                $.blockUI();
                $.post("utilities.php",{op:"Product.setFeature",id:blog_id,state:current_state},function(re){
                    $.unblockUI();
                    alert(re.response_message);
                    // getpage('blog_list.php',"page");
                    do_filter();
                },'json')
            }
    }
    function setAdvert(current_state,blog_id)
    {
        let cnf = confirm("Are you sure you want to take this action?");
        if(cnf == true)
            {
                $.blockUI();
                $.post("utilities.php",{op:"Product.setAdvert",id:blog_id,state:current_state},function(re){
                    $.unblockUI();
                    alert(re.response_message);
                    // getpage('blog_list.php',"page");
                    do_filter();
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