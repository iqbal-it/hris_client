<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Form_spd_dalam extends MX_Controller {

    public $data;

    function __construct()
    {
        parent::__construct();
        $this->load->library('authentication', NULL, 'ion_auth');
        $this->load->library('form_validation');
        $this->load->helper('url');
        
        $this->load->database();
        $this->load->model('person/person_model','person_model');
        $this->load->model('form_spd_dalam/form_spd_dalam_model','form_spd_dalam_model');
        
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('auth');
        $this->load->helper('language');
    }

    function index($ftitle = "fn:",$sort_by = "id", $sort_order = "asc", $offset = 0)
    {
        if (!$this->ion_auth->logged_in())
        {
            //redirect them to the login page
            redirect('auth/login', 'refresh');
        }
        else
        {
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            //set sort order
            $this->data['sort_order'] = $sort_order;
            
            //set sort by
            $this->data['sort_by'] = $sort_by;
           
            //set filter by title
            $this->data['ftitle_param'] = $ftitle; 
            $exp_ftitle = explode(":",$ftitle);
            $ftitle_re = str_replace("_", " ", $exp_ftitle[1]);
            $ftitle_post = (strlen($ftitle_re) > 0) ? array('form_spd_dalam.title'=>$ftitle_re) : array() ;
            
            //set default limit in var $config['list_limit'] at application/config/ion_auth.php 
            $this->data['limit'] = $limit = (strlen($this->input->post('limit')) > 0) ? $this->input->post('limit') : 10 ;

            $this->data['offset'] = 6;

            //list of filterize all form_spd_dalam  
            $this->data['form_spd_dalam_all'] = $this->form_spd_dalam_model->like($ftitle_post)->where('users_spd_dalam.is_deleted',0)->form_spd_dalam()->result();
            
            $this->data['num_rows_all'] = $this->form_spd_dalam_model->like($ftitle_post)->where('users_spd_dalam.is_deleted',0)->form_spd_dalam()->num_rows();

            //list of filterize limit form_spd_dalam for pagination  
            if(is_admin()==true){
                $this->data['form_spd_dalam'] = $this->form_spd_dalam_model->like($ftitle_post)->where('users_spd_dalam.is_deleted',0)->limit($limit)->offset($offset)->order_by($sort_by, $sort_order)->form_spd_dalam_admin()->result();
                $this->data['_num_rows'] = $this->form_spd_dalam_model->like($ftitle_post)->where('users_spd_dalam.is_deleted',0)->limit($limit)->offset($offset)->order_by($sort_by, $sort_order)->form_spd_dalam_admin()->num_rows();
            }else{
                $this->data['form_spd_dalam'] = $this->form_spd_dalam_model->like($ftitle_post)->where('users_spd_dalam.is_deleted',0)->limit($limit)->offset($offset)->order_by($sort_by, $sort_order)->form_spd_dalam()->result();
                $this->data['_num_rows'] = $this->form_spd_dalam_model->like($ftitle_post)->where('users_spd_dalam.is_deleted',0)->limit($limit)->offset($offset)->order_by($sort_by, $sort_order)->form_spd_dalam()->num_rows();
            }
            

            $this->_render_page('form_spd_dalam/index', $this->data);
        }
    }

    function submit($id=0)
    {
        $user_id = $this->session->userdata('user_id');
        if ($id == 0) {
            $task_id = $this->uri->segment(3);
        }else{
            $task_id = $id;
        }

        if (!$this->ion_auth->logged_in())
        {
            //redirect them to the login page
            redirect('auth/login', 'refresh');
        }
        else
        {
            if(is_admin()){
                $data_result = $this->data['task_detail'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$task_id)->form_spd_dalam_admin($id)->result();
                $this->data['td_num_rows'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$task_id)->form_spd_dalam_admin()->num_rows($id);
            }else{
                $data_result = $this->data['task_detail'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$task_id)->form_spd_dalam($id)->result();
                $this->data['td_num_rows'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$task_id)->form_spd_dalam()->num_rows($id);
            }
            //get task creator id
            foreach ($data_result as $dr) {
                $created_by_id = $dr->task_creator;
                $receiver_user_id = $dr->task_receiver;
            }


            //get task creator name
            $query_result = $this->form_spd_dalam_model->where('users.id',$created_by_id)->get_emp_detail()->result();
            foreach ($query_result as $qr) {
                $this->data['task_creator_nm'] = $qr->first_name." ".$qr->last_name;
            }

            //get Receiver Info From API
            $receiver_info = $this->get_receiver_info($receiver_user_id);

            $this->data['task_receiver_nm'] = (!empty($receiver_info['NAME'])) ? $receiver_info['NAME'] : '-';
            $this->data['task_receiver_org'] = (!empty($receiver_info['ORGANIZATION'])) ? $receiver_info['ORGANIZATION'] : '-';
            $this->data['task_receiver_pos'] = (!empty($receiver_info['POSITION'])) ? $receiver_info['POSITION'] : '-';

            //get tast receiver name
            $query_result = $this->form_spd_dalam_model->where('users.id',$receiver_user_id)->get_emp_detail()->result();
            foreach ($query_result as $qr) {
                $this->data['task_receiver_nm'] = $qr->first_name." ".$qr->last_name;
            }

            //get task creator detail
            $this->data['task_creator'] = $this->form_spd_dalam_model->where('users.nik',$created_by_id)->get_emp_detail()->result();
            $this->data['tc_num_rows'] = $this->form_spd_dalam_model->where('users.nik',$created_by_id)->get_emp_detail()->num_rows();
            $this->data['task_receiver'] = $this->form_spd_dalam_model->where('users.id',$receiver_user_id)->get_emp_detail()->result();
            $this->data['tr_num_rows'] = $this->form_spd_dalam_model->where('users.id',$receiver_user_id)->get_emp_detail()->num_rows();

            //get data from API
            $this->get_user_info($created_by_id);
            //get user org_id
            $data_result = $this->form_spd_dalam_model->where('users.id',$user_id)->get_org_id()->result();
            foreach ($data_result as $dr) {
                $org_id = $dr->organization_id;
            }

            // render employee
            $this->data['employee_list'] = $this->form_spd_dalam_model->where('users_employement.organization_id',$org_id)->render_emp()->result();
            $this->data['el_num_rows'] = $this->form_spd_dalam_model->where('users_employement.organization_id',$org_id)->render_emp()->num_rows();
            


            $this->_render_page('form_spd_dalam/submit', $this->data);
        }
    }

    public function do_submit($id)
    {
        $user_id = $this->session->userdata('user_id');
        $date_now = date('Y-m-d');

        $receiver_id = $this->db->where('id', $id)->get('users_spd_dalam')->row('task_creator');
        $additional_data = array(
        'is_submit' => 1,  
        'date_submit' => $date_now);

        if($this->form_spd_dalam_model->update($id,$additional_data)) {
        $this->send_spd_submitted_mail($id, $receiver_id);
        redirect('form_spd_dalam/submit/'.$id,'refresh');
       }
    }

    public function input()
    {
        $user_id = $this->session->userdata('user_id');

        if (!$this->ion_auth->logged_in())
        {
            //redirect them to the login page
            redirect('auth/login', 'refresh');
        }
        else
        {
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            //get task creator name
            $query_result = $this->form_spd_dalam_model->where('users.id',$user_id)->get_emp_detail()->result();
            foreach ($query_result as $qr) {
                $this->data['task_creator_nm'] = $qr->first_name." ".$qr->last_name;
            }

            //get user org_id
            $data_result = $this->form_spd_dalam_model->where('users.id',$user_id)->get_org_id()->result();
            foreach ($data_result as $dr) {
                $org_id = $dr->organization_id;
            }

            //get tast receiver name
            $query_result = $this->form_spd_dalam_model->where('users_employement.organization_id',$org_id)->get_emp_detail()->result();
            foreach ($query_result as $qr) {
                $this->data['task_receiver_nm'] = $qr->first_name." ".$qr->last_name;
            }

            //get_task_receiver_from_same_organization
            $this->get_task_receiver();
            $this->get_user_info($user_id);

            $this->data['all_users'] = $this->form_spd_dalam_model->render_emp()->result();

            //get task creator detail
            $this->data['task_creator'] = $this->form_spd_dalam_model->where('users.id',$user_id)->get_emp_detail()->result();
            $this->data['tc_num_rows'] = $this->form_spd_dalam_model->where('users.id',$user_id)->get_emp_detail()->num_rows();

            //get user org_id
            $data_result = $this->form_spd_dalam_model->where('users.id',$user_id)->get_org_id()->result();
            foreach ($data_result as $dr) {
                $org_id = $dr->organization_id;
            }

            // render employee
            $this->data['employee_list'] = $this->form_spd_dalam_model->where('users_employement.organization_id',$org_id)->render_emp()->result();
            $this->data['el_num_rows'] = $this->form_spd_dalam_model->where('users_employement.organization_id',$org_id)->render_emp()->num_rows();
            

            $this->_render_page('form_spd_dalam/input', $this->data);
        }
    }

    public function add()
    {

        $this->form_validation->set_rules('destination', 'Tujuan', 'trim|required');
        $this->form_validation->set_rules('title', 'Tanggal Terakhir spd_dalam', 'trim|required');
        $this->form_validation->set_rules('date_spd', 'Tanggal Berangkat', 'trim|required');
        $this->form_validation->set_rules('spd_start_time', 'Waktu Berangkat', 'trim|required');
        $this->form_validation->set_rules('spd_end_time', 'Waktu Selesai', 'trim|required');
        
        if($this->form_validation->run() == FALSE)
        {
            echo json_encode(array('st'=>0, 'errors'=>validation_errors('<div class="alert alert-danger" role="alert">', '</div>')));
        }
        else
        {
            $user_id    = $this->input->post('employee');

            $start_spd_dalam = $this->input->post('start_spd_dalam');
            $end_spd_dalam = $this->input->post('end_spd_dalam');

            $additional_data = array(
                'task_creator'          => $this->input->post('emp_tc'),
                'title'                 => $this->input->post('title'),
                'destination'           => $this->input->post('destination'),
                'date_spd'              => date('Y-m-d', strtotime($this->input->post('date_spd'))),
                'start_time'            => $this->input->post('spd_start_time'),
                'end_time'              => $this->input->post('spd_end_time'),
                'created_on'            => date('Y-m-d',strtotime('now')),
                'created_by'            => $this->session->userdata('user_id')
            );

            $num_rows = $this->form_spd_dalam_model->form_spd_dalam_admin()->num_rows();

             if($num_rows>0){
                $spd_id = $this->db->select('id')->order_by('id', 'asc')->get('users_spd_dalam')->last_row();
                $spd_id = $spd_id->id+1;
            }else{
                $spd_id = 1;
            }

            $sender_id = $this->input->post('emp_tc');

            if ($this->form_validation->run() == true && $this->form_spd_dalam_model->create_($user_id,$additional_data))
            {
                
                $this->send_spd_mail($spd_id, $user_id, $sender_id);
                echo json_encode(array('st' =>1));   
            }
        }
    }

    public function report($id)
    {
        $user_id = $this->session->userdata('user_id');
        $report_id = $this->db->where('users_spd_dalam_report.user_spd_dalam_id', $id)->get('users_spd_dalam_report')->row('id');

        if (!$this->ion_auth->logged_in())
        {
            //redirect them to the login page
            redirect('auth/login', 'refresh');
        }
        else
        {
            $this->data['photo'] = array(
            'name'  => 'photo',
            'id'    => 'photo',
            'class'    => 'input-file-control',
        );
            $this->data['message'] = $this->session->flashdata('message');

            $receiver_user_id = $this->db->where('id', $id)->get('users_spd_dalam')->row('task_receiver');
            
            $date_spd = date_create($this->db->where('id', $id)->get('users_spd_dalam')->row('date_spd'));
            $date_now = date_create(date('Y-m-d',strtotime('now')));
            $this->data['lama_pjd'] = date_diff($date_spd, $date_now)->days + 1;

            $receiver_info = $this->get_receiver_info($receiver_user_id);

            $this->data['task_receiver_nm'] = (!empty($receiver_info['NAME'])) ? $receiver_info['NAME'] : '-';
            $this->data['task_receiver_org'] = (!empty($receiver_info['ORGANIZATION'])) ? $receiver_info['ORGANIZATION'] : '-';
            $this->data['task_receiver_pos'] = (!empty($receiver_info['POSITION'])) ? $receiver_info['POSITION'] : '-';

            if(is_admin()){
                $data_result = $this->data['task_detail'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$id)->form_spd_dalam_admin($id)->result();
                $this->data['td_num_rows'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$id)->form_spd_dalam_admin($id)->num_rows();
            }else{
                $data_result = $this->data['task_detail'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$id)->form_spd_dalam($id)->result();
                $this->data['td_num_rows'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$id)->form_spd_dalam($id)->num_rows();
            }
            $this->data['user_folder'] = $user_folder = $this->db->where('id', $id)->get('users_spd_dalam')->row('task_receiver');

            if(is_admin()){
                $report = $this->data['report'] = $this->form_spd_dalam_model->where('users_spd_dalam_report.user_spd_dalam_id', $id)->form_spd_dalam_report_admin($report_id)->result();
                $n_report = $this->data['n_report'] = $this->form_spd_dalam_model->where('users_spd_dalam_report.user_spd_dalam_id', $id)->form_spd_dalam_report_admin($report_id)->num_rows();
            }else{
                $report = $this->data['report'] = $this->form_spd_dalam_model->where('users_spd_dalam_report.user_spd_dalam_id', $id)->form_spd_dalam_report($report_id)->result();
                $n_report = $this->data['n_report'] = $this->form_spd_dalam_model->where('users_spd_dalam_report.user_spd_dalam_id', $id)->form_spd_dalam_report($report_id)->num_rows();
            }
            if($n_report==0){
                $this->data['tujuan'] = '';
                $this->data['hasil'] = '';
                $this->data['attachment'] = '-';
                $this->data['disabled'] = '';

            
            }else{
                foreach ($report as $key) {
                $this->data['id_report'] = $key->id;    
                $this->data['tujuan'] = $key->description;
                $this->data['hasil'] = $key->result;
                $this->data['attachment'] = (!empty($key->attachment)) ? $key->attachment : 2 ;
                $this->data['created_on'] = $key->created_on;
                $this->data['disabled'] = 'disabled='.'"disabled"';
            }}

            $this->_render_page('form_spd_dalam/report', $this->data);
        }
    }

    public function add_report($spd_id)
    {
        $this->form_validation->set_rules('maksud', 'Maksud dan Tujuan', 'trim|required');
        $this->form_validation->set_rules('hasil', 'Hasil Kegiatan', 'trim|required');
        
        if($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect('form_spd_dalam/report/'.$spd_id, 'refresh');
        }
        else
        {

            $user_folder = $this->db->where('id', $spd_id)->get('users_spd_dalam')->row('task_receiver');
            if(!is_dir('./'.'uploads/pdf/')){
            mkdir('./'.'uploads/pdf/', 0777);
            }
            if(!is_dir('./uploads/pdf/'.$user_folder)){
            mkdir('./uploads/pdf/'.$user_folder, 0777);
            }

                $config =  array(
                  'upload_path'     => "./uploads/pdf/".$user_folder,
                  'allowed_types'   => '*',
                  'overwrite'       => TRUE,
                );    
                $this->load->library('upload', $config);
                if(!$this->upload->do_upload())
                {
                    $additional_data = array(
                        'description'   => $this->input->post('maksud'),
                        'result'        => $this->input->post('hasil'),
                        'date_submit'   => date('Y-m-d',strtotime('now')),
                        'created_on'    => date('Y-m-d',strtotime('now')),
                        'created_by'    => $this->session->userdata('user_id')
                    );
                }
                else
                {
                    $upload_data = $this->upload->data();
                    $file_name = $upload_data['file_name'];
                
                    $additional_data = array(
                        'description'   => $this->input->post('maksud'),
                        'result'        => $this->input->post('hasil'),
                        'attachment'    => $file_name,
                        'date_submit'   => date('Y-m-d',strtotime('now')),
                        'created_on'    => date('Y-m-d',strtotime('now')),
                        'created_by'    => $this->session->userdata('user_id')
                    );
                }

                $receiver_id = $this->db->where('id', $spd_id)->get('users_spd_dalam')->row('task_creator');
            if ($this->form_validation->run() == true && $this->form_spd_dalam_model->create_report($spd_id,$additional_data))
            {
                $this->send_spd_report_mail($spd_id, $receiver_id);
                redirect('form_spd_dalam/report/'.$spd_id, 'refresh');  
            }          
        }

    }

     public function update_report($report_id)
    {
        $spd_id = $this->db->where('id', $report_id)->get('users_spd_dalam_report')->row('user_spd_dalam_id');
        $this->form_validation->set_rules('maksud', 'Maksud dan Tujuan', 'trim|required');
        $this->form_validation->set_rules('hasil', 'Hasil Kegiatan', 'trim|required');
        
        if($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect('form_spd_dalam/report/'.$spd_id, 'refresh');
        }
        else
        {

            $user_folder = $this->db->where('id', $spd_id)->get('users_spd_dalam')->row('task_receiver');
            if(!is_dir('./'.'uploads/pdf/')){
            mkdir('./'.'uploads/pdf/', 0777);
            }
            if(!is_dir('./uploads/pdf/'.$user_folder)){
            mkdir('./uploads/pdf/'.$user_folder, 0777);
            }

                $config =  array(
                  'upload_path'     => "./uploads/pdf/".$user_folder,
                  'allowed_types'   => '*',
                  'overwrite'       => TRUE,
                );    
                $this->load->library('upload', $config);
                if(!$this->upload->do_upload())
                {
                    $additional_data = array(
                        'description'   => $this->input->post('maksud'),
                        'result'        => $this->input->post('hasil'),
                        'attachment'    => '',
                        'date_submit'   => date('Y-m-d',strtotime('now')),
                        'edited_on'    => date('Y-m-d',strtotime('now')),
                        'edited_by'    => $this->session->userdata('user_id')
                    );
                }
                else
                {
                    $upload_data = $this->upload->data();
                    $file_name = $upload_data['file_name'];
                
                    $additional_data = array(
                        'description'   => $this->input->post('maksud'),
                        'result'        => $this->input->post('hasil'),
                        'attachment'    => $file_name,
                        'date_submit'   => date('Y-m-d',strtotime('now')),
                        'edited_on'    => date('Y-m-d',strtotime('now')),
                        'edited_by'    => $this->session->userdata('user_id')
                    );
                }

                $receiver_id = $this->db->where('id', $spd_id)->get('users_spd_dalam')->row('task_creator');
            if ($this->form_validation->run() == true && $this->form_spd_dalam_model->update_report($report_id,$additional_data))
            {
                $this->send_spd_report_mail($spd_id, $receiver_id);
                redirect('form_spd_dalam/report/'.$spd_id, 'refresh');  
            }          
        }

    }

    function send_spd_mail($spd_id, $receiver_id, $sender)
    {
        $url = base_url().'form_spd_dalam/submit/'.$spd_id;
        $data = array(
                    'sender_id' => $sender,
                    'receiver_id' => $receiver_id,
                    'sent_on' => date('Y-m-d-H-i-s',strtotime('now')),
                    'subject' => 'Pemberian Tugas Perjalanan Dinas Dalam Kota',
                    'email_body' => get_name($sender).' memberikan tugas perjalan dinas dalam kota, untuk melihat detail silakan <a href='.$url.'>Klik Disini</a><br/>'.$this->detail_email_submit($spd_id),
                    'is_read' => 0,
                );
            $this->db->insert('email', $data);
    }

    function send_spd_submitted_mail($spd_id, $receiver_id)
    {
        $url = base_url().'form_spd_dalam/submit/'.$spd_id;
        $sender = (!empty(get_nik($this->session->userdata('user_id')))) ? get_nik($this->session->userdata('user_id')) : $this->session->userdata('user_id');
        $data = array(
                    'sender_id' => $sender,
                    'receiver_id' => $receiver_id,
                    'sent_on' => date('Y-m-d-H-i-s',strtotime('now')),
                    'subject' => 'Persetujuan Tugas Perjalanan Dinas Dalam Kota',
                    'email_body' => get_name($sender).' telah menyetujui tugas perjalan dinas dalam kota yang anda berikan, untuk melihat detail silakan <a href='.$url.'>Klik Disini</a><br/>'.$this->detail_email_submit($spd_id),
                    'is_read' => 0,
                );
        $this->db->insert('email', $data);
    }
    
    function send_spd_report_mail($spd_id, $receiver_id)
    {
        $url = base_url().'form_spd_dalam/report/'.$spd_id;
        $sender = (!empty(get_nik($this->session->userdata('user_id')))) ? get_nik($this->session->userdata('user_id')) : $this->session->userdata('user_id');
        $data = array(
                    'sender_id' => $sender,
                    'receiver_id' => $receiver_id,
                    'sent_on' => date('Y-m-d-H-i-s',strtotime('now')),
                    'subject' => 'Laporan Tugas Perjalanan Dinas Dalam Kota',
                    'email_body' => get_name($sender).' telah membuat laporan perjalanan dinas dalam kota, untuk melihat detail silakan <a href='.$url.'>Klik Disini</a><br/>'.$this->detail_email_report($spd_id),
                    'is_read' => 0,
                );
            $this->db->insert('email', $data);
    }

    function detail_email_submit($id)
    {
        $task_id = $id;
        $user_id = $this->session->userdata('user_id');
        if(is_admin()){
                $data_result = $this->data['task_detail'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$task_id)->form_spd_dalam_admin($id)->result();
                $this->data['td_num_rows'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$task_id)->form_spd_dalam_admin()->num_rows($id);
            }else{
                $data_result = $this->data['task_detail'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$task_id)->form_spd_dalam($id)->result();
                $this->data['td_num_rows'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$task_id)->form_spd_dalam()->num_rows($id);
            }
            //get task creator id
            foreach ($data_result as $dr) {
                $created_by_id = $dr->task_creator;
                $receiver_user_id = $dr->task_receiver;
            }


            //get task creator name
            $query_result = $this->form_spd_dalam_model->where('users.id',$created_by_id)->get_emp_detail()->result();
            foreach ($query_result as $qr) {
                $this->data['task_creator_nm'] = $qr->first_name." ".$qr->last_name;
            }

            //get Receiver Info From API
            $receiver_info = $this->get_receiver_info($receiver_user_id);

            $this->data['task_receiver_nm'] = (!empty($receiver_info['NAME'])) ? $receiver_info['NAME'] : '-';
            $this->data['task_receiver_org'] = (!empty($receiver_info['ORGANIZATION'])) ? $receiver_info['ORGANIZATION'] : '-';
            $this->data['task_receiver_pos'] = (!empty($receiver_info['POSITION'])) ? $receiver_info['POSITION'] : '-';

            //get tast receiver name
            $query_result = $this->form_spd_dalam_model->where('users.id',$receiver_user_id)->get_emp_detail()->result();
            foreach ($query_result as $qr) {
                $this->data['task_receiver_nm'] = $qr->first_name." ".$qr->last_name;
            }

            //get task creator detail
            $this->data['task_creator'] = $this->form_spd_dalam_model->where('users.nik',$created_by_id)->get_emp_detail()->result();
            $this->data['tc_num_rows'] = $this->form_spd_dalam_model->where('users.nik',$created_by_id)->get_emp_detail()->num_rows();
            $this->data['task_receiver'] = $this->form_spd_dalam_model->where('users.id',$receiver_user_id)->get_emp_detail()->result();
            $this->data['tr_num_rows'] = $this->form_spd_dalam_model->where('users.id',$receiver_user_id)->get_emp_detail()->num_rows();

            //get data from API
            $this->get_user_info($created_by_id);
            //get user org_id
            $data_result = $this->form_spd_dalam_model->where('users.id',$user_id)->get_org_id()->result();
            foreach ($data_result as $dr) {
                $org_id = $dr->organization_id;
            }

            // render employee
            $this->data['employee_list'] = $this->form_spd_dalam_model->where('users_employement.organization_id',$org_id)->render_emp()->result();
            $this->data['el_num_rows'] = $this->form_spd_dalam_model->where('users_employement.organization_id',$org_id)->render_emp()->num_rows();
            
            return $this->load->view('form_spd_dalam/spd_dalam_mail', $this->data, TRUE);
    } 

    function detail_email_report($id)
    {
        $user_id = $this->session->userdata('user_id');
        $report_id = $this->db->where('users_spd_dalam_report.user_spd_dalam_id', $id)->get('users_spd_dalam_report')->row('id');

        if (!$this->ion_auth->logged_in())
        {
            //redirect them to the login page
            redirect('auth/login', 'refresh');
        }
        else
        {
            $this->data['photo'] = array(
            'name'  => 'photo',
            'id'    => 'photo',
            'class'    => 'input-file-control',
        );
            $this->data['message'] = $this->session->flashdata('message');

            $receiver_user_id = $this->db->where('id', $id)->get('users_spd_dalam')->row('task_receiver');
            
            $date_spd = date_create($this->db->where('id', $id)->get('users_spd_dalam')->row('date_spd'));
            $date_now = date_create(date('Y-m-d',strtotime('now')));
            $this->data['lama_pjd'] = date_diff($date_spd, $date_now)->days + 1;

            $receiver_info = $this->get_receiver_info($receiver_user_id);

            $this->data['task_receiver_nm'] = (!empty($receiver_info['NAME'])) ? $receiver_info['NAME'] : '-';
            $this->data['task_receiver_org'] = (!empty($receiver_info['ORGANIZATION'])) ? $receiver_info['ORGANIZATION'] : '-';
            $this->data['task_receiver_pos'] = (!empty($receiver_info['POSITION'])) ? $receiver_info['POSITION'] : '-';

            if(is_admin()){
                $data_result = $this->data['task_detail'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$id)->form_spd_dalam_admin($id)->result();
                $this->data['td_num_rows'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$id)->form_spd_dalam_admin($id)->num_rows();
            }else{
                $data_result = $this->data['task_detail'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$id)->form_spd_dalam($id)->result();
                $this->data['td_num_rows'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$id)->form_spd_dalam($id)->num_rows();
            }
            $this->data['user_folder'] = $user_folder = $this->db->where('id', $id)->get('users_spd_dalam')->row('task_receiver');

            if(is_admin()){
                $report = $this->data['report'] = $this->form_spd_dalam_model->where('users_spd_dalam_report.user_spd_dalam_id', $id)->form_spd_dalam_report_admin($report_id)->result();
                $n_report = $this->data['n_report'] = $this->form_spd_dalam_model->where('users_spd_dalam_report.user_spd_dalam_id', $id)->form_spd_dalam_report_admin($report_id)->num_rows();
            }else{
                $report = $this->data['report'] = $this->form_spd_dalam_model->where('users_spd_dalam_report.user_spd_dalam_id', $id)->form_spd_dalam_report($report_id)->result();
                $n_report = $this->data['n_report'] = $this->form_spd_dalam_model->where('users_spd_dalam_report.user_spd_dalam_id', $id)->form_spd_dalam_report($report_id)->num_rows();
            }
            if($n_report==0){
                $this->data['tujuan'] = '';
                $this->data['hasil'] = '';
                $this->data['attachment'] = '-';
                $this->data['disabled'] = '';

            
            }else{
                foreach ($report as $key) {
                $this->data['id_report'] = $key->id;    
                $this->data['tujuan'] = $key->description;
                $this->data['hasil'] = $key->result;
                $this->data['attachment'] = (!empty($key->attachment)) ? $key->attachment : 2 ;
                $this->data['created_on'] = $key->created_on;
                $this->data['disabled'] = 'disabled='.'"disabled"';
            }}

            return $this->load->view('form_spd_dalam/spd_dalam_report_mail', $this->data, TRUE);
        }
    }


    public function get_emp_org()
    {
        $id = $this->input->post('id');

        $url = 'http://admin:12345678@localhost/hris_api/users/employement/EMPLID/'.$id.'/format/json';
            $headers = get_headers($url);
            $response = substr($headers[0], 9, 3);
            if ($response != "404") {
                $getuser_info = file_get_contents($url);
                $user_info = json_decode($getuser_info, true);
                $org_nm = $user_info['ORGANIZATION'];
            } else {
                $org_nm = '';
            }
        
        echo $org_nm;
    }

    public function get_emp_pos()
    {
        $id = $this->input->post('id');

        $url = 'http://admin:12345678@localhost/hris_api/users/employement/EMPLID/'.$id.'/format/json';
            $headers = get_headers($url);
            $response = substr($headers[0], 9, 3);
            if ($response != "404") {
                $getuser_info = file_get_contents($url);
                $user_info = json_decode($getuser_info, true);
                $pos_nm = $user_info['POSITION'];
            } else {
                $pos_nm = '';
            }

        echo $pos_nm;
    }
    
    function get_user_info($user_id)
    {
        $user = $this->person_model->getUsers($user_id)->row();
            $url = 'http://admin:12345678@localhost/hris_api/users/employement/EMPLID/'.$user_id.'/format/json';
            $headers = get_headers($url);
            $response = substr($headers[0], 9, 3);
            if ($response != "404") {
                $getuser_info = file_get_contents($url);
                $user_info = json_decode($getuser_info, true);
                return $this->data['user_info'] = $user_info;
            } else {
                return $this->data['user_info'] = '';
            }
    }

    function get_receiver_info($receiver_nik)
    {
            $url = 'http://admin:12345678@localhost/hris_api/users/employement/EMPLID/'.$receiver_nik.'/format/json';
            $headers = get_headers($url);
            $response = substr($headers[0], 9, 3);
            if ($response != "404") {
                $getuser_info = file_get_contents($url);
                $user_info = json_decode($getuser_info, true);
                return $this->data['user_info'] = $user_info;
            } else {
                return $this->data['user_info'] = '';
            }
    }

    function get_task_receiver()
    {
            $user_id = $this->session->userdata('user_id');
            $user = $this->person_model->getUsers($user_id)->row();
            $data_result = $this->form_spd_dalam_model->where('users.id',$user_id)->get_org_id()->result();
            foreach ($data_result as $dr) {
                $org_id = $dr->organization_id;
            }
            $url_org = 'http://admin:12345678@localhost/hris_api/users/org/EMPLID/'.$user->nik.'/format/json';
            $headers_org = get_headers($url_org);
            $response = substr($headers_org[0], 9, 3);
            if ($response != "404") {
            $get_task_receiver = file_get_contents($url_org);
            $task_receiver = json_decode($get_task_receiver, true);
            return $this->data['task_receiver'] = $task_receiver;
            }else{
                $data_result = $this->form_spd_dalam_model->where('users.id',$user_id)->get_org_id()->result();
            foreach ($data_result as $dr) {
                $org_id = $dr->organization_id;
            }
             return $this->data['task_receiver_2'] = $this->form_spd_dalam_model->where('users_employement.organization_id',$org_id)->render_emp()->result();
            }
    }

    public function get_tr($id)
    {
        $url = get_api_key().'users/org/EMPLID/'.$id.'/format/json';
        //print_r($url);
            $headers = get_headers($url);
            $response = substr($headers[0], 9, 3);
            if ($response != "404") {
                $get_task_receiver = file_get_contents($url);
                $task_receiver = json_decode($get_task_receiver, true);
                 foreach ($task_receiver as $row)
                    {
                        $result['0']= '-- Pilih User --';
                        $result[$row['ID']]= ucwords(strtolower($row['NAME']));
                    }
            } else {
               $result['-']= '- Tidak ada user dengan departemen yang sama -';
            }
        $data['result']=$result;
        $this->load->view('dropdown_tc',$data);
    }
    
    function pdf($id)
    {
        $user_id = $this->session->userdata('user_id');
        if ($id == 0) {
            $task_id = $this->uri->segment(3);
        }else{
            $task_id = $id;
        }

        $title_spd = $this->db->where('id', $id)->get('users_spd_dalam')->row('title');
        $title = $this->data['title'] = 'Form-SPD-Dalam-'.$title_spd;

            if(is_admin()){
                $data_result = $this->data['task_detail'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$task_id)->form_spd_dalam_admin($id)->result();
                $this->data['td_num_rows'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$task_id)->form_spd_dalam_admin()->num_rows($id);
            }else{
                $data_result = $this->data['task_detail'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$task_id)->form_spd_dalam($id)->result();
                $this->data['td_num_rows'] = $this->form_spd_dalam_model->where('users_spd_dalam.id',$task_id)->form_spd_dalam()->num_rows($id);
            }
            //get task creator id
            foreach ($data_result as $dr) {
                $created_by_id = $dr->task_creator;
                $receiver_user_id = $dr->task_receiver;
            }


            //get task creator name
            $query_result = $this->form_spd_dalam_model->where('users.id',$created_by_id)->get_emp_detail()->result();
            foreach ($query_result as $qr) {
                $task_creator = $this->data['task_creator_nm'] = $qr->first_name." ".$qr->last_name;
            }

            //get Receiver Info From API
            $receiver_info = $this->get_receiver_info($receiver_user_id);

            $this->data['task_receiver_nm'] = (!empty($receiver_info['NAME'])) ? $receiver_info['NAME'] : '-';
            $this->data['task_receiver_org'] = (!empty($receiver_info['ORGANIZATION'])) ? $receiver_info['ORGANIZATION'] : '-';
            $this->data['task_receiver_pos'] = (!empty($receiver_info['POSITION'])) ? $receiver_info['POSITION'] : '-';

            //get tast receiver name
            $query_result = $this->form_spd_dalam_model->where('users.id',$receiver_user_id)->get_emp_detail()->result();
            foreach ($query_result as $qr) {
                $this->data['task_receiver_nm'] = $qr->first_name." ".$qr->last_name;
            }

            //get task creator detail
            $this->data['task_creator'] = $this->form_spd_dalam_model->where('users.nik',$created_by_id)->get_emp_detail()->result();
            $this->data['tc_num_rows'] = $this->form_spd_dalam_model->where('users.nik',$created_by_id)->get_emp_detail()->num_rows();
            $this->data['task_receiver'] = $this->form_spd_dalam_model->where('users.id',$receiver_user_id)->get_emp_detail()->result();
            $this->data['tr_num_rows'] = $this->form_spd_dalam_model->where('users.id',$receiver_user_id)->get_emp_detail()->num_rows();

            //get data from API
            $this->get_user_info($created_by_id);
            //get user org_id
            $data_result = $this->form_spd_dalam_model->where('users.id',$user_id)->get_org_id()->result();
            foreach ($data_result as $dr) {
                $org_id = $dr->organization_id;
            }

            // render employee
            $this->data['employee_list'] = $this->form_spd_dalam_model->where('users_employement.organization_id',$org_id)->render_emp()->result();
            $this->data['el_num_rows'] = $this->form_spd_dalam_model->where('users_employement.organization_id',$org_id)->render_emp()->num_rows();
            

            $this->load->library('mpdf60/mpdf');
            $html = $this->load->view('spd_dalam_pdf', $this->data, true); 
            $mpdf = new mPDF();
            $mpdf = new mPDF('A4');
            $mpdf->WriteHTML($html);
            $mpdf->Output($id.'-'.$title.'-'.$task_creator.'pdf', 'I');
        
    }


    function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key   = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return array($key => $value);
    }

    function _valid_csrf_nonce()
    {
        if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
            $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    function _render_page($view, $data=null, $render=false)
    {
        $data = (empty($data)) ? $this->data : $data;
        if ( ! $render)
        {
            $this->load->library('template');

                if(in_array($view, array('form_spd_dalam/index')))
                {
                    $this->template->set_layout('default');

                    $this->template->add_js('jquery.min.js');
                    $this->template->add_js('bootstrap.min.js');
                    
                    
                    $this->template->add_js('core.js');
                    $this->template->add_js('jquery-ui-1.10.1.custom.min.js');
                    $this->template->add_js('jquery.sidr.min.js');

                    
                    $this->template->add_css('jquery-ui-1.10.1.custom.min.css');
                    
                }
                elseif(in_array($view, array('form_spd_dalam/input',
                                             'form_spd_dalam/submit',
                                             'form_spd_dalam/report'
                                             )))
                {

                    $this->template->set_layout('default');

                    $this->template->add_js('jquery.min.js');
                    $this->template->add_js('bootstrap.min.js');

                    $this->template->add_js('jquery-ui-1.10.1.custom.min.js');
                    $this->template->add_js('jquery.sidr.min.js');
                    $this->template->add_js('breakpoints.js');
                    $this->template->add_js('select2.min.js');

                    $this->template->add_js('core.js');
                    $this->template->add_js('purl.js');

                    $this->template->add_js('main.js');
                    $this->template->add_js('respond.min.js');

                    $this->template->add_js('jquery.bootstrap.wizard.min.js');
                    $this->template->add_js('jquery.validate.min.js');
                    $this->template->add_js('bootstrap-datepicker.js');
                    $this->template->add_js('jquery.slimscroll.js');
                    $this->template->add_js('bootstrap-timepicker.js');
                    $this->template->add_js('form_spd_dalam_input.js');
                    
                    $this->template->add_css('jquery-ui-1.10.1.custom.min.css');
                    $this->template->add_css('plugins/select2/select2.css');
                    $this->template->add_css('datepicker.css');
                    $this->template->add_css('bootstrap-timepicker.css');
                     
                }


            if ( ! empty($data['title']))
            {
                $this->template->set_title($data['title']);
            }

            $this->template->load_view($view, $data);
        }
        else
        {
            return $this->load->view($view, $data, TRUE);
        }
    }
}

/* End of file form_spd_dalam.php */
/* Location: ./application/modules/form_spd_dalam/controllers/form_spd_dalam.php */