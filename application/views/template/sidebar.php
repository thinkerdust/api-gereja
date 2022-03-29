
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-info elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo base_url();?>" class="brand-link text-center">
      <span class="brand-text font-weight-light">GBT Kristus Alfa Omega</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo base_url();?>assets/dist/img/user.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $this->session->userdata('username');?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item <?php echo ($sidebar == 'jemaat' || $sidebar == 'renungan' || $sidebar == 'berita') ? 'menu-open' : '';?>">
            <a href="#" class="nav-link <?php echo ($sidebar == 'jemaat' || $sidebar == 'renungan' || $sidebar == 'berita') ? 'active' : '';?>">
              <i class="nav-icon fas fa-server"></i>
              <p>
                Master
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo base_url()?>master/jemaat" class="nav-link <?php echo ($sidebar == 'jemaat') ? 'active' : '';?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Data Jemaat</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo base_url()?>master/renungan" class="nav-link <?php echo ($sidebar == 'renungan') ? 'active' : '';?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Renungan</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo base_url()?>master/berita" class="nav-link <?php echo ($sidebar == 'berita') ? 'active' : '';?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Berita</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>