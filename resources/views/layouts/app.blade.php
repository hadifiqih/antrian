<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="icon" href="{{ asset('adminlte') }}/dist/img/antree-logo.png" type="image/png" sizes="16x16">
  <title>Software Antree | Kassab Syariah</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/fontawesome-free/css/all.min.css">
  <!-- IonIcons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('adminlte') }}/dist/css/adminlte.min.css">
  
  <link href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css" rel="stylesheet">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <script src="https://js.pusher.com/beams/1.0/push-notifications-cdn.js"></script>

  @vite(['resources/js/app.js', 'resources/css/app.css'])

  @yield('style')
  {{-- Pusher --}}
  <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js')
          .then(registration => {
            console.log('SW registered: ', registration);
          })
          .catch(registrationError => {
            console.log('SW registration failed: ', registrationError);
          });
      });
    }
  </script>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
            @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                <span class="badge badge-danger navbar-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
            @endif
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          @foreach (auth()->user()->unreadNotifications->take(6) as $notification)
          <a href="{{ route('notification.markAsRead', $notification->id) }}" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="{{ asset('adminlte') }}/dist/img/antree-150x150.png" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                    {{ $notification->data['title'] }}
                    <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">{{ $notification->data['message'] }}</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i>
                    {{ $notification->created_at->diffForHumans() }}
                </p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          @endforeach
          @foreach (auth()->user()->readNotifications->take(3) as $notification)
            <a href="#" class="dropdown-item">
                <!-- Message Start -->
                <div class="media">
                <img src="{{ asset('adminlte') }}/dist/img/antree-150x150.png" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                <div class="media-body">
                    <h3 class="dropdown-item-title">
                        {{ $notification->data['title'] }}
                    </h3>
                    <p class="text-sm">{{ $notification->data['message'] }}</p>
                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i>
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>
                </div>
                <!-- Message End -->
            </a>
            <div class="dropdown-divider"></div>
          @endforeach
          @if(auth()->user()->notifications->count() == 0)
            <a href="#" class="dropdown-item text-sm text-center"> Tidak ada notifikasi </a>
            <div class="dropdown-divider"></div>
          @endif
          <a href="{{ route('notification.markAllAsRead') }}" class="dropdown-item dropdown-footer {{ auth()->user()->unreadNotifications->count() > 0 ? "" : "disabled" }}">Tandai sudah dibaca ({{ auth()->user()->unreadNotifications->count() }})</a>
        </div>
      </li>
    </ul>
  </nav>
  <div class="container-fluid" id="pengumuman">
    {{-- Membuat Pengumuman Berjalan menggunakan marquee --}}
    <marquee behavior="scroll" direction="left" class="text-secondary">
        <strong>Pengumuman!</strong> --- Libur Hari Raya dimulai pada tanggal <strong>7 April - 15 April</strong>, masuk kembali pada 16 April --- Selamat Lebaran ! 🎂🥳🎉🎊 ---
    </marquee>
  </div>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <div class="brand-link">
      <img src="{{ asset('adminlte') }}/dist/img/antree-logo.png" alt="AdminLTE Logo" class="brand-image elevation-3" style="opacity: .8" width="30">
      <span class="brand-text font-weight-light">Antree</span>
      <span id="iconLogout" class="float-right" onclick="confirmLogout()"><i class="fas fa-sign-out-alt text-danger"></i></span>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ !isset(Auth::user()->employee->photo) || Auth::user()->employee->photo == null ? asset('adminlte/dist/img/user-kosong.png') :  asset('storage/profile/'. Auth::user()->employee->photo)  }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="{{ route('employee.show', Auth::user()->id) }}" class="d-block">{{ Auth::user()->name }}</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search" id="searchSide" name="searchSide">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                @if(Auth::user()->role_id == 11)
                    @include('layouts.partials.menu-sales')
                @elseif(Auth::user()->role_id == 12 || Auth::user()->role_id == 20)
                    @include('layouts.partials.menu-marol')
                @elseif(Auth::user()->role_id == 15)
                    @include('layouts.partials.menu-admin-workshop')
                @elseif(Auth::user()->role_id == 19)
                    @include('layouts.partials.menu-admin')
                @elseif(Auth::user()->role_id == 16 || Auth::user()->role_id == 17)
                    @include('layouts.partials.menu-desainer')
                @elseif(Auth::user()->role_id == 13)
                    @include('layouts.partials.menu-produksi')
                @elseif(Auth::user()->role_id == 21)
                    @include('layouts.partials.menu-dokumentasi')
                @elseif(Auth::user()->role_id == 10)
                    @include('layouts.partials.menu-estimator')
                @elseif(Auth::user()->role_id == 5)
                    @include('layouts.partials.menu-spv-desain')
                @elseif(Auth::user()->role_id == 14)
                    @include('layouts.partials.menu-gudang')
                @else
                    <li class="nav-item">
                        <a href="{{ route('antrian.index') }}" class="nav-link {{ request()->routeIs('antrian.index') || request()->routeIs('antrian.edit') || request()->routeIs('antrian.show') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Antrian Workshop</p>
                        </a>
                    </li>
                @endif
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>@yield('page')</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">@yield('page')</a></li>
              <li class="breadcrumb-item active">@yield('breadcrumb')</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      @yield('content')
    </section>
  </div>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2023 <a href="#">by Kassab Syariah</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Tanggal : </b> {{ date('d F Y - H:i') }}
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="{{ asset('adminlte') }}/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="{{ asset('adminlte') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('adminlte') }}/plugins/sweetalert2/sweetalert2.min.js"></script>

<script src="{{ asset('adminlte') }}/plugins/jszip/jszip.min.js"></script>
<script src="{{ asset('adminlte') }}/plugins/pdfmake/pdfmake.min.js"></script>
<script src="{{ asset('adminlte') }}/plugins/pdfmake/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>

@yield('script')
{{-- Select2 --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

{{-- BS-Custom-Input-File --}}
<script src="{{ asset('adminlte') }}/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>

<!-- AdminLTE -->
<script src="{{ asset('adminlte') }}/dist/js/adminlte.js"></script>

{{-- DayJS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.7/dayjs.min.js"></script>

<!-- PusherJS -->
<script src="https://js.pusher.com/beams/1.0/push-notifications-cdn.js"></script>

<script>
  function notif(data) {
    if(data.message.title == 'Antrian Workshop') {
        $(document).Toasts('create', {
        class: 'bg-warning',
        body: data.message.body,
        title: data.message.title,
        icon: 'fas fa-envelope fa-lg',
        });
    }else if(data.message.title == 'Antrian Desain') {
        $(document).Toasts('create', {
        class: 'bg-info',
        body: data.message.body,
        title: data.message.title,
        icon: 'fas fa-envelope fa-lg',
        });
    }
  }

  function confirmLogout(){
    const confirmation = confirm('Apakah Anda yakin ingin keluar?');
    if (confirmation) {
      const beamsClient = new PusherPushNotifications.Client({
        instanceId: '0958376f-0b36-4f59-adae-c1e55ff3b848',
      });

      beamsClient.stop()
          .then(() => {
              console.log('Beams client stopped.');
              // Lanjutkan dengan logika logout Anda, misalnya menghapus sesi pengguna
              localStorage.removeItem('beamsInitialized');
          })
          .catch(console.error);
      
    }
    //jika beamsClient.stop() berhasil, maka akan dilanjutkan dengan logout
    window.location.href = "{{ route('auth.logout') }}";
  }

  function sendReminder() {
      $.ajax({
          type: "GET",
          url: "{{ route('antrian.reminder') }}",
          success: function (response) {
              console.log(response);
          }
      })
  }

  //print function
  function printData() {
    var config = qz.configs.create("POS-80");               // Exact printer name from OS
    var data = ['Halo kak Sandya !'];   // Raw commands (ZPL provided)

    qz.print(config, data).then(function() {
      alert("Sent data to printer");
    });
  }

  //FIND PRINTER
  function findPrinter() {
    qz.printers.find().then((printers) => {
      console.log(printers);
    }).catch((err) => {
      console.error(err);
    });
  }

  function setDeviceInterests(beamsClient, roleId) {
    let interests = ['hello'];

    switch (roleId) {
        case 11:
            interests.push('sales');
            break;
        case 15:
            interests.push('admin');
            break;
        case 13:
            interests.push('operator');
            break;
        case 5:
        case 20:
            interests.push('supervisor');
            break;
        case 10:
            interests.push('operator');
            break;
        case 16:
            interests.push('desain');
            break;
        default:
            break;
    }

    console.log('Setting device interests:', interests);
    beamsClient.setDeviceInterests(interests)
        .then(() => console.log('Device interests set successfully:', interests))
        .catch(error => console.error('Error setting device interests:', error));
  }

  $(document).ready(function () {
      bsCustomFileInput.init();

      if (!localStorage.getItem('beamsInitialized')) {
      const beamsClient = new PusherPushNotifications.Client({
          instanceId: '0958376f-0b36-4f59-adae-c1e55ff3b848',
      });

      const tokenProvider = new PusherPushNotifications.TokenProvider({
          url: "{{ route('beams.auth') }}"
      });

      beamsClient.start()
          .then(() => {
              console.log('Successfully registered with Beams!');
              return beamsClient.setUserId('user-{{ Auth::user()->id }}', tokenProvider);
          })
          .then(() => beamsClient.getUserId())
          .then(userId => {
              console.log('User ID set:', userId);
              const roleId = {{ Auth::user()->role_id }};
              console.log('User role ID:', roleId);
              setDeviceInterests(beamsClient, roleId);
              
              // Mark initialization in localStorage
              localStorage.setItem('beamsInitialized', 'true');
          })
          .then(() => beamsClient.getDeviceInterests())
          .then(interests => console.log('Successfully registered and subscribed!', interests))
          .catch(console.error);
      }
  });
</script>
{{-- <script>
  const beamsClient = new PusherPushNotifications.Client({
    instanceId: '0958376f-0b36-4f59-adae-c1e55ff3b848',
  });

  const tokenProvider = new PusherPushNotifications.TokenProvider({
    url: "{{ route('beams.auth') }}"
  });

  //stop the SDK from automatically connecting to Beams
  beamsClient.stop();

  beamsClient.start()
  .then(() => beamsClient.clearAllState()) // clear state on start
  .then(() => console.log('Successfully registered and subscribed to Beams!'))
  .then(() => beamsClient.setUserId('user-{{ Auth::user()->id }}', tokenProvider))
  .then(() => beamsClient.getUserId())
  .then(userId => console.log('Successfully registered and subscribed!', userId))
  .then(() =>

  @if(Auth::user()->role_id == 11)
  beamsClient.setDeviceInterests(['hello' , 'sales'])
  @elseif(Auth::user()->role_id == 15)
  beamsClient.setDeviceInterests(['hello' , 'admin'])
  @elseif(Auth::user()->role_id == 13)
  beamsClient.setDeviceInterests(['hello' , 'operator'])
  @elseif(Auth::user()->role_id == 5 || Auth::user()->role_id == 20)
  beamsClient.setDeviceInterests(['hello' , 'supervisor'])
  @elseif(Auth::user()->role_id == 10)
  beamsClient.setDeviceInterests(['hello', 'operator'])
  @elseif(Auth::user()->role_id == 16)
  beamsClient.setDeviceInterests(['hello' , 'desain'])
  @else
  beamsClient.setDeviceInterests(['hello'])
  @endif
  )
  .then(() => beamsClient.getDeviceInterests())
  .then(interests => console.log('Successfully registered and subscribed!', interests))
  .catch(console.error);
</script> --}}
</body>
</html>
