@extends('layouts.main')

@section('container')
<div class="container-fluid px-4">
    <h3 class="mt-4">My Profile</h3>

    {{-- Breadcrumb --}}
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="/dashboard/karyawan/{{ auth()->user()->employee_id }}">Dashboard</a></li>
            <li class="breadcrumb-item active">My Profile</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-12">
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>{{ $message }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        {{-- Informasi Akun --}}
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Informasi Akun
                </div>
                <div class="card-body" id="accountInfo">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p>Memuat data...</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Biodata Saya --}}
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Biodata Saya
                </div>
                <div class="card-body" id="biodataInfo">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p>Memuat data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('{{ $apiProfileUrl }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal memuat data profile');
                }
                return response.json();
            })
            .then(data => {
                // Isi Informasi Akun
                document.getElementById('accountInfo').innerHTML = `
                    <div class="row">
                        <div class="col-lg-4 fw-bold">Nama</div>
                        <div class="col-lg-8 mb-1">: ${data.data.nama}</div>

                        <div class="col-lg-4 fw-bold">Email</div>
                        <div class="col-lg-8 mb-1">: {{ auth()->user()->email }}</div>

                        <div class="col-lg-4 fw-bold">Role</div>
                        <div class="col-lg-8 mb-1">: {{ auth()->user()->role->role_name }}</div>

                        <div class="text-center mt-1">
                            <hr>
                            <a href="/dashboard/karyawan/profile/edit-account/{{ auth()->user()->employee_id  }}" class="btn btn-sm btn-primary mb-1">Edit Informasi Akun</a>
                        </div>
                    </div>
                `;

                // Isi Biodata Saya
                const employee = data.data;
                document.getElementById('biodataInfo').innerHTML = `
                    <div class="row">
                        <div class="col-lg-4 fw-bold">NIP</div>
                        <div class="col-lg-8 mb-1">: ${employee.nip || '-'}</div>

                        <div class="col-lg-4 fw-bold">Nama</div>
                        <div class="col-lg-8 mb-1">: ${employee.nama}</div>

                        <div class="col-lg-4 fw-bold">Jenis Kelamin</div>
                        <div class="col-lg-8 mb-1">: ${employee.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</div>

                        <div class="col-lg-4 fw-bold">Tempat & Tgl Lahir</div>
                        <div class="col-lg-8 mb-1">: ${employee.tempat_lahir || '-'}, ${formatDate(employee.tgl_lahir)}</div>

                        <div class="col-lg-4 fw-bold">Alamat</div>
                        <div class="col-lg-8 mb-1">: ${employee.alamat || '-'}</div>

                        <div class="col-lg-4 fw-bold">No. Handphone</div>
                        <div class="col-lg-8 mb-1">: ${employee.no_telp || '-'}</div>

                        <div class="col-lg-4 fw-bold">No. Rekening</div>
                        <div class="col-lg-8 mb-1">: ${employee.no_rek || '-'}</div>

                        <div class="col-lg-4 fw-bold">Bank</div>
                        <div class="col-lg-8 mb-1">: ${employee.bank || '-'}</div>

                        <div class="col-lg-4 fw-bold">Tanggal Masuk</div>
                        <div class="col-lg-8 mb-1">: ${formatDate(employee.tgl_masuk)}</div>

                        <div class="col-lg-4 fw-bold">Status Karyawan</div>
                        <div class="col-lg-8 mb-1">: ${getStatusBadge(employee.status)}</div>

                        <div class="text-center mt-1">
                            <hr>
                            <a href="/dashboard/karyawan/profile/edit-biodata/{{ auth()->user()->employee_id  }}" class="btn btn-sm btn-primary mb-1">Edit Biodata Saya</a>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                document.getElementById('accountInfo').innerHTML = `
                    <div class="alert alert-danger">${error.message}</div>
                `;
                document.getElementById('biodataInfo').innerHTML = `
                    <div class="alert alert-danger">${error.message}</div>
                `;
                console.error('Error:', error);
            });

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        }

        function getStatusBadge(status) {
            switch(status) {
                case 1: return '<span class="badge text-bg-primary">Karyawan Kontrak</span>';
                case 2: return '<span class="badge text-bg-success">Karyawan Tetap</span>';
                default: return '<span class="badge text-bg-danger">Tidak Aktif</span>';
            }
        }
    });
</script>
@endsection
@endsection