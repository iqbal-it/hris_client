<!-- BEGIN PAGE CONTAINER-->
<div class="page-content"> 
  <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
  <div id="portlet-config" class="modal hide">
    <div class="modal-header">
      <button data-dismiss="modal" class="close" type="button"></button>
      <h3>Widget Settings</h3>
    </div>
    <div class="modal-body"> Widget settings form goes here </div>
  </div>
  <div class="clearfix"></div>
  <div class="content">  
  
  
    <div id="container">
      <div class="row">
        <div class="col-md-12">
            <div class="grid simple ">
              <div class="grid-title no-border">
                <h4>Form <a href="<?php echo site_url('form_spd_dalam_group')?>">Perjalanan Dinas <span class="semi-bold">Dalam Kota (Group)</span></a></h4>
                <div class="tools"> 
                  <a href="<?php echo site_url() ?>form_spd_dalam_group/input" class="config"></a>
                </div>
              </div>
                <div class="grid-body no-border"> 
                             <br/>   
                            <?php echo form_open(site_url('form_spd_dalam_group/keywords'))?>
                              <div class="row">
                                  <div class="col-md-5">
                                      <div class="row">
                                          <div class="col-md-4 search_label"><?php echo form_label('Nama','first_name')?></div>
                                          <div class="col-md-8"><?php echo bs_form_input($ftitle_search)?></div>
                                      </div>
                                  </div>
                                  <div class="col-md-2">
                                      <div class="row">
                                          <div class="col-md-12">
                                              <button type="submit" class="btn btn-info"><i class="icon-search"></i>&nbsp;<?php echo lang('search_button')?></button>
                                          </div>
                                      </div>
                                  </div>    
                              </div>
                          <?php echo form_close()?>     
                        <table class="table table-striped table-flip-scroll cf">
                            <thead>
                              <tr>
                                <th width="90%">Kegiatan</th>
                                <th width="10%" class="text-center">appr. spv</th>
                                <th width="10%" class="text-center">appr. ka. bag</th>
                                <th width="10%" class="text-center">appr. Atasan Lainnya</th>
                                <th width="10%" class="text-center">appr. HRD</th>
                                <th width="10%" colspan="2" class="text-center">Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if ($_num_rows > 0) { ?>
                              <?php foreach ($form_spd_dalam_group as $spd) : ?>
                              <?php

                              $txt_app_lv1 = $txt_app_lv2 = $txt_app_lv3 = $txt_app_hrd = "<i class='icon-minus' title = 'Pending'></i>";
                              $approval_status_lv1 = "<i class='icon-ok-sign' title = 'Approved'></i>";
                              $approval_status_lv2 = "<i class='icon-ok-sign' title = 'Approved'></i>";
                              $approval_status_lv3 = "<i class='icon-ok-sign' title = 'Approved'></i>";
                              $approval_status_hrd = "<i class='icon-ok-sign' title = 'Approved'></i>";

                            //Approval Level 1
                              if(empty($spd->user_app_lv1)){
                                 $txt_app_lv1 = "<i class='icon-circle' title = 'Tidak Butuh Approval'></i>";
                                }elseif(!empty($spd->user_app_lv1 && $spd->is_app_lv1 == 1)){
                                  $txt_app_lv1 = "<a href='".site_url('form_spd_dalam_group/submit/'.$spd->id)."''>$approval_status_lv2</a>";
                                }elseif(!empty($spd->user_app_lv1) && $spd->is_app_lv1 == 0 && $sess_nik == $spd->user_app_lv1){
                                  $txt_app_lv1 = "<a href='".site_url('form_spd_dalam_group/submit/'.$spd->id)."''>
                                                  <button type='button' class='btn btn-info btn-small' title='Make Approval'><i class='icon-edit'></i></button>
                                                  </a>";
                                }
                              

                              //ApprovalLevel 2
                              
                              if(empty($spd->user_app_lv2)){
                                 $txt_app_lv2 = "<i class='icon-circle' title = 'Tidak Butuh Approval'></i>";
                                }elseif(!empty($spd->user_app_lv2 && $spd->is_app_lv2 == 1)){
                                  $txt_app_lv2 = "<a href='".site_url('form_spd_dalam_group/submit/'.$spd->id)."''>$approval_status_lv2</a>";
                                }elseif(!empty($spd->user_app_lv2) && $spd->is_app_lv2 == 0 && $sess_nik == $spd->user_app_lv2){
                                  $txt_app_lv2 = "<a href='".site_url('form_spd_dalam_group/submit/'.$spd->id)."''>
                                                  <button type='button' class='btn btn-info btn-small' title='Make Approval'><i class='icon-edit'></i></button>
                                                  </a>";
                                }

                              //Approval Level 3

                              if(empty($spd->user_app_lv3)){
                                 $txt_app_lv3 = "<i class='icon-circle' title = 'Tidak Butuh Approval'></i>";
                                }elseif(!empty($spd->user_app_lv3 && $spd->is_app_lv3 == 1)){
                                  $txt_app_lv3 = "<a href='".site_url('form_spd_dalam_group/submit/'.$spd->id)."''>$approval_status_lv3</a>";
                                }elseif(!empty($spd->user_app_lv3) && $spd->is_app_lv3 == 0 && $sess_nik == $spd->user_app_lv3){
                                  $txt_app_lv3 = "<a href='".site_url('form_spd_dalam_group/submit/'.$spd->id)."''>
                                                  <button type='button' class='btn btn-info btn-small' title='Make Approval'><i class='icon-edit'></i></button>
                                                  </a>";
                                }

                              //Approval HRD
                                if($this->approval->approver('dinas') == $sess_nik && $spd->is_app_hrd == 0){
                                  $txt_app_hrd = "<a href='".site_url('form_spd_dalam_group/submit/'.$spd->id)."''>
                                                  <button type='button' class='btn btn-info btn-small' title='Make Approval'><i class='icon-edit'></i></button>
                                                  </a>";
                                }elseif($spd->is_app_hrd == 1){
                                  $txt_app_hrd =  "<a href='".site_url('form_spd_dalam_group/submit/'.$spd->id)."''>$approval_status_hrd</a>";
                                }

                                $peserta = getAll('users_spd_dalam_group', array('id'=>'where/'.$spd->id))->row('task_receiver');
                                $p = explode(",", $peserta);
                                $user_submit = getAll('users_spd_dalam_group', array('id'=>'where/'.$spd->id))->row('user_submit');
                                $receiver_submit = explode(",", $user_submit);
                                $report_num = getAll('users_spd_dalam_report_group', array('user_spd_dalam_group_id'=>'where/'.$spd->id, 'created_by'=>'where/'.$sess_id))->num_rows();

                                $hidden = (!in_array(get_nik($sess_id), $p)) ? 'style="display:none"' : '';
                                $btn_sub = (in_array(get_nik($sess_id), $p) && !in_array(get_nik($sess_id), $receiver_submit)) ? 'Submit' :((in_array(get_nik($sess_id), $p) && in_array(get_nik($sess_id), $receiver_submit))?'Submitted':'');
                                $btn_rep = ($report_num>0)?'View Report':(($report_num < 1 && in_array(get_nik($sess_id), $receiver_submit))?'Create Report':'Report');
                               ?>
                                <tr>
                                  <td>
                                    <a href="<?php echo base_url() ?>form_spd_dalam_group/submit/<?php echo $spd->id ?>"><h4><?php echo $spd->title ?></h4>
                                      <div class="small-text-custom">
                                        <span>Pemberi tugas : </span><?php echo get_name($spd->task_creator) ?><br/>
                                        <span>Penerima tugas : </span>
                                        <?php
                                          for($i=0;$i<sizeof($p);$i++):
                                            $n = get_name($p[$i]).',';
                                        ?>
                                          <a href="<?php echo site_url('form_spd_dalam_group/submit/'.$spd->id)?>"><?php echo $n;?></a>
                                        <?php endfor;?><br/>
                                        <span>Tanggal : </span><?php echo dateIndo($spd->date_spd) .', '. date('H:i',strtotime($spd->start_time)) .' - '. date('H:i',strtotime($spd->end_time)) ?> WIB<br/>
                                        <span>Tempat : </span><?php echo $spd->destination ?>
                                      </div>
                                    </a>
                                  </td>
                                  <td class="text-center">
                                    <?php echo $txt_app_lv1;?>
                                  </td>
                                  <td class="text-center">
                                    <?php echo $txt_app_lv2;?>
                                  </td>
                                  <td class="text-center">
                                    <?php echo $txt_app_lv3;?>
                                  </td>
                                  <td class="text-center">
                                    <?php echo $txt_app_hrd;?>
                                  </td>
                                  <td>
                                    <div class="list-actions" class="text-center">
                                      <a href="<?php echo base_url() ?>form_spd_dalam_group/submit/<?php echo $spd->id ?>">
                                        <button class="btn btn-primary btn-cons" type="button" <?php echo $hidden?>>
                                          <i class="icon-ok"></i>
                                           <?php echo $btn_sub; ?>
                                        </button>
                                      </a>
                                        <button class="btn btn-info btn-cons" type="button" onclick='window.location.href="<?php echo base_url()?>form_spd_dalam_group/report/<?php echo $spd->id ?>"'>
                                          <i class="icon-paste"></i>
                                          <?php echo $btn_rep; ?>
                                        </button>
                                      <a href="<?php echo base_url() ?>form_spd_dalam_group/pdf/<?php echo $spd->id ?>" target="_blank">
                                        <button class="btn btn-info btn-cons" type="button">
                                          <i class="icon-print"></i>
                                          Print
                                        </button>
                                      </a>
                                    </div>
                                  </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php if($_num_rows>0):?>
                          <div class="row">
                            <div class="col-md-4 page_limit">
                                <?php echo form_open(uri_string());?>
                                <?php 
                                    $selectComponentData = array(
                                        10  => '10',
                                        25 => '25',
                                        50 =>'50',
                                        75 => '75',
                                        100 => '100',);
                                    $selectComponentJs = 'class="select2" onChange="this.form.submit()" id="limit"';
                                    echo "Per page: ".form_dropdown('limit', $selectComponentData, $limit, $selectComponentJs);
                                    echo '&nbsp;'.lang('found_subheading').'&nbsp;'.$num_rows_all.'&nbsp;'.'Pengajuan';
                                ?>
                                <?php echo form_close();?>
                            </div>

                            <div class="col-md-10">
                              <ul class="dataTables_paginate paging_bootstrap pagination">
                                  <?php echo $halaman;?>
                              </ul>
                            </div>
                          </div>
                        <?php endif; ?>
                </div>
            </div>
        </div>
      </div>
    </div>
            
  
    </div>
  
</div>  
<!-- END PAGE -->