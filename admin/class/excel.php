<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Excel extends dbobject
{
  public function createExcel()
  {
        $sql = $_SESSION['sql_without_limit'];
        $table_head = array(
            'Booking ID',
            'Total Amount',
            'Payer Email',
            'Dress Name',
            'Dress Price',
            'Caution Fee',
            'Extra Item Price',
            'Branch',
            'Items',
            'Extra Items',
            'Pickup Date',
            'Return Date',
            'Wedding Date',
            'Posted By',
            'Mode of Payment',
            'Created On'
        );
        $excel_data = [$table_head];

        
        $result    = $this->db_query($sql);
        foreach($result as $row)
        {
            $dress_name = $this->getitemlabel('dress','id',$row['dress_id'],'name');
            $branch_name = $this->getitemlabel('branch','id',$row['branch_id'],'name');
            $excel_data[] = [
                $row['transaction_id']." ",
                $row['transaction_amount'],
                $row['source_acct'],
                $dress_name,
                $row['dress_amount'],
                $row['caution_fee'],
                $row['extra_item_price'],
                $branch_name,
                $row['items'],
                $row['extra_item'],
                date("F jS, Y",strtotime($row['pickup_date'])),
                date("F jS, Y",strtotime($row['return_date'])),
                date("F jS, Y",strtotime($row['wedding_date'])),
                $row['posted_by'],
                $row['payment_mode'],
                date("F jS, Y",strtotime($row['created']))
            ];
        }
        $spreadsheet = new Spreadsheet();
        
        $spreadsheet->getActiveSheet()
                    ->fromArray(
                        $excel_data, // The data to set
                        NULL, // Array values with this value will not be set
                        'A1' // Top left coordinate of the worksheet range where
                        // we want to set these values (default is A1)
        );
      
                     
      
      
      
        $fileName = "transaction_report.xlsx";
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
    }
    
}