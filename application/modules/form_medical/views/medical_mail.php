<div class="row">
        <div class="col-md-12">
          <div class="grid simple">
            <div class="grid-title no-border">
              <h4>Detail Rekapitulasi <span class="semi-bold"><a href="<?php echo site_url('form_medical')?>">Rawat Jalan & Inap</a></span></h4>
            </div>
            <div class="grid-body no-border">
            <h6 class="bold">BAGIAN : <?php echo $bagian?></h6>
              <form class="form-no-horizontal-spacing" id="formMedical"> 
                <div class="row column-seperation">

                  <hr/>
                  <h5 class="text-center"><span class="semi-bold">Rekapitulasi Rawat Jalan & Inap Yang Diajukan</span></h5>
                    <table id="dataTable" class="table table-bordered">
                      <thead>
                        <tr>
                          <th width="5%">NIK</th>
                          <th width="25%">Nama</th>
                          <th width="25%">Nama Pasien</th>
                          <th width="15%">Hubungan</th>
                          <th width="13%">Jenis Pemeriksaan</th>
                          <th width="12%">Rupiah</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                          if(!empty($detail)){
                             $total = $detail[0]['rupiah'];
                             $approved = assets_url('img/approved_stamp.png');
                              for($i=0;$i<sizeof($detail);$i++):
                              ?>
                          <tr>
                            <td><?php echo get_nik($detail[$i]['karyawan_id'])?></td>
                            <td><?php echo get_name($detail[$i]['karyawan_id'])?></td>
                            <td><?php echo $detail[$i]['pasien']?></td>
                            <td><?php echo $detail[$i]['hubungan']?></td>
                            <td><?php echo $detail[$i]['jenis']?></td>
                            <td><?php echo  'Rp. '.number_format($detail[$i]['rupiah'], 0)?></td>
                          </tr>
                            <?php /*
                              if(sizeof($detail)>1){?>
                                <?php if($detail[$i]['karyawan_id'] != $detail[$i+1]['karyawan_id']){
                                    $sub_total = $detail[$i]['rupiah'] + $detail[$i+1]['rupiah']
                                  ?>
                                  <tr>
                                    <td align="right" colspan="5">Total <?php echo $detail[$i]['karyawan_id']?>: </td><td><?php echo $sub_total?></td>
                                  </tr>
                                  <?php } ?>
                            <?php };*/?>
                            <?php
                            if(sizeof($detail)>1 && isset($detail[$i+1])){
                            $total = $total + $detail[$i+1]['rupiah'];
                            }
                            endfor;}
                            ?>
                            <tr>
                            <td align="right" colspan="5">Total : </td><td><?php echo 'Rp. '.number_format($total, 0)?></td>
                            </tr>
                        </tbody>
                      </table>
                  <?php if($is_app_hrd!=0){?>
                  <hr/>
                  <h5 class="text-center"><span class="semi-bold">Rekapitulasi Rawat Jalan & Inap Yang Disetujui HRD</span></h5>
                    <table id="dataTable" class="table table-bordered">
                      <thead>
                        <tr>
                          <th width="5%">NIK</th>
                          <th width="25%">Nama</th>
                          <th width="25%">Nama Pasien</th>
                          <th width="15%">Hubungan</th>
                          <th width="13%">Jenis Pemeriksaan</th>
                          <th width="12%">Rupiah</th>
                          <th width="12%">Disetujui</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                          if(!empty($detail_hrd)){
                             $total = $detail_hrd[0]['rupiah'];
                              for($i=0;$i<sizeof($detail_hrd);$i++):
                              $is_approve = ($detail_hrd[$i]['is_approve'] == 1) ? 'Ya':'Tidak';
                              ?>
                          <tr>
                            <td><?php echo get_nik($detail_hrd[$i]['karyawan_id'])?></td>
                            <td><?php echo get_name($detail_hrd[$i]['karyawan_id'])?></td>
                            <td><?php echo $detail_hrd[$i]['pasien']?></td>
                            <td><?php echo $detail_hrd[$i]['hubungan']?></td>
                            <td><?php echo $detail_hrd[$i]['jenis']?></td>
                            <td><?php echo  'Rp. '.number_format($detail_hrd[$i]['rupiah'], 0)?></td>
                            <td><?php echo $is_approve?></td>
                          </tr>
                            <?php
                            endfor;}
                            ?>
                            <tr>
                            <td align="right" colspan="5">Total yang disetujui HRD: </td><td><?php echo 'Rp. '.number_format($total_medical_hrd, 0)?></td>
                            </tr>
                        </tbody>
                      </table>
                      <?php } ?>
                    </div>
                  </div>
                </div>
             </div>  
            </div>
          </div>
        </div>
      </div>