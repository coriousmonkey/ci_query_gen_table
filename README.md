# ci_query_gen_table

Generate table from a complex query with simple config
        

        //LOAD THIS MODEL
        $this->load->model('Table_gen');

        //YOUR CONTROLLER
        $this->Table_gen->id='id'; //fieldname as primary key (important)
        $this->Table_gen->edit_url=site_url().'/Absensi/edit/'; //url controller for edit act (optional)
        $this->Table_gen->delete_url=site_url().'/Absensi/delete/'; //url controller for delete act (optional)
        $this->Table_gen->controller_url=site_url().'/Absensi/'; // url controller (!important)
        
        $this->Table_gen->query = "Your query";  -- no need limit string
        
        $myhtmltable= $this->Table_gen->with_paging_query();
        
        $this->load->view('coba',array( 'myhtmltable'=>$myhtmltable  ) );
        
        //YOUR VIEW
        //require bootstrap
         echo $myhtmltable;
