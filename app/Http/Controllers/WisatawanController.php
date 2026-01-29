<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WisatawanController extends Controller
{
    public function index(Request $request) {
    $tahun = (int)$request->get('tahun', 2024);
    
    $data = DB::table('wisatawans')->where('tahun', $tahun)->get();
    $listTahun = [2020, 2021, 2022, 2023, 2024, 2025];
    
    return view('dashboard', compact('data', 'tahun', 'listTahun'));
}

    public function update(Request $request, $id) {
        DB::table('wisatawans')->where('id', $id)->update([
            'nama_negara' => $request->nama_negara,
            'januari' => $request->januari,
            'februari' => $request->februari,
            'maret' => $request->maret,
            'april' => $request->april,
            'mei' => $request->mei,
            'updated_at' => now(),
        ]);
        return back()->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id) {
        DB::table('wisatawans')->where('id', $id)->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}