<?php
include_once("../libs/dbfunctions.php");
include_once("../class/transaction.php");
$dbobject = new dbobject();
$transaction = new Transaction();
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
$booking_amount = $transaction->getBookingAmount($_REQUEST[id]);
$prev_amt       = $transaction->getSumInstallmentAmount($_REQUEST[id]);
$sql = "SELECT name FROM dress WHERE id = '$_REQUEST[dress_id]' LIMIT 1";
    $dress = $dbobject->db_query($sql);
$dress_name = $dress[0]['name'];
$balance  = $booking_amount - $prev_amt;
?>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold">Make Payment</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
   
    <form action="" id="settings_forms" onsubmit="return false">
        <input type="hidden" name="op" value="Transaction.completePayment">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="trans_id" value="<?php echo $_REQUEST[id]; ?>">
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                   <label for="">Paying</label>
                   <input type="text" autocomplete="off" class="form-control" name="amount_paid" placeholder="Enter amount" />
               </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
               <label for="">&nbsp;</label>
               <button class="btn btn-block btn-success" onclick="saveRecord()">Submit</button>
               </div>
           </div>
       </div>
       <div class="row">
           <div class="col-sm-12">
               <div class="server_message" style="color:red"></div>
           </div>
       </div>
       <div class="row">
           <div class="col-sm-4">
               <div class="form-group">
                   <label for="">Owing</label>
                   <input type="text" class="form-control" readonly value="<?php echo number_format($balance,2); ?>" />
               </div>
           </div>
           <div class="col-sm-4">
               <div class="form-group">
                   <label for="">Total Amount Paid</label>
                   <input type="text" class="form-control" readonly value="<?php echo number_format($prev_amt,2); ?>" />
               </div>
           </div>
           <div class="col-sm-4">
               <div class="form-group">
                   <label for="">Dress</label>
                   <input type="text" class="form-control" readonly value="<?php echo $dress_name; ?>" />
               </div>
           </div>
       </div>
       <div class="row">
           
           <div class="col-sm-4">
               <div class="form-group">
                   <label for="">Customer Name</label>
                   <input type="text" class="form-control" readonly value="<?php echo $row['balance']; ?>" />
               </div>
           </div>
           <div class="col-sm-4">
               <div class="form-group">
                   <label for="">Booking ID</label>
                   <input type="text" class="form-control" readonly value="<?php echo $_REQUEST[id]; ?>" />
               </div>
           </div>
       </div> 
       <div class="row">
           
           
       </div>
       <div class="row">
                <div class="col-sm-12 table-responsive">
                   <h3>Payment History</h3>
                    <table id="teds" class="table table-striped " >
                        <thead>
                            <tr role="row">
                                <th>S/N</th>
                                <th>Amount Paid</th>
                                
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM installment_payment WHERE booking_id = '$_REQUEST[id]' ";
                            $result = $dbobject->db_query($sql);
                            $counter = 1;
                            foreach($result as $row)
                            {
                                echo '<tr><td>'.$counter.'</td><td>NGN '.number_format($row['amount_paid'],2).'</td><td>'.$row['created'].'</td></tr>';
                                $counter++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>      
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
                        swal({
                            text:rr.response_message,
                            icon:"success"
                        }).then((rs)=>{
                            getpage('transaction_list.php','page');
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
            }s
</style>