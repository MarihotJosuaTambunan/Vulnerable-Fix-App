@extends('layouts.main')

@section('container')
<div class="container-fluid px-4">
    <h3 class="mt-4">Dashboard Karyawan</h3>
    <ol class="breadcrumb mb-4">

    </ol>

    <h1 class="mt-4 mb-3" id="welcome-message">Selamat Datang</h1>

    <div class="row">
        <!-- Card Jabatan -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white py-3 mb-4">
                <div class="card-body">
                    <h6>Jabatan Kamu Saat Ini</h6>
                    <h3 id="jabatan">-</h3>
                </div>
            </div>
        </div>

        <!-- Card Hutang -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white py-3 mb-4">
                <div class="card-body">
                    <h6>Hutang Kamu Saat Ini</h6>
                    <h3 id="hutang-belum-lunas">-</h3>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="/dashboard/karyawan/hutang/{{ $employeeId }}">Lihat Hutang</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <!-- Card Gaji Saat Ini -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white py-3 mb-4">
                <div class="card-body">
                    <h6>Gaji Kamu Saat Ini</h6>
                    <h3 id="gaji-sekarang">-</h3>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="/dashboard/karyawan/gaji/{{ $employeeId }}">Lihat Gaji</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Card Total Gaji Diterima -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white py-3 mb-4">
                <div class="card-body">
                    <h6>Total Gaji Yang Diterima</h6>
                    <h3 id="total-gaji">-</h3>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="/dashboard/karyawan/gaji/{{ $employeeId }}">Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const employeeId = {{ $employeeId }};
        const apiUrl = '{{ $apiUrl }}';
        
        fetch(apiUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.data) {
                    // Update welcome message
                    document.getElementById('welcome-message').textContent = 
                        `Selamat Datang, ${data.data.employee.nama}`;
                    
                    // Update jabatan
                    document.getElementById('jabatan').textContent = 
                        data.data.employee.jabatan;
                    
                    // Update hutang
                    document.getElementById('hutang-belum-lunas').textContent = 
                        formatCurrency(data.data.hutang_belum_lunas);
                    
                    // Update gaji sekarang
                    const gajiSekarang = data.data.employee.gaji_pokok + 
                                        data.data.employee.tunjangan_transport + 
                                        data.data.employee.uang_makan;
                    document.getElementById('gaji-sekarang').textContent = 
                        formatCurrency(gajiSekarang);
                    
                    // Update total gaji
                    document.getElementById('total-gaji').textContent = 
                        formatCurrency(data.data.total_gaji);
                }
            })
            .catch(error => {
                console.error('Error fetching dashboard data:', error);
                // Tampilkan pesan error atau fallback UI
            });
            
        // Fungsi format mata uang
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount || 0);
        }
    });
</script>
@endsection

@endsection