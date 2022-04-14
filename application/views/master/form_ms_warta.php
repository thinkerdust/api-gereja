<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Pokok Doa</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Master</a></li>
              <li class="breadcrumb-item active">Pokok Doa</li>
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
            <h3 class="card-title">Form Input Pokok Doa</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <?php echo (isset($alert)) ? $alert:'' ;?>
            <form method="post" action="<?php echo base_url()?>master/store_ms_warta">
              <input type="hidden" name="id" value="<?php echo (!empty($data)) ? $data->id:'';?>">
              <div class="form-group">
                <label>Title</label>
                <input type="text" class="form-control" id="judul" name="judul" placeholder="Title" value="<?php echo (!empty($data)) ? $data->judul:'';?>" required="">
              </div>
              <div class="form-group">
                <label>Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" placeholder="Deskripsi" rows="5" required=""><?php echo (!empty($data)) ? $data->deskripsi:'';?></textarea>
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