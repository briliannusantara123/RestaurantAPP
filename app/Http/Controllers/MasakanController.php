<?php

namespace App\Http\Controllers;

use App\Masakan;
use App\Kategori;
use App\Repositories\KategoriRepository;
use App\Repositories\MasakanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class MasakanController extends Controller
{
    private $masakanRepository, $kategoriRepository;

    public function __construct(
        KategoriRepository $kategoriRepository,
        MasakanRepository $masakanRepository
    ) {
        $this->masakanRepository = $masakanRepository;
        $this->kategoriRepository = $kategoriRepository;
    }

    public function daftar(Request $req)
    {
        // $data = Masakan::where('nama_masakan','like',"%{$req->keyword}%")->paginate(10);
        // return view('admin.pages.masakan.daftar', ['data'=>$data]);
        $data = Masakan::join('kategori', 'kategori.id', 'masakan.kategori_id')
            ->where('nama_masakan', 'like', "%{$req->keyword}%")
            ->select('masakan.*', 'nama_kategori')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.pages.masakan.daftar', ['data' => $data]);
    }

    //  public function add()
    // {
    // 	return view('admin.pages.masakan.add');
    // }

    public function save(Request $req)
    {
        $count = $this->masakanRepository->countAll();

        // Buat kode masakan
        $blt = date('ym');
        $kode_mkn = 'MKN' . $blt . str_pad($count + 1, 5, '0', STR_PAD_LEFT);

        // Validasi input
        // $req->validate([
        //     'nama_masakan' => 'required|between:3,100',
        //     'harga' => 'required|numeric',
        //     'gambar' => 'required|image',
        //     'status_masakan' => 'required',
        // ]);

        // Simpan file gambar
        $filename = rand(1, 999) . '_' . str_replace(' ', '', $req->file('gambar')->getClientOriginalName());
        $req->file('gambar')->storeAs('public/gambar', $filename);

        // Simpan data masakan
        $data = [
            'kode_masakan'   => $kode_mkn,
            'kategori_id'    => $req->kategori_id,
            'nama_masakan'   => $req->nama_masakan,
            'gambar'         => $filename,
            'harga'          => $req->harga,
            'status_masakan' => $req->status_masakan,
        ];

        $result = $this->masakanRepository->create($data);

        // Periksa apakah data berhasil disimpan
        if ($result) {
            alert()->success('Data Berhasil Tersimpan ke Database.', 'Tersimpan!')->autoclose(2000);
            return redirect('/admin/masakan');
        } else {
            alert()->info('Harap Periksa lagi data Formulir anda.', 'Tidak Tersimpan!')->autoclose(2000);
        }
    }

    public function edit($id)
    {
        $data = $this->masakanRepository->findById($id);

        return view('admin.pages.masakan.edit', ['rc' => $data]);
    }

    public function update(Request $req)
    {

        Validator::make($req->all(), [
            'nama_masakan' => 'required',
            'harga' => 'numeric',
            'status_masakan' => 'required',
            'gambar' => 'nullable|image',
        ])->validate();

        if (!empty($req->gambar)) {
            $idimg = $this->masakanRepository->findById($req->id);

            $filename = rand(1, 999) . '_' . str_replace(' ', '', $req->gambar->getClientOriginalName());

            if ($req->hasFile('gambar')) {
                $req->file('gambar')->storeAs('/public/gambar', $filename);
                File::delete(storage_path('public/gambar' . $idimg->gambar));
            }

            $field = [
                'nama_masakan' => $req->nama_masakan,
                'kategori_id' => $req->kategori_id,
                'harga' => $req->harga,
                'status_masakan' => $req->status_masakan,
                'gambar' => $filename,
            ];
        } else {
            $field = [
                'nama_masakan' => $req->nama_masakan,
                'kategori_id' => $req->kategori_id,
                'harga' => $req->harga,
                'status_masakan' => $req->status_masakan,
            ];
        }

        $result = $this->masakanRepository->update($field, $req->id);

        if ($result) {
            alert()->success('Berhasil Mengupdate Data.', 'Terupdate!')->autoclose(2000);
            return redirect('/admin/masakan');
        } else {
            alert()->info('Harap Periksa lagi data Formulir anda.', 'Tidak Tersimpan!')->autoclose(2000);
        }
    }


    public function delete(Request $req)
    {
        $result = $this->masakanRepository->findById($req->id);

        if ($result->delete()) {
            alert()->success('Data Berhasil Terhapus dari Database.', 'Terhapus!')->autoclose(3000);
            return redirect('/admin/masakan');
        }
    }


    //READ KATEGORI
    public function daftarKategori(Request $req)
    {
        $data = $this->kategoriRepository->paginateWhere(
            10, 
            fn($q) => $q->where('nama_kategori', 'like', "%{$req->keyword}%"), 
            [], 
            ['updated_at', 'DESC']
        );
        
        return view('admin.pages.masakan.kategori.daftar', ['data' => $data]);
    }

    public function addKategori()
    {
        return view('admin.pages.masakan.kategori.add');
    }

    public function saveKategori(Request $req)
    {
        Validator::make($req->all(), [
            'kategori' => 'required|between:3,100|unique:kategori,nama_kategori',
        ])->validate();

        $result = $this->kategoriRepository->create([
            'nama_kategori' => $req->kategori
        ]);

        if ($result) {
            alert()->success('Data Berhasil Tersimpan ke Database.', 'Tersimpan!')->autoclose(2000);
            return redirect()->route('admin.masakan.kategori');
        } else {
            return back()->with('result', 'fail');
        }
    }

    public function editKategori($id)
    {
        $data = $this->kategoriRepository->findById($id);

        return view('admin.pages.masakan.kategori.edit', ['rc' => $data]);
    }

    public function updateKategori(Request $req)
    {
        Validator::make($req->all(), [
            'nama_kategori' => 'required|between:3,100|unique:kategori,nama_kategori,' . $req->id,
        ])->validate();

        $result = $this->kategoriRepository->update(['nama_kategori' => $req->nama_kategori], $req->id);

        if ($result) {
            alert()->success('Berhasil Mengupdate Data.', 'Terupdate!')->autoclose(2000);
            return redirect()->route('admin.masakan.kategori');
        } else {
            return back()->with('result', 'fail');
        }
    }

    public function deleteKategori(Request $req)
    {
        $result = $this->kategoriRepository->delete($req->id);

        if ($result->delete()) {
            alert()->success('Data Berhasil Terhapus dari Database.', 'Terhapus!')->autoclose(3000);
            return redirect()->route('admin.masakan.kategori');
        }
    }
}
