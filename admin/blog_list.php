<?php
include_once("libs/dbfunctions.php");
$dbobject = new dbobject();
?>
   <div class="card">
    <div class="card-header">
        <h5 class="card-title">Blog List</h5>
        <h6 class="card-subtitle text-muted">The report contains Blogs that have been setup in the system.</h6>
    </div>
    <div class="card-body">
      <a class="btn btn-info" onclick="getModal('setup/blog_setup.php','modal_div')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Create Blog</a>
        <div id="datatables-basic_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-3">
                    <label for=""></label>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 table-responsive">
                    <table id="page_list" class="table table-striped " >
                        <thead>
                            <tr role="row">
                                <th>S/N</th>
                                <th>Title</th>
                                <th>Image</th>
                                <th>Category</th>
                                <th>Author</th>
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
<!-- <script src="https://cdn.ckeditor.com/ckeditor5/29.2.0/classic/ckeditor.js"></script> -->
<script src="js/build/ckeditor.js"></script>
<script>
  var table;
  var editor;
  var op = "Blog.blogList";
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
    function disableBlog(blog_id,current_state)
    {
        let cnf = confirm("Are you sure you want to take this action?");
        if(cnf == true)
            {
                $.blockUI();
                $.post("utilities.php",{op:"Blog.disableBlog",id:blog_id,state:current_state},function(re){
                    $.unblockUI();
                    alert(re.response_message);
                    // getpage('blog_list.php',"page");
                    do_filter();
                },'json')
            }
    }
    function setAsFeature(current_state,blog_id)
    {
        let cnf = confirm("Are you sure you want to take this action?");
        if(cnf == true)
            {
                $.blockUI();
                $.post("utilities.php",{op:"Blog.setAsFeature",id:blog_id,state:current_state},function(re){
                    $.unblockUI();
                    alert(re.response_message);
                    // getpage('blog_list.php',"page");
                    do_filter();
                },'json')
            }
    }
    function deleteBlog(blog_id)
{
    let cnf = confirm("Are you sure you want to delete blog?");
        if(cnf == true)
            {
                $.blockUI();
                $.post("utilities.php",{op:"Blog.deleteBlog",id:blog_id},function(re){
                    $.unblockUI();
                    alert(re.response_message);
                    do_filter();
                },'json')
            }
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