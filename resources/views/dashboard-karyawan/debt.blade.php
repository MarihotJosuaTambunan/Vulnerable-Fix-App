@extends('layouts.main')

@section('container')
<div class="container-fluid px-4">
    <h3 class="mt-4">Hutang</h3>

    {{-- Breadcrumb --}}
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="/dashboard/karyawan/{{ auth()->user()->employee_id }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Hutang</li>
        </ol>
    </nav>

    {{-- Form Pinjam Hutang (Tetap Form Biasa) --}}
    <div class="row">
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Form Pinjam Hutang 
            </div>
            <div class="card-body">
                {{-- Tampilkan Error Validation --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Tampilkan Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="/dashboard/karyawan/hutang" method="POST">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $selectedEmployee->id }}">
                    
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Nama</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="{{ $selectedEmployee->nama }}" disabled>
                        </div>
                    </div>
    
                        <div class="mb-3 row">
                            <label for="jumlah_hutang" class="col-sm-4 col-form-label">Jumlah Hutang</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control @error('jumlah_hutang') is-invalid @enderror" 
                                       name="jumlah_hutang" 
                                       value="{{ old('jumlah_hutang') }}"
                                       min="1" 
                                       step="1" 
                                       required>
                                @error('jumlah_hutang')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
    
                        <div class="mb-3 row">
                            <label for="alasan" class="col-sm-4 col-form-label">Alasan</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control @error('alasan') is-invalid @enderror" 
                                       name="alasan" 
                                       value="{{ old('alasan') }}"
                                       maxlength="255"
                                       required>
                                @error('alasan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
    
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary mb-3">Ajukan Pinjaman</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Hutang (Menggunakan API) --}}
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Data Hutang-Hutang Saya
        </div>
        <div class="card-body">
            <div id="loading" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Memuat data hutang...</p>
            </div>
            
            <div id="debt-container" style="display: none;">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jumlah Hutang</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Jatuh Tempo</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="debt-list">
                        <!-- Data akan diisi oleh JavaScript -->
                    </tbody>
                </table>
                
                <div class="h6">Total Hutang Yang Pernah Diajukan: <span id="total-hutang">Rp 0</span></div>
                <div class="h6">Total Hutang Belum Lunas: <span id="hutang-belum-lunas">Rp 0</span></div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const apiUrl = '{{ $apiUrl }}';
        
        // Fungsi format mata uang
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount || 0);
        }
        
        // Fungsi format tanggal
        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID');
        }
        
        // Ambil data hutang dari API
        fetch(apiUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal memuat data');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('loading').style.display = 'none';
                document.getElementById('debt-container').style.display = 'block';
                
                // Isi tabel hutang
                const debtList = document.getElementById('debt-list');
                debtList.innerHTML = '';
                
                data.data.forEach((debt, index) => {
                    const row = document.createElement('tr');
                    
                    // Format tanggal jatuh tempo
                    let tglJatuhTempo = '-';
                    if (debt.tgl_jatuh_tempo) {
                        const jatuhTempo = new Date(debt.tgl_jatuh_tempo);
                        const today = new Date();
                        
                        if (jatuhTempo <= today) {
                            tglJatuhTempo = `<span class="text-bg-danger p-1">${formatDate(debt.tgl_jatuh_tempo)}</span>`;
                        } else {
                            tglJatuhTempo = formatDate(debt.tgl_jatuh_tempo);
                        }
                    }
                    
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${debt.nama}</td>
                        <td>${formatCurrency(debt.jumlah_hutang)}</td>
                        <td>${formatDate(debt.tgl_pinjam)}</td>
                        <td>${tglJatuhTempo}</td>
                        <td>${debt.alasan || '-'}</td>
                        <td>${debt.status_label}</td>
                        <td>${debt.keterangan_label}</td>
                    `;
                    
                    debtList.appendChild(row);
                });
                
                // Update total hutang
                document.getElementById('total-hutang').textContent = formatCurrency(data.total_hutang);
                document.getElementById('hutang-belum-lunas').textContent = formatCurrency(data.hutang_belum_lunas);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('loading').innerHTML = '<div class="alert alert-danger">Gagal memuat data hutang</div>';
            });
    });
</script>
@endsection
@endsection