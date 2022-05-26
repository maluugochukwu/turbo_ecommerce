<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();
$blog_id = $_REQUEST['blog_id'];
?>
 <link rel="stylesheet" href="codebase/dhtmlxcalendar.css" />
<script src="codebase/dhtmlxcalendar.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
<script>
    doOnLoad();
    var myCalendar;
function doOnLoad()
{
   myCalendar = new dhtmlXCalendarObject(["start_date"]);
    myCalendar.setSensitiveRange(null, "<?php echo date('Y-m-d') ?>");
   myCalendar.hideTime();
}
</script>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold">Blog Tag Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form onsubmit="return false" id="form1">
        <input type="hidden" name="op" value="Blog.saveTag">
        <input type="hidden" name="blog_id" value="<?php echo $blog_id ?>">
        <div class="form-group">
            <label for="tag_name">Tag Name</label>
            <input type="text" class="form-control" name="tag" >
        </div>
        <div class="server_message"></div>
        <button id="save_facility" onclick="saveRecord()" class="btn btn-primary mb-1">Submit</button>
    </form>
    <h3>Tag List</h3>
        <div id="datatables-basic_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-3">
                    <label for=""></label>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 table-responsive">
                    <table  class="table table-striped" >
                        <thead>
                            <tr role="row">
                                <th>S/N</th>
                               <th>Tag Name</th>
                                <th>Action</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody id="tag_list">
                            <?php
                                $sql = "SELECT * FROM blog_tag WHERE blog_id = '$blog_id'";
                                $result = $dbobject->db_query($sql);
                                $counter = 0;
                                foreach($result as $row)
                                {
                                    $tag = $row['tag'];
                                    $counter++;
                            ?>
                                    <tr>
                                        <td><?php echo $counter; ?></td>
                                        <td><?php echo $row['tag'] ?></td>
                                        <td><?php echo date("jS M h:i:s",strtotime($row['created'])); ?></td>
                                        <td><button onclick='deleteTag("<?php echo $blog_id ?>","<?php echo $tag; ?>")' class="btn btn-danger">Delete</button></td>
                                    </tr>
                            <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</div>
<script src="https://cdn.ckeditor.com/ckeditor5/29.2.0/classic/ckeditor.js"></script>
<script>
    
</script>
<script>
    var content,introduction;
        

                var ck_story;
    $("document").ready(function(){
        ClassicEditor
        .create( document.querySelector( '#introduction' ) )
        .then((editor)=>{
            ck_story = editor
                    ck_story.setData("<?php echo $contact[0]['value']; ?>");
        })
        .catch( error => {
            console.error( error );
        } );
    })
    function deleteTag(b_id,tag_name)
    {
        var conf = confirm("Are you sure you want to delete this tag?")
        if(conf)
        {
            $("#defaultModalPrimary").block();
            $.post('utilities.php',{op:"Blog.deleteTag",blog_id:b_id,tag:tag_name},function(rr){
            $("#defaultModalPrimary").unblock();
             if(rr.response_code == 0)
                {
                    swal({
                        text:rr.response_message,
                        icon:"success"
                    }).then((rs)=>{
                       $("#tag_list").html(rr.data)
                    })
                    
                }else{
                    $(".server_message").text(rr.response_message);
                }
        },'json')
        }
    }       
    function saveRecord()
    {
        $("#defaultModalPrimary").block();
        var ddd = $("#form1").serialize();
        $.post('utilities.php',ddd,function(rr){
            $("#defaultModalPrimary").unblock();
             if(rr.response_code == 0)
                {
                    swal({
                        text:rr.response_message,
                        icon:"success"
                    }).then((rs)=>{
                        $("#tag_list").html(rr.data)
                    })
                    
                }else{
                    $(".server_message").text(rr.response_message);
                }
        },'json')
    }
</script>
