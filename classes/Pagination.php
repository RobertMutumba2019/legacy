<?php
Class Pagination{
    public $numPerPage = 20;
    public $totalRecords = 0;
    public $pages = 0;
    public $url = "";
    public $active = "";
    public $searchWord = "";
    public $check = false;
    public $searchColumns = 0;

    public function setUp($check="", $searchColumns="", $searchWord="", $url="", $query="", $numPerPage=0, $start=0){
        $this->numPerPage = $numPerPage;
        $this->url = $url;
        $this->active = $start;
        $this->check = $searchWord;
        if (!empty($searchWord)) {
            $this->check = TRUE;
        }
        if ($check) {
            $this->searchWord = $searchWord;
        }

        $this->searchColumns = $searchColumns;
        foreach($searchColumns as $fts=>$v){
            $s[] = "$fts LIKE '%$searchWord%'";
            foreach($searchwords as $sw){
                $s[] = "$fts LIKE '%$sw%'";
            }
        }
    
        $db = new Db();

        $sql = "SELECT ".implode(',',@$query["select"]);
        $sql .= " FROM ".implode(',',@$query["tables"]);

        if ($check||!empty($query["whereAnd"])||!empty($query["whereOr"])) {
            $sql .= " WHERE ";
        }

        $where = array();

        if (!empty($query["whereAnd"])) {
            $where[] = "(".implode(' AND ', $query["whereAnd"]).")";
        }

        if (!empty($query["whereOr"])) {
            $where[] = "(".implode(' OR ', $query["whereOr"]).")";
        }

        if ($check) {
            $where[] = "(".implode(' OR ', $s).")";
        }

        $sql .= implode(" AND ", $where);

        if(!empty($query["order"])){
            $sql .= " ORDER BY ";
            $order = array();
            foreach($query["order"] as $column=>$value){
                $order[] = $column." ".$value;
            }
            $sql .= implode(',',$order);
        }

        //echo $sql;

        $select = $db->select($sql);
        echo $db->error();

        $this->totalRecords = $db->num_rows();
        $this->pages = ceil($db->num_rows()/$this->numPerPage);

        if (empty($start)) {
            $start = 1;
        }
        $start -= 1;
        $start *= $this->numPerPage;

        $sql = $sql.' OFFSET '.$start.' ROWS FETCH NEXT '.$this->numPerPage.' ROWS ONLY';
        $select = $db->select($sql);
        echo $db->error();
        return $select;
    }

    public function search(){
        if(isset($_POST['pSearch'])){
            $q = $_POST['q'];
            Feedback::redirect($this->url.'1/'.$this->numPerPage.'/'.$q.'/search');
        }elseif(isset($_POST['pReset'])){
            Feedback::redirect($this->url.'');
        }
        echo '<form action="" method="post" style="margin-bottom:10px;">';
        if ($this->check) {
            echo '<input style="width:300px;" type="text" placeholder="Search '.implode(', ', array_values($this->searchColumns)).'" autocomplete="off" name="q" value="'.$this->searchWord.'"/>';
        } else {
            echo '<input style="width:300px;" type="text" placeholder="Search '.implode(', ', array_values($this->searchColumns)).'" autocomplete="off" name="q" value=""/>';
        }

        echo '&nbsp; <input type="submit" name="pSearch" value="Search"/>';
        echo '&nbsp; <input type="submit" name="pReset" value="Reset"/>';
        echo '</form>';
    }

    public function links($color='blue', $active_color='black'){
        $active = $this->active;
        $url = $this->url;
        echo "<style>.pagination-new{margin:0; margin-top:10px; padding-left:0;} .pagination-new li{margin:1px; margin-left:0;padding:1px;}.page-active{color:white; background-color:$color; }.page-color{color:white; background-color:$active_color;} .page-color, .page-active{padding:2px 10px; border-radius:5px;margin-left:0; margin-right:2px;}.page-color li a{text-decoration:none;}</style>";

        $v = "";
        $v .= 'Total Records: <b>'.number_format($this->totalRecords).'</b>';
        $v .= '<ul class="pagination-new">';

        if (empty($active)) {
            $active = 1;
        }

        if ($active > 1) {
            $v .= '<li style="display:inline"><a href="'.$url.''.($active-1).'/'.($this->numPerPage).'/'.$this->searchWord.'" class="page-color">Previous</a></li>';
        } else {
            $v .= '<li style="display:inline"><a href="#" class="page-color" style="opacity:0.5">Previous</a></li>';
        }

        if ($active > 1) {
            $v .= '<li style="display:inline"><a href="'.$url.''.(1).'/'.($this->numPerPage).'/'.$this->searchWord.'" class="page-color">First</a></li>';
        } else {
            $v .= '<li style="display:inline"><a href="#" class="page-color" style="opacity:0.5">First</a></li>';
        }

        if(empty($active)){
            $first = 1; 
            $last = 10;
        }else{
            $pages = $this->pages;
            $first = $active-5;
            $last = $active+5;
            if($last > $pages){
                $last = $pages;
                $first += ($last-$pages);
                if ($first < 1) {
                    $first = 1;
                }
            }

            if($first<1){
                $last += abs($first);
                $first = 1;

                if($last > $pages){
                    $last = $pages;
                }
            }

             
            //$first = 1; 
            //$last = 10;
        }

        for($i=$first; $i<=$last; $i++){
            if ($i == $active) {
                $v .= '<li style="display:inline"><a href="'.$url.''.$i.'/'.$this->numPerPage.'/'.$this->searchWord.'" class="page-active">'.$i.'</a></li>';
            } else {
                $v .= '<li style="display:inline"><a href="'.$url.''.$i.'/'.$this->numPerPage.'/'.$this->searchWord.'" class="page-color">'.$i.'</a></li>';
            }
        }

        if ($active < $this->pages) {
            $v .= '<li style="display:inline"><a href="'.$url.''.($active+1).'/'.($this->numPerPage).'/'.$this->searchWord.'" class="page-color">Next</a></li>';
        } else {
            $v .= '<li style="display:inline"><a href="#" class="page-color" style="opacity:0.5">Next</a></li>';
        }

        if ($active < $this->pages) {
            $v .= '<li style="display:inline"><a href="'.$url.''.($this->pages).'/'.($this->numPerPage).'/'.$this->searchWord.'" class="page-color">Last</a></li>';
        } else {
            $v .= '<li style="display:inline"><a href="#" class="page-color" style="opacity:0.5">Last</a></li>';
        }

        $v .= '</ul>';

        if ($this->pages >= 2) {
            return $v;
        } else {
            return "";
        }
    }
}
