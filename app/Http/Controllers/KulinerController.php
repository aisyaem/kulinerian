<?php

namespace App\Http\Controllers;

use App\Kuliner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class KulinerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Halaman Utama
        $kuliners = Kuliner::all();
        return view('home', compact('kuliners'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //Menambahkan kuliner baru
         if($request->session()->has('login')){
            return view('tambah');
        }
        return redirect('/admin/login') ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file = $request->file('gambar');
        $nama_file = time()."_".$file->getClientOriginalName();
        $folder_tujuan = 'img';
        $file->move($folder_tujuan, $nama_file);

        Kuliner::create([
                 'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'kategori' => $request->kategori,
                'bahan_utama' => $request->bahan_utama,
                'asal' => $request->asal,
                'alat_bahan' => $request->alat_bahan,
                'cara_masak' => $request->cara_masak,
                'nama_tempat' => $request->nama_tempat,
                'url_tempat' => $request->url_tempat,
                'gambar' => $nama_file
        ]);

        return redirect('/admin') -> with('status','Kuliner "'.$request->nama.'" berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kuliner  $kuliner
     * @return \Illuminate\Http\Response
     */
    public function show(Kuliner $kuliner)
    {
        //Menampilkan data kuliner pada halaman kuliner
        return view('kuliner', compact('kuliner'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kuliner  $kuliner
     * @return \Illuminate\Http\Response
     */
    public function edit(Kuliner $kuliner, Request $request)
    {
         if($request->session()->has('login')){
            return view('edit', compact('kuliner'));
        }
         return redirect('/admin/login');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kuliner  $kuliner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kuliner $kuliner)
    {
        $nama_file = $kuliner->gambar;
        if($request->hasFile('gambar')){
            $nama_file = time()."_".$request->file('gambar')->getClientOriginalName();
            $folder_tujuan = 'img';
            $request->file('gambar')->move($folder_tujuan, $nama_file);
            $image_path = "img/".$kuliner->gambar;
            if(File::exists($image_path))
                File::delete($image_path);
        }

        Kuliner::where('id', $kuliner->id)
            ->update([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'kategori' => $request->kategori,
                'bahan_utama' => $request->bahan_utama,
                'asal' => $request->asal,
                'alat_bahan' => $request->alat_bahan,
                'cara_masak' => $request->cara_masak,
                'nama_tempat' => $request->nama_tempat,
                'url_tempat' => $request->url_tempat,
                'gambar' => $nama_file
            ]);

            return redirect('/admin') -> with('status','Kuliner berhasil diedit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kuliner  $kuliner
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kuliner $kuliner)
    {
        $image_path = "img/".$kuliner->gambar;
        if(File::exists($image_path))
            File::delete($image_path);

        Kuliner::destroy($kuliner->id);
        return redirect('/admin') -> with('status','Kuliner "'.$kuliner->nama.'" berhasil dihapus');
    }

    public function cari($keyword){
        //Qurey DB untuk mencari sesuai keyword dan langsung di return ke view
        return view('cari', compact('keyword'));
    }

    public function kategori(){
        $kuliners = Kuliner::all();
        return view('kategori', compact('kuliners'));
    }

    public function admin(Request $request){
        if($request->session()->has('login')){
            $kuliners = Kuliner::paginate(12);
            return view('admin', compact('kuliners'));
        }
         return redirect('/admin/login');
    }
}
