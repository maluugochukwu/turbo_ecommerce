<?php
    include_once("../libs/dbfunctions.php");
 
    $dbobject     = new dbobject();
    $id           = $_REQUEST['id'];
    $customer_id  = $_REQUEST['customer_id'];
    $sql          = "SELECT * FROM transaction_table INNER JOIN customers ON source_acct = email WHERE transaction_id = '$id'  ";
    $result       = $dbobject->db_query($sql);
//var_dump($result);

//    $sql          = "SELECT * FROM transaction_table WHERE transaction_id = '$id' ";
//    $result       = $dbobject->db_query($sql);
?>
<style>
    b{
        color:#000
    }
</style>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold" align="center"><?php echo $dbobject->getitemlabel('dress','id',$result[0]['dress_id'],'name') ?> Order</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 " style="background:#f5f9fc">
    <div class="tab">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" href="#tab-1" data-toggle="tab" role="tab"><i class="fa fa-shopping-cart"></i> Booking Details</a></li>
            <li class="nav-item"><a class="nav-link" href="#tab-2" data-toggle="tab" role="tab"><i class="fa fa-user"></i> Customer Details</a></li>

        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab-1" role="tabpanel">
               <div class="table-responsive">
                   <table id="order_summarys" class="table table-striped " >
                        <thead>
                            <tr role="row">
                                <th>S/N</th>
                                <th>Order ID</th>
                                <th>Total Price</th>
                                <th>Dress Price</th>
                                <th>Caution Fee</th>
                                <th>Pickup Date</th>
                                <th>Return Date</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                                <tr role="row" class="odd">
                                    <th><?php echo 1; ?></th>
                                    <th><?php echo $result[0]['transaction_id']; ?></th>
                                    <th><?php echo number_format($result[0]['transaction_amount']); ?></th>
                                    <th><?php echo number_format($result[0]['dress_amount']); ?></th>
                                    <th><?php echo number_format($result[0]['caution_fee']); ?></th>
                                    <th><?php echo date("F jS, Y",strtotime($result[0]['pickup_date'])); ?></th>
                                    <th><?php echo date("F jS, Y",strtotime($result[0]['return_date'])); ?></th>
                                </tr>
                        </tbody>
                    </table>
               </div>
            </div>
            <div class="tab-pane" id="tab-2" role="tabpanel">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card mb-3">
                            
                            <div class="card-body table-responsive">
                                <table id="customer_tbl" class="table table-stripe">
                                    <thead>
                                        <tr role="row">
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Phone Number</th>
                                            <th>Address</th>
                                            <th>Bank Name</th>
                                            <th>Account Number</th>
                                            <th>Account Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            
                                                    <tr role="row" class="odd">
                                                        <th><?php echo $result[0]['first_name']." ".$result[0]['last_name']; ?></th>
                                                        <th><?php echo $result[0]['email']; ?></th>
                                                        <th><?php echo $result[0]['phone']; ?></th>
                                                        <th><?php echo $result[0]['address']; ?></th>
                                                        <th><?php echo $result[0]['bank_name']; ?></th>
                                                        <th><?php echo $result[0]['account_no']; ?></th>
                                                        <th><?php echo $result[0]['account_name']; ?></th>
                                                    </tr>
                                            
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
      
    
</script>