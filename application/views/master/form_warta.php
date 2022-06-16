<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Data Warta</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Master Data</a></li>
              <li class="breadcrumb-item active">Data Warta</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Form Input Data Warta</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <?php echo (isset($alert)) ? $alert:'' ;?>
            <form method="post" action="<?php echo base_url()?>warta/store_warta">
              <input type="hidden" name="id" value="<?php echo (!empty($data)) ? $data->id:'';?>">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Tanggal Ibadah</label>
                    <input type="text" class="form-control datetimepicker" id="tanggal" name="tanggal" placeholder="Tanggal" value="<?php echo (!empty($data)) ? date('m/d/Y H:i:s', strtotime($data->tanggal)):'';?>" required="" readonly>
                  </div>
                  <div class="form-group">
                    <label>Worship Leader</label>
                      <?php $leader = isset($data->leader) ? $data->leader:0;?>
                     <select class="select2 form-control" name="leader" id="leader" required="">
                      <option value="" disabled="" selected="">Select Option</option>
                      <?php foreach($jemaat as $row): ?>
                      <option value="<?php echo $row->nij?>" <?php echo ($leader == $row->nij) ? 'selected':'';?>><?php echo $row->nama?></option>
                      <?php endforeach;?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Singer 1</label>
                      <?php $singer1 = isset($data->singer1) ? $data->singer1:0;?>
                     <select class="select2 form-control" name="singer1" id="singer1" required="">
                      <option value="" disabled="" selected="">Select Option</option>
                      <?php foreach($jemaat as $row): ?>
                      <option value="<?php echo $row->nij?>" <?php echo ($singer1 == $row->nij) ? 'selected':'';?>><?php echo $row->nama?></option>
                      <?php endforeach;?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Singer 2</label>
                      <?php $singer2 = isset($data->singer2) ? $data->singer2:0;?>
                     <select class="select2 form-control" name="singer2" id="singer2" required="">
                      <option value="" disabled="" selected="">Select Option</option>
                      <?php foreach($jemaat as $row): ?>
                      <option value="<?php echo $row->nij?>" <?php echo ($singer2 == $row->nij) ? 'selected':'';?>><?php echo $row->nama?></option>
                      <?php endforeach;?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Koordinator</label>
                      <?php $koordinator = isset($data->koordinator) ? $data->koordinator:0;?>
                     <select class="select2 form-control" name="koordinator" id="koordinator" required="">
                      <option value="" disabled="" selected="">Select Option</option>
                      <?php foreach($jemaat as $row): ?>
                      <option value="<?php echo $row->nij?>" <?php echo ($koordinator == $row->nij) ? 'selected':'';?>><?php echo $row->nama?></option>
                      <?php endforeach;?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Usher</label>
                    <input type="text" id="usher" name="usher" class="form-control" value="<?php echo (!empty($data)) ? $data->usher:'';?>">
                  </div>
                  <div class="form-group">
                    <label>Kolektan</label>
                    <input type="text" id="kolektan" name="kolektan" class="form-control" value="<?php echo (!empty($data)) ? $data->kolektan:'';?>">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Petugas LCD</label>
                    <input type="text" id="petugas_lcd" name="petugas_lcd" class="form-control" value="<?php echo (!empty($data)) ? $data->petugas_lcd:'';?>">
                  </div>
                  <div class="form-group">
                    <label>Multimedia</label>
                    <input type="text" id="multimedia" name="multimedia" class="form-control" value="<?php echo (!empty($data)) ? $data->multimedia:'';?>">
                  </div>
                  <div class="form-group">
                    <label>Pokok Doa</label>
                    <?php $warta_detail = isset($warta_detail) ? $warta_detail:[];
                    ?>
                    <select class="select2 form-control" name="pokok_doa[]" id="pokok_doa" multiple="multiple">
                      <?php $no=0; 
                      foreach($ms_warta as $row): 
                        $doa = isset($warta_detail[$no]) ? $warta_detail[$no]:0;
                      ?>
                      <option value="<?php echo $row->id?>" <?php echo ($doa == $row->id) ? 'selected':'';?>><?php echo $row->judul?></option>
                      <?php $no++; 
                        endforeach;?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Team Musik</label>
                    <input type="text" id="group" name="group" class="form-control" value="<?php echo (!empty($musik)) ? $musik->group:'';?>">
                  </div>
                  <div class="form-group">
                    <label>Gitar</label>
                      <?php $gitar = isset($musik->gitar) ? $musik->gitar:0;?>
                     <select class="select2 form-control" name="gitar" id="gitar" required="">
                      <option value="" disabled="" selected="">Select Option</option>
                      <?php foreach($jemaat as $row): ?>
                      <option value="<?php echo $row->nij?>" <?php echo ($gitar == $row->nij) ? 'selected':'';?>><?php echo $row->nama?></option>
                      <?php endforeach;?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Bass</label>
                      <?php $bass = isset($musik->bass) ? $musik->bass:0;?>
                     <select class="select2 form-control" name="bass" id="bass" required="">
                      <option value="" disabled="" selected="">Select Option</option>
                      <?php foreach($jemaat as $row): ?>
                      <option value="<?php echo $row->nij?>" <?php echo ($bass == $row->nij) ? 'selected':'';?>><?php echo $row->nama?></option>
                      <?php endforeach;?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Keyboard</label>
                      <?php $keyboard = isset($musik->keyboard) ? $musik->keyboard:0;?>
                     <select class="select2 form-control" name="keyboard" id="keyboard" required="">
                      <option value="" disabled="" selected="">Select Option</option>
                      <?php foreach($jemaat as $row): ?>
                      <option value="<?php echo $row->nij?>" <?php echo ($keyboard == $row->nij) ? 'selected':'';?>><?php echo $row->nama?></option>
                      <?php endforeach;?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Drum</label>
                      <?php $drum = isset($musik->drum) ? $musik->drum:0;?>
                     <select class="select2 form-control" name="drum" id="drum" required="">
                      <option value="" disabled="" selected="">Select Option</option>
                      <?php foreach($jemaat as $row): ?>
                      <option value="<?php echo $row->nij?>" <?php echo ($drum == $row->nij) ? 'selected':'';?>><?php echo $row->nama?></option>
                      <?php endforeach;?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="text-right">
                <button type="submit" class="btn btn-success">Save</button>
                <button type="reset" class="btn btn-danger">Reset</button>
              </div>
            </form>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->