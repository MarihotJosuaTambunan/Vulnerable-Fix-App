@extends('layouts.main')

@section('container')
<div class="container-fluid px-4">
    <h3 class="mt-4">Gaji</h3>

    {{-- Breadcrumb --}}
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="/dashboard/karyawan/{{ auth()->user()->employee_id }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Gaji</li>
        </ol>
    </nav>
    {{-- End Breadcrumb --}}

    {{-- Card --}}
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Daftar Gaji-Gaji Saya
        </div>
        <div class="card-body">
            <div id="salaryTable">
                {{-- Data akan diisi oleh JavaScript --}}
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Memuat data gaji...</p>
                </div>
            </div>
        </div>
    </div>
    {{-- End Card --}}
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('{{ $apiSalaryUrl }}')
            .then(response => {
                if (!response.ok) throw new Error('Gagal memuat data');
                return response.json();
            })
            .then(data => {
                if (data.data && data.data.length > 0) {
                    let tableHTML = `
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jabatan</th>
                                    <th>Gaji Pokok</th>
                                    <th>Tunjangan</th>
                                    <th>Uang Makan</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    data.data.forEach((item, index) => {
                        tableHTML += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${new Date(item.tgl_gajian).toLocaleDateString('id-ID')}</td>
                                <td>${item.jabatan}</td>
                                <td>${formatCurrency(item.gaji_pokok)}</td>
                                <td>${formatCurrency(item.tunjangan_transport)}</td>
                                <td>${formatCurrency(item.uang_makan)}</td>
                                <td>${formatCurrency(item.total_gaji)}</td>
                            </tr>
                        `;
                    });

                    tableHTML += `
                            </tbody>
                        </table>
                        <div class="text-end fw-bold mt-3">
                            Total Keseluruhan: ${formatCurrency(data.total_keseluruhan)}
                        </div>
                    `;

                    document.getElementById('salaryTable').innerHTML = tableHTML;
                } else {
                    document.getElementById('salaryTable').innerHTML = `
                        <div class="alert alert-info">Belum ada data gaji</div>
                    `;
                }
            })
            .catch(error => {
                document.getElementById('salaryTable').innerHTML = `
                    <div class="alert alert-danger">
                        ${error.message}
                    </div>
                `;
            });

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }
    });
</script>
@endsection

@endsection