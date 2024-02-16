<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Order;
use App\DetailOrder;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function data(Request $req) 
    {
        $data = $this->orderRepository->getWhereAdvanced(
            fn($q) => $q->where('no_meja', 'LIKE', "%{$req->keyword}%"),
            ['updated_at', 'DESC'],
            ['user']
        );

        return view('admin.pages.order.data', compact('data'));
    }

    public function add()
    {
    	return view('admin.pages.order.add');
    }

    public function save(Request $req)
    {
        $count = $this->orderRepository->countAll();

        $blt = date('ym');
        $kode_ord = 'ORD' . $blt . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
        $id_user = Auth::user()->id;

        $result = $this->orderRepository->create([
            'kode_order'   => $kode_ord,
            'no_meja'      => $req->no_meja,
            'id_user'      => $id_user,
            'keterangan'   => $req->keterangan,
            'status_order' => 'pending',
        ]);

        if ($result) {
            alert()->success('Data Berhasil Disimpan ke Database.','Tersimpan!')->autoclose(4000);
            return redirect()->route('admin.order');
        } else {
           alert()->info('Harap Periksa lagi data Formulir anda.','Tidak Tersimpan!')->autoclose(4000);
        }
        
    }

    public function edit($id_order)
    {
        $data = $this->orderRepository->findById($id_order);
        return view('admin.pages.order.edit', ['rc'=>$data]);
    }

    public function update (Request $req)
    {   
        $field = [
            'id_order'     => $req->id_order,
            'no_meja'      => $req->no_meja,
            'id_user'      => $req->id_user,
            'keterangan'   => $req->keterangan,
            'status_order' => $req->status_order,
        ];

        $result = $this->orderRepository->updateWhere($field, ['id_order' => $req->id_order]);

        if ($result) {
            alert()->success('Berhasil Mengupdate Data.', 'Terupdate!')->autoclose(4000);
            return redirect()->route('admin.order');
        } else {
            alert()->info('Harap Periksa lagi data Formulir anda.','Tidak Tersimpan!')->autoclose(4000);
        }

    }

    public function delete(Request $req)
    {
        $result = $this->orderRepository->findById($req->id_order);
        $result->transaksi()->delete();

        if ($result->delete() ){
            alert()->success('Data Berhasil Terhapus dari Database.', 'Terhapus!')->autoclose(3000);
            return redirect()->route('admin.order');
        }
        
    }

    public function entri(Request $req)
    {     
        $orders = $this->orderRepository->getWhereAdvanced(fn($q) => $q->where('status_order', 'Pending'), ['updated_at', 'DESC']);
        
        $od = [];
        foreach ($orders as $order) {
            $orderDetails = DB::table('order_details')
                                ->join('masakan', 'order_details.id_masakan', '=', 'masakan.id')
                                ->select('order_details.*', 'masakan.harga', 'masakan.gambar', 'masakan.nama_masakan')
                                ->where('id_order', $order->id)
                                ->get();

            $od[$order->id] = $orderDetails;
        }

        return view('admin.pages.order.entri.entri', compact('orders','od'));
    }

    public function terimaEntri($id)
    {
        $orders = $this->orderRepository->findById($id);
        $od = DetailOrder::where('id_order',$orders->id)->whereIn('status',['Pending','Di Proses'])->get();

        if (!$od->isEmpty()) {
            alert()->warning('Belum Bisa Mengantarkan Karena Ada Masakan yang Belum Selesai.','Gagal!')->persistent('oke');
        }else{
            $orders->update(['status_order' => 'Beres']);
            alert()->success('Pesanan Berhasil Diantar!.','Berhasil!')->persistent('oke');
        }
        
        return redirect()->route('entri.order');
    }


}
