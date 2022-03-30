<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Media</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><a href="#">Media</a></li>
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
            <h3 class="card-title">Form Input Media</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <?php echo (isset($alert)) ? $alert:'' ;?>
            <form method="post" action="<?php echo base_url()?>master/store_media" enctype="multipart/form-data">
              <input type="hidden" name="id" value="<?php echo (!empty($data)) ? $data->id:'';?>">
              <div class="form-group">
                <label>Judul</label>
                <input type="text" class="form-control" id="judul" name="judul" placeholder="Judul" value="<?php echo (!empty($data)) ? $data->judul:'';?>" required="">
              </div>
              <div class="form-group">
                <label>Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" placeholder="Keterangan" rows="5" required=""><?php echo (!empty($data)) ? $data->keterangan:'';?></textarea>
              </div>
              <div class="form-group">
                <label>URL</label>
                <input type="text" class="form-control" id="link" name="link" placeholder="URL" value="<?php echo (!empty($data)) ? $data->link:'';?>" required="">
              </div>
              <div class="form-group">
                <label>Upload Gambar</label>
                <input type="file" name="gambar" id="gambar" class="form-control" accept="png,jpg,jpeg">
                <span class="text-muted">*kosongi jika tidak ingin mengganti</span>
                <?php if(!empty($data->image)):?>
                <a target="_blank" href="<?php echo base_url('assets/upload/images/'.$data->image)?>">
                  <p><?php echo $data->image?></p>
                </a>
                <?php endif;?>
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