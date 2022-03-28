<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Data Jemaat</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Master Data</a></li>
              <li class="breadcrumb-item active">Data Jemaat</li>
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
            <h3 class="card-title">Form Input Data Jemaat</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <?php echo (isset($alert)) ? $alert:'' ;?>
            <form method="post" action="<?php echo base_url()?>master/store_jemaat">
              <input type="hidden" name="nij" value="<?php echo (!empty($data)) ? $data->nij:'';?>">
              <div class="form-group">
                <label>Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Jemaat" value="<?php echo (!empty($data)) ? $data->nama:'';?>" required="">
              </div>
              <div class="form-group">
                <label>No Telp</label>
                <input type="text" class="form-control" id="no_telp" name="no_telp" placeholder="No Telepon" value="<?php echo (!empty($data)) ? $data->no_telp:'';?>" required="">
              </div>
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo (!empty($data)) ? $data->email:'';?>">
              </div>
              <div class="form-group">
                <label>User Level</label>
                <?php $user_level = isset($data->user_level) ? $data->user_level:0;?>
                <select class="select2 form-control" name="user_level" id="user_level" required="">
                  <option value="" disabled="" selected="">Select Option</option>
                  <option value="1" <?php echo ($user_level == '1') ? 'selected':'';?>>Admin</option>
                  <option value="2" <?php echo ($user_level == '2') ? 'selected':'';?>>Pendeta</option>
                  <option value="3" <?php echo ($user_level == '3') ? 'selected':'';?>>Pelayan</option>
                  <option value="4" <?php echo ($user_level == '4') ? 'selected':'';?>>Jemaat</option>
                </select>
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