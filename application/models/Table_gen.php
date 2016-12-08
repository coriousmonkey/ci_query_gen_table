<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Table_gen extends CI_Model {

	
    public $order="asc";
    public $tablename="";
    public $id="";//insert column wich primary key
    public $filter="";
    public $column="*";
    public $controller_url="";//insert your controller url
    public $tables_atrrib='class="table table-bordered"';
    public $edit_url="";
    public $delete_url="";
    public $query="";
    public $total_rows=0;
    
	function __construct()
	{
		parent::__construct();
	}
    
    public function with_paging(){
        $start = intval($this->input->get('start'));
        
        $this->load->library('pagination');
        
        $config['query_string_segment'] = 'start';

        $config['full_tag_open'] = '<nav><ul class="pagination" style="margin-top:0px">';
        $config['full_tag_close'] = '</ul></nav>';
        
        $config['first_link'] = false;
        $config['first_tag_open'] = '<li class="hidden">';
        $config['first_tag_close'] = '</li>';
        
        $config['last_link'] = false;
        $config['last_tag_open'] = '<li class="hidden" >';
        $config['last_tag_close'] = '</li>';
        
        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        
        $config['prev_link'] = 'Prev';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';
        
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $config['base_url'] = $this->controller_url;
        $config['first_url'] = $this->controller_url;
        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->count_rows();
        
        $this->pagination->initialize($config);
        return '<div style="overflow-x:scroll;">'.
                $this->get_limit_data( $config['per_page'], $start ).
                '</div>
                <div class="row">
                    <div class="col-md-6">
                        <a class="btn btn-primary" > Total Record '.$config['total_rows'].'</a>
                    </div>
                    <div class="col-md-6 text-right">'.$this->pagination->create_links().'</div>
                </div>
                ';
    }
    
    public function with_paging_query(){
        $start = intval($this->input->get('start'));
        
        $this->load->library('pagination');
        
        $config['query_string_segment'] = 'start';

        $config['full_tag_open'] = '<nav><ul class="pagination" style="margin-top:0px">';
        $config['full_tag_close'] = '</ul></nav>';
        
        $config['first_link'] = false;
        //$config['first_tag_open'] = '<li>';
        //$config['first_tag_close'] = '</li>';
        
        $config['last_link'] = false;
        //$config['last_tag_open'] = '<li>';
        //$config['last_tag_close'] = '</li>';
        
        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        
        $config['prev_link'] = 'Prev';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';
        
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $config['base_url'] = $this->controller_url;
        $config['first_url'] = $this->controller_url;
        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->count_rows_query();
        
        $this->pagination->initialize($config);
        return '<div style="overflow-x:scroll;">'.
                $this->get_limit_data_query( $config['per_page'], $start ).
                '</div>
                <div class="row">
                    <div class="col-md-6">
                        <a class="btn btn-primary" > Total Record '.$config['total_rows'].'</a>
                    </div>
                    <div class="col-md-6 text-right">'.$this->pagination->create_links().'</div>
                </div>
                ';
    }
    
    public function get_limit_data_query($limit, $start = 0 ) {
        $this->db->limit($limit, $start);
        $data=$this->db->query( $this->query." LIMIT ".$start.",".$limit )->result_array();
        $fields=array();
        foreach($data[0] as $key=>$row){
            array_push($fields,$key);
        }
        return $this->table_generate( $fields, $data );   
    }
    public function count_rows_query() {
        if($this->total_rows==0){
            return $this->db->count_all( "(".$this->query.") as tb");    
        }
        else{
            return $this->total_rows;
        }
    }
    

    public function get_limit_data($limit, $start = 0 ) {
        if($this->filter<>""){
            $this->db->where($this->filter);
        }
        
        $this->db->order_by($this->id, $this->order);
        $this->db->limit($limit, $start);
        $data=$this->db->select($this->column)->from($this->tablename)->get();
        
        return $this->table_generate( $data->list_fields(), $data->result_array() );   
    }
    
    public function count_rows() {
        if($this->filter<>""){
            $this->db->where($this->filter);
        }
        return $this->db->count_all($this->tablename);
    }
    
    public function table_generate($columns,$data){
        $string_tb='<table '.$this->tables_atrrib.' >';
        $string_tb.='<thead><tr>';
        foreach($columns as $th){
                $string_tb.='<th>'.$th.'</th>';   
        }
        if($this->edit_url=="" and $this->delete_url==""){
            $string_tb.=''; 
        }else{
            $string_tb.='<th style="">action</th>';
        }
        
        $string_tb.='</tr></thead>';
        $string_tb.='<tbody>';
            foreach( $data as $row ){
                $string_tb.='<tr>';
                    foreach($row as $key=>$td){
                        $string_tb.='<td>'.$td.'</td>';  
                    }
                if($this->edit_url=="" and $this->delete_url==""){
                    $string_tb.=''; 
                }else{
                    $string_tb.='<th><div class="btn-group btn-group-sm" style="width:100px;">';
                    if($this->edit_url<>""){
                        $string_tb.='<a class="btn btn-default" href="'.$this->edit_url.'?'.$this->id.'='.$row[$this->id].'" >Edit</a>';   
                    }
                    if($this->delete_url<>""){
                        $string_tb.='<a  class="btn btn-default"  href="'.$this->delete_url.'?'.$this->id.'='.$row[$this->id].'" >Delete</a>';   
                    }
                    $string_tb.='</div></th>';
                }
                
                $string_tb.='</tr>';
            }  
        $string_tb.='</tbody>';
        $string_tb.='</table>';
        return $string_tb;
    }
}