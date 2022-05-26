<?php
class Pagination extends dbobject
{
    public $recordsPerRequest = 10;
    public $totalCount        = 0;
    public $pageNo            = 1;
    public $dataType          = "";
    public function __construct($data_type = "sql")
    {
        $this->dataType = $data_type;
    }
    public function prepareData($sql)
    {
        if($this->dataType     == "sql")
        {
            $this->pageNo       = (isset($_GET['pageNo']))?$_GET['pageNo']:$this->pageNo;
            $startLimit         = ($this->pageNo - 1) * $this->recordsPerRequest;
            $this->totalCount   = $this->db_query($sql,false);
            $sql                = $sql." LIMIT $startLimit,$this->recordsPerRequest";
            $data               = $this->db_query($sql);
            return $data;
        }
        elseif($this->dataType == "json")
        {
            $data               = json_decode($sql,TRUE);
            $this->totalCount   = count($data);
            
            $this->pageNo       = (isset($_GET['pageNo']))?($_GET['pageNo'] - 1):$this->pageNo - 1;
            $startLimit         = ($this->pageNo - 1) * $this->recordsPerRequest;
            $data               = array_slice( $data, $startLimit, $this->recordsPerRequest );
            return $data;
        } 
    }
    public function paginate($records_per_view)
    {
        $this->recordsPerRequest = (trim($records_per_view) == "")?$this->recordsPerRequest:$records_per_view;
        return $this;
    }
    public function links($page)
    {
        $loop             = ceil($this->totalCount / $this->recordsPerRequest);
        $previous_val     = (isset($_GET['pageNo']))?$_GET['pageNo'] - 1:"2";
        $display_previous = ($_GET['pageNo'] == "1" || !isset($_GET['pageNo']))?' cursor:not-allowed;':'display:inline-block';
        $previous_link    = ($_GET['pageNo'] == "1" || !isset($_GET['pageNo']))?'javascript:void(0)':$_SERVER['PHP_SELF']."?pageNo=".$previous_val;
        $links            = "<div class='blog-pagination'><ul class='pagination'><li><a style='$display_previous' href='".$previous_link."'>Previous</a></li>";
        // if($loop > 7)
        // {
            
        // }
        // else
        // {

        // }
        for($x = 1; $x<=$loop; $x++)
        {
            $active = (isset($_GET['pageNo']))?($_GET['pageNo'] == $x)?"class='active'":"" : ($x == "1")?"class='active'":"";
            $links = $links."<li $active><a  href='".$_SERVER['PHP_SELF']."".$this->getQueryString($x)."' >$x</a></li>";
        }
        $next_val = (isset($_GET['pageNo']))?$_GET['pageNo'] + 1:"2";
        $display_next = ($loop == $_GET['pageNo'])?'cursor:not-allowed; ':'display:inline-block';
        $next_link = ($loop == $_GET['pageNo'])?'javascript:void(0) ':$_SERVER['PHP_SELF']."?pageNo=".$next_val;
        return $links."<li ><a style='$display_next' href='$next_link'>Next</a></li></ul></div>";
    }
    public function getQueryString($x)
    {
        return ($_SERVER['QUERY_STRING'] != "")?"?pageNo=$x&".$_SERVER['QUERY_STRING']:"?pageNo=$x";
    }
}