<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();

$sql = "SELECT * FROM product_page";
$page = $dbobject->db_query($sql);



if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
{
    $operation = 'edit';
    $product_id = $_REQUEST['id'];
    $sql_blog = "SELECT * FROM product_category WHERE id = '$product_id' LIMIT 1";
    $blog = $dbobject->db_query($sql_blog);
}else
{
    $operation = 'new';
}
$files = "";
function getFileName($str)
{
    $dd = explode("/",$str);
    $last = count($dd)-1;
    return $dd[$last];
}
// $r = false;
// $b = false;
// if(!$r && $b)
// {

// }
?>
<style>
    .ck-editor__editable_inline {
    min-height: 200px;
}
</style>
 <link rel="stylesheet" href="codebase/dhtmlxcalendar.css" />
 <link rel="stylesheet" href="codebase/dhtmlxcalendar.css" />
<script src="codebase/dhtmlxcalendar.js"></script>

 <!-- <link rel="stylesheet" href="codebase/dhtmlxcalendar.css" /> -->
 <script src="js/ckeditor.js"></script>
 <!-- <script src="https://cdn.ckeditor.com/ckeditor5/29.1.0/classic/ckeditor.js"></script> -->
 <script src="https://cdn.ckeditor.com/ckeditor5/29.2.0/classic/ckeditor.js"></script>
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
    <h4 class="modal-title" style="font-weight:bold">Product Category Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="Product.saveProductCategory">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="id" value="<?php echo $product_id; ?>">
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" value="<?php echo $blog[0]['name'] ?>" name="name" >
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Icon</label>
                    <select name="icon" onchange="display_icon(this.value)" id="icon" class="form-control">
                        <option value="">:: SELECT ICON ::</option>
                        <?php
                            foreach (glob("../img/product_category/*") as $filename)
                            {
                                $selected = (getFileName($filename) == $blog[0]['icon'])?"selected":"";
                                echo "<option $selected value='".getFileName($filename)."'>".getFileName($filename)."</option>";
                            }
                        ?>
                    </select>
                </div>
           </div>
           
       </div>
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                   <label for="">Background Color</label>
                   <input type="color" class="form-control" value="<?php echo $blog[0]['bg_color'] ?>"name="bg_color" >
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                   <label for="">Icon Background Color</label>
                   <input type="color" name="icon_bg_color" value="<?php echo $blog[0]['icon_bg_color'] ?>" class="form-control">
                </div>
           </div>
       </div>
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" class="form-control" value="<?php echo $blog[0]['description'] ?>" name="description" >
                </div>
           </div>
       </div>
       <div class="row mb-5">
           <div class="col-sm-12" >
               <img src="img/product_category/<?php echo $blog[0]['icon'] ?>" id="show_icon" style="<?php echo ($operation =="edit")?"display:block":"display:none" ?>" alt="">
           </div>
       </div>
       
       
       
       
        
        
        
       
       
        
       
       <div class="server_message"></div>
       <button id="save_facility" onclick="saveRecord()" class="btn btn-primary mb-1">Submit</button>
       
    </form>
</div>
<script src="js/jquery.uploadfile.min.js"></script>
        <link rel="stylesheet" href="css/uploadfile.css">
        <link rel="stylesheet" href="css/bootstrap-tagsinput.css" />
        <script src="js/bootstrap-tagsinput.js"></script>
<script>
    function display_icon(val)
    {
        $("#show_icon").show();
        $("#show_icon").attr("src","img/product_category/"+val);
    }
    String.prototype.replaceAt = function(index, replacement) {
    return this.substr(0, index) + replacement + this.substr(index + replacement.length);
}
    var content,introduction;
        

                var ck_story;
    $("document").ready(function(){
        ClassicEditor
        .create( document.querySelector( '#introduction' ) )
        .then((editor)=>{
            ck_story = editor
                    ck_story.setData("<?php echo $blog[0]['body']; ?>");
        })
        .catch( error => {
            console.error( error );
        } );
    })            
                            
                </script>
<script>
    function showsubcat(val)
    {
        if(val != "")
        {
            $.post("utilities.php",{op:"Template.getTemplateSubcategory",cat_id:val},function(record){
                    $("#subcategory").html(record);
            });
        }
    }
    
    function saveRecord()
    {
        // var ccd = ck_story.getData();
        // ccd = ccd.replace(/"/g,'" ');
        // ccd = ccd.replace(/&nbsp;/g,"");
        // console.log(ccd,"logger")
        $("#defaultModalPrimary").block();
        var ddd = $("#form1").serialize();
        // ddd = ddd + "&body="+ccd
        $.post('utilities.php',ddd,function(rr){
            $("#defaultModalPrimary").unblock();
             if(rr.response_code == 0)
                {
                    swal({
                        text:rr.response_message,
                        icon:"success"
                    }).then((rs)=>{
                        do_filter();
                        $("#defaultModalPrimary").modal('hide');
                    })
                    
                }else{
                    $(".server_message").text(rr.response_message);
                }
        },'json')
    }
    
    
   
</script>
<style>
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