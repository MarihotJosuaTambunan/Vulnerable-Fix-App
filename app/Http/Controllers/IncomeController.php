<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.pemasukan.index', [
            'title'         => 'Data Pemasukan',
            'incomes'       => Income::all(),
            'categories'    => Category::all(),
            'users'         => User::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.pemasukan.tambah', [
            'title'         => 'Form Tambah Data Pemasukan',

            // mengambil data kategori berdasarkan jenis kategori = pemasukan
            'categories'    => Category::where('jenis_kategori', '=', 'Pemasukan')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:1',
            'category_id' => 'required|exists:categories,id',
            'keterangan' => 'required|string|max:255'
        ], [
            'nominal.required' => 'Nominal pemasukan harus diisi.',
            'nominal.numeric' => 'Nominal pemasukan harus berupa angka.',
            'nominal.min' => 'Nominal pemasukan tidak boleh kurang dari Rp 1.',
            'tanggal.required' => 'Tanggal harus diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'category_id.required' => 'Kategori harus dipilih.',
            'keterangan.required' => 'Keterangan harus diisi.',
            'keterangan.max' => 'Keterangan maksimal 255 karakter.'
        ]);

        Income::create($request->all());

        return redirect('/data/pemasukan')->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Income $income)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('dashboard.pemasukan.ubah', [
            'title'         => 'Form Ubah Data Pemasukan',
            'income'        => Income::find($id),
            'categories'    => Category::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:1',
            'category_id' => 'required|exists:categories,id',
            'keterangan' => 'required|string|max:255'
        ], [
            'nominal.required' => 'Nominal pemasukan harus diisi.',
            'nominal.numeric' => 'Nominal pemasukan harus berupa angka.',
            'nominal.min' => 'Nominal pemasukan tidak boleh kurang dari Rp 1.',
            'tanggal.required' => 'Tanggal harus diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'category_id.required' => 'Kategori harus dipilih.',
            'keterangan.required' => 'Keterangan harus diisi.',
            'keterangan.max' => 'Keterangan maksimal 255 karakter.'
        ]);

        $data = [
            'user_id'       => $request->input('user_id'),
            'tanggal'       => $request->input('tanggal'),
            'nominal'       => $request->input('nominal'),
            'category_id'   => $request->input('category_id'),
            'keterangan'    => $request->input('keterangan')
        ];

        Income::where('id', $id)->update($data);
        return redirect('/data/pemasukan')->with('success', 'Data berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Income::destroy($id);
        return redirect('/data/pemasukan')->with('success', 'Data berhasil dihapus!');
    }
}
