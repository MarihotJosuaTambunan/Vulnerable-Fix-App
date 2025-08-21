@extends('layouts.main')

@section('container')

<div class="container-fluid px-4">
    <h2 class="mt-4">Pemasukan - Tambah Data Pemasukan</h2>

    {{-- Breadcrumb --}}
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb mb-4">
            @if (auth()->user()->role_id == 1)
                <li class="breadcrumb-item"><a href="/dashboard/admin">Dashboard</a></li>
            @else
                <li class="breadcrumb-item"><a href="/dashboard/finance_manager">Dashboard</a></li>
            @endif
            <li class="breadcrumb-item"><a href="/data/pemasukan">Data Pemasukan</a></li>
            <li class="breadcrumb-item active">Tambah Data Pemasukan</li>
        </ol>
    </nav>
    {{-- End Breadcrumb --}}

    <div class="row">
        <div class="col-lg-8 col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Tambah Data Pemasukan
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

                    {{-- Form --}}
                    <form action="/data/pemasukan" method="POST">

                        @method('post')
                        @csrf

                        {{-- Mengambil ID User yang sedang login  --}}
                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                        <div class="mb-3 row">
                            <label for="tanggal" class="col-sm-2 col-form-label">Tanggal</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" class="form-control @error('tanggal') is-invalid @enderror" 
                                       id="tanggal" name="tanggal" value="{{ old('tanggal') }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="nominal" class="col-sm-2 col-form-label">Nominal</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control @error('nominal') is-invalid @enderror" 
                                       id="nominal" name="nominal" value="{{ old('nominal') }}" 
                                       min="1" step="1" required>
                                @error('nominal')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="kategori" class="col-sm-2 col-form-label">Kategori</label>
                            <div class="col-sm-10">
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        aria-label="Default select example" id="kategori" name="category_id" required>
                                    <option value="">Pilih kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="keterangan" class="col-sm-2 col-form-label">Keterangan</label>
                            <div class="col-sm-10">
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                          id="keterangan" rows="3" name="keterangan" maxlength="255" required>{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mb-3">Tambah</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


</div>

@endsection