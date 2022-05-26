<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();
$product_id = $_REQUEST['product_id'];
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
    <h4 class="modal-title" style="font-weight:bold">Product Features Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form onsubmit="return false" id="form1">
        <input type="hidden" name="op" value="Product.saveProductFeature" />
        <input type="hidden" name="product_id" value="<?php echo $product_id ?>">
        <div class="form-group">
            <label for="tag_name">Name</label>
            <input type="text" class="form-control" name="name" >
        </div>
        <div class="form-group">
            <label for="tag_name">Slogan</label>
            <input type="text" class="form-control" name="slogan" >
        </div>
        <div class="form-group">
            <label for="tag_name">Description</label>
            <div id="introduction"></div>
        </div>
        <div class="form-group">
            <label for="tag_name">Feature Image</label>
            <div id="extraupload"></div>
        </div>

        <div class="server_message"></div>
        <button id="save_facility" onclick="saveRecord()" class="btn btn-primary mb-1">Submit</button>
    </form>
    <h3 align="center">Feature List</h3>
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
                               <th>Name</th>
                               <th>Description</th>
                                <th>Action</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody id="tag_list">
                            <?php
                                $sql = "SELECT * FROM product_feature WHERE product_id = '$product_id'";
                                $result = $dbobject->db_query($sql);
                                $counter = 0;
                                foreach($result as $row)
                                {
                                    $id = $row['id'];
                                    $p_id = $row['product_id'];
                                    $counter++;
                            ?>
                                    <tr>
                                        <td><?php echo $counter; ?></td>
                                        <td><?php echo $row['name'] ?></td>
                                        <td><?php echo $row['description'] ?></td>
                                        <td><img src="<?php echo $row['image']; ?>" width="50" height="50" alt=""></td>
                                        <td><?php echo date("jS M h:i:s",strtotime($row['created'])); ?></td>
                                        <td><button onclick='deleteFeature("<?php echo $id; ?>","<?php echo $p_id; ?>")' class="btn btn-danger">Delete</button></td>
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
<script src="js/jquery.uploadfile.min.js"></script>
        <link rel="stylesheet" href="css/uploadfile.css">
<script>
    
</script>
<script>
    var content,introduction,instnc;
    // for(name in ClassicEditor.instances)
    // {
    //     ClassicEditor.instances[name].destroy()
    // }   

                var description;
    $("document").ready(function(){
        instnc = ClassicEditor
        .create( document.querySelector( '#introduction' ) )
        .then((editor)=>{
            description = editor
            description.setData("<?php echo $contact[0]['value']; ?>");
        })
        .catch( error => {
            console.error( error );
        } );
    })
    function deleteFeature(f_id,p_id)
    {
        var conf = confirm("Are you sure you want to delete this feature?")
        if(conf)
        {
            $("#defaultModalPrimary").block();
            $.post('utilities.php',{op:"Product.deleteFeature",feature_id:f_id,product_id:p_id},function(rr){
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
    var coverImg = $("#extraupload").uploadFile({
                    url:"utilities.php",
                    fileName:"upfile",
                    showPreview:true,
                    previewHeight: "100px",
                    previewWidth: "100px",
                    maxFileCount:1,
                    multiple:false,
                    allowedTypes:"jpg,png,jpeg",
                    maxFileSize:1000000,
                    autoSubmit:false,
                    returnType:"json",
                    onSubmit:function(files)
                    {
                        $.blockUI({message:"Saving Feature information. Kindly wait.."});
                    },
                    dynamicFormData: function()
                    {
                        $("#save_facility").text("Loading......");
                        // var ccd = description.getData();
                        // ccd = ccd.replace(/nbsp;/g," ");
                        var ccd = description.getData();
                        
                        // ccd = ccd.replace(/"/g,'" ');
                        ccd = ccd.replace(/&nbsp;/g,"");
                        // ccd = ccd.replace(/'/g,"");
                        // ccd = ccd.replace(/#x25;/g,"");
                        // ccd = ccd.replace(/&#x25;/g,"");
                        // ccd = decodeURIComponent( unescape( unescape(ccd)))
                        var dd = $("#form1").serialize();
                        dd = dd + "&description="+ccd
                        dd = encodeURI(dd);
                        console.log(dd);
                        return dd;
                    },
                    onSuccess:function(files,data,xhr,pd)
                    {
                        $.unblockUI();
                        console.log(data);
                         $("#save_facility").text("Save");
                               
                        if(data.response_code == 0)
                        {
                            $('.server_message').css('color','green');
                            $('.server_message').html(data.response_message);
                                swal({
                                    text:data.response_message,
                                    icon:"success"
                                }).then((rs)=>{
                                    $("#tag_list").html(data.data)
                                })
                        }else
                        {
                            
                            $('.server_message').css('color','red');
                            $('.server_message').html(data.response_message);
                            coverImg.reset();
                            $('.ajax-file-upload-red').click();
                        }
//                        featureImg.startUpload();
                    }
                });
    
    function saveRecord()
    {
        if(coverImg.selectedFiles == 0)
            {
                alert("kindly select an image file for product feature.")
            }
        else{
                coverImg.startUpload();
            }
    }       
    
</script>
