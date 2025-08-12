<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                @php
                    // Simulasi broken access control - simpan role pilihan di session
                    $simulated_role = session('simulated_role', auth()->user()->role_id);
                    
                    // Jika mengakses URL admin, set session ke role admin
                    if(Request::is('dashboard/admin*')) {
                        session(['simulated_role' => 1]);
                        $simulated_role = 1;
                    } 
                    // Jika mengakses URL finance, set session ke role finance
                    elseif(Request::is('dashboard/finance_manager*')) {
                        session(['simulated_role' => 2]);
                        $simulated_role = 2;
                    }
                    // Jika kembali ke URL karyawan, reset ke role asli
                    elseif(Request::is('dashboard/karyawan*')) {
                        session(['simulated_role' => auth()->user()->role_id]);
                        $simulated_role = auth()->user()->role_id;
                    }
                @endphp

                {{-- DASHBOARD --}}
                <div class="ms-3 mt-1 fs-6 fw-bold {{ Request::is('dashboard*') ? 'text-light' : '' }}">DASHBOARD</div>

                {{-- Tampilkan dashboard berdasarkan simulated_role --}}
                @if ($simulated_role == 1)
                    <a class="nav-link {{ Request::is('dashboard/admin*') ? 'active' : '' }}" href="/dashboard/admin/{{ auth()->user()->employee_id }}">
                        <div class="sb-nav-link-icon"><i class="fa-fw fas fa-tachometer-alt"></i></div>
                        Dashboard Admin
                    </a>
                @elseif ($simulated_role == 2)
                    <a class="nav-link {{ Request::is('dashboard/finance_manager*') ? 'active' : '' }}" href="/dashboard/finance_manager/{{ auth()->user()->employee_id }}">
                        <div class="sb-nav-link-icon"><i class="fa-fw fas fa-tachometer-alt"></i></div>
                        Dashboard Finance
                    </a>
                @else
                    <a class="nav-link {{ Request::is('dashboard/karyawan*') ? 'active' : '' }}" href="/dashboard/karyawan/{{ auth()->user()->employee_id }}">
                        <div class="sb-nav-link-icon"><i class="fa-fw fas fa-tachometer-alt"></i></div>
                        Dashboard Karyawan
                    </a>
                @endif

                {{-- TRANSAKSI --}}
                @if ($simulated_role == 1 || $simulated_role == 2)
                    <div class="ms-3 mt-2 fs-6 fw-bold {{ Request::is('data*') ? 'text-light' : '' }}">TRANSAKSI</div>
                    <a class="nav-link {{ Request::is('data/pemasukan*') ? 'active' : '' }}" href="/data/pemasukan">
                        <div class="sb-nav-link-icon text-success"><i class="fa-fw fas fa-arrow-up"></i></div>
                        Pemasukan
                    </a>
                    <a class="nav-link {{ Request::is('data/pengeluaran*') ? 'active' : '' }}" href="/data/pengeluaran">
                        <div class="sb-nav-link-icon text-danger"><i class="fa-fw fas fa-arrow-down"></i></div>
                        Pengeluaran
                    </a>
                @endif

                {{-- MASTER DATA --}}
                @if ($simulated_role == 1 || $simulated_role == 2)
                    <div class="ms-3 mt-2 fs-6 fw-bold {{ Request::is('users*') || Request::is('hutang*') || Request::is('gaji*') || Request::is('karyawan*') ? 'text-light' : '' }}">MASTER DATA</div>

                    @if ($simulated_role == 1)
                        <a class="nav-link {{ Request::is('users*') ? 'active' : '' }}" href="/users">
                            <div class="sb-nav-link-icon"><i class="fa-fw fas fa-user"></i></div>
                            User Management
                        </a>
                    @endif

                    <a class="nav-link {{ Request::is('hutang*') ? 'active' : '' }}" href="/hutang">
                        <div class="sb-nav-link-icon"><i class="fa-fw fas fa-credit-card"></i></div>
                        Hutang
                    </a>

                    @if ($simulated_role == 1)
                        <a class="nav-link {{ Request::is('karyawan*') ? 'active' : '' }}" href="/karyawan">
                            <div class="sb-nav-link-icon"><i class="fa-fw fas fa-users"></i></div>
                            Karyawan
                        </a>
                    @endif

                    <a class="nav-link {{ Request::is('gaji*') ? 'active' : '' }}" href="/gaji-karyawan">
                        <div class="sb-nav-link-icon"><i class="fa-fw fas fa-sack-dollar"></i></div>
                        Penggajian
                    </a>
                @endif

                {{-- REPORT --}}
                @if ($simulated_role == 1 || $simulated_role == 2)
                    <div class="ms-3 mt-2 fs-6 fw-bold {{ Request::is('cetak-laporan*') ? 'text-light' : '' }}">REPORT</div>
                    <a class="nav-link {{ Request::is('cetak-laporan*') ? 'active' : '' }}" href="/cetak-laporan">
                        <div class="sb-nav-link-icon"><i class="fa fa-file"></i></div>
                        Laporan
                    </a>
                    

                    <div class="collapse" id="collapseReports" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="/cetak-laporan/keuangan">Laporan Keuangan</a>
                            <a class="nav-link" href="/cetak-laporan/hutang">Laporan Hutang</a>
                            <a class="nav-link" href="/cetak-laporan/gaji">Laporan Gaji</a>
                        </nav>
                    </div>
                @endif

                {{-- MENU KARYAWAN --}}
                @if ($simulated_role == 3)
                    <div class="ms-3 mt-3 fs-6 fw-bold {{ Request::is('dashboard/karyawan*') ? 'text-light' : '' }}">MENU</div>
                    <a class="nav-link {{ Request::is('dashboard/karyawan/profile*') ? 'active' : '' }}" href="/dashboard/karyawan/profile/{{ auth()->user()->employee_id }}">
                        <div class="sb-nav-link-icon"><i class="fa-fw fa fa-user"></i></div>
                        My Profile
                    </a>
                    <a class="nav-link {{ Request::is('dashboard/karyawan/gaji*') ? 'active' : '' }}" href="/dashboard/karyawan/gaji/{{ auth()->user()->employee_id }}">
                        <div class="sb-nav-link-icon"><i class="fa-fw fas fa-sack-dollar"></i></div>
                        Gaji Saya
                    </a>
                    <a class="nav-link {{ Request::is('dashboard/karyawan/hutang*') ? 'active' : '' }}" href="/dashboard/karyawan/hutang/{{ auth()->user()->employee_id }}">
                        <div class="sb-nav-link-icon"><i class="fa-fw fas fa-credit-card"></i></div>
                        Pinjam Hutang
                    </a>
                    <a class="nav-link {{ Request::is('dashboard/karyawan/change-password*') ? 'active' : '' }}" href="/dashboard/karyawan/change-password/{{ auth()->user()->employee_id }}">
                        <div class="sb-nav-link-icon"><i class="fa-fw fas fa-lock"></i></div>
                        Ganti Password
                    </a>
                @endif
            </div>
        </div>

    </nav>
</div>