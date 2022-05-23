<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();
$sql2 = "SELECT * FROM branch";
$branches = $dbobject->db_query($sql2);

//$sql3 = "SELECT * FROM package";
//$package = $dbobject->db_query($sql3);

if(isset($_GET['op']))
{
    $sql = "SELECT * FROM package WHERE id = '$_REQUEST[id]'";
    $package = $dbobject->db_query($sql);
    $operation = "edit";
}
else
{
    $operation ="new";
}
?>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold">Add a Package</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
   
    <form action="" id="settings_forms" onsubmit="return false">
        <input type="hidden" name="op" value="Package.savePackage">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="id" value="<?php echo $_REQUEST[id]; ?>">
       <ul><li>Items are grouped to a package name</li> </ul>
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label for="">Package Name</label>
                    <input type="text" autocomplete="off" value="<?php echo $package[0]['name']; ?>"  class="form-control" name="name" />
                </div>
           </div>
           <div class="col-sm-6">
                <div class="form-group">
                     <label for="">Items </label>
                    <input type="text" id="optval" name="items" value="" data-role="tagsinput"   />
                    <small>enter many items seperated by a comma</small>
                </div>
           </div>
       </div>
       
    
        <div class="server_message" style="font-weight:bold; color:red"></div>
      <a class="btn btn-success" onclick="saveRecord()"  >SAVE</a>
    </form>
</div>

<link rel="stylesheet" href="css/bootstrap-tagsinput.css" />
<script src="js/bootstrap-tagsinput.js"></script>
<script src="js/jquery.uploadfile.min.js"></script>
<link rel="stylesheet" href="css/uploadfile.css">
<script>
    
    var items = "<?php echo implode(',',json_decode($package[0]['items'],TRUE)); ?>";
    console.log(items);
    $('#optval').tagsinput('removeAll');
    $('#optval').tagsinput('add', items);
    function displayVal(v)
    {
        $("#showval").text(v);
    }
    
   
    
    function saveRecord()
    {
        var wer = $("#settings_forms").serialize();
        console.log(wer)
        
            $("#defaultModalPrimary").block();
            var wer = $("#settings_forms").serialize();
            console.log(wer)
    //        return true;
            $.post('utilities.php',wer,function(rr){
                 $("#defaultModalPrimary").unblock();
                 if(rr.response_code == 0)
                    {
                        menuStates.package.isSaved = 1;
                        swal({
                            text:rr.response_message,
                            icon:"success"
                        }).then((rs)=>{
                            getpage('package_list.php','page');
                            $("#defaultModalPrimary").modal('hide');
                        })

                    }else{
                        $(".server_message").text(rr.response_message);
                    }
            },'json')
   
    }
</script>
<style>
    .ajax-upload-dragdrop, .ajax-file-upload-filename, .ajax-file-upload-statusbar{
                width: auto !important;
            }
    .label-info {
                background-color: #5bc0de;
            }
            .label {
                display: inline;
                padding: .2em .6em .3em;
                font-size: 75%;
                font-weight: 700;
                line-height: 1;
                color: #fff;
                text-align: center;
                white-space: nowrap;
                vertical-align: baseline;
                border-radius: .25em;
            }
            .bootstrap-tagsinput{
              width:100%;  
            }
            .bootstrap-tagsinput input {
                width:inherit;  
            }
</style>