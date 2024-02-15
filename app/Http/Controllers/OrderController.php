<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Order;
use App\DetailOrder;
use Auth;
use Alert;

class OrderController extends Controller
{
    public function data(Request $req) {
    	$data = Order::join('users','users.id','orders.id_user')
    		->where('no_meja','like',"%{$req->keyword}%")
            ->select('orders.*', 'fullname')
            ->orderBy('updated_at','desc')
            ->get();
    		return view('admin.pages.order.data', ['data'=>$data]);
    }

    public function add()
    {
    	return view('admin.pages.order.add');
    }

    public function save(Request $req)
    {
        $count = DB::table('orders')->count();
        $blt = date('ym');
        $kode_ord = 'ORD' . $blt . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
        $id_user = Auth::user()->id;

        $result = new Order;
        $result->kode_order = $kode_ord;
        $result->no_meja = $req->no_meja;
        $result->id_user = $id_user;
        $result->keterangan = $req->keterangan;
        $result->status_order = 'pending';

        if ($result->save()) {
            alert()->success('Data Berhasil Disimpan ke Database.','Tersimpan!')->autoclose(4000);
            return redirect()->route('admin.order');
        } else {
           alert()->info('Harap Periksa lagi data Formulir anda.','Tidak Tersimpan!')->autoclose(4000);
        }
        
    }

    public function edit($id_order)
    {
        $data = Order::where('id_order',$id_order)->first();
        return view('admin.pages.order.edit',['rc'=>$data]);
    }

    public function update (Request $req)
    {
        
        $field = [
                'id_order'=>$req->id_order,
                'no_meja'=>$req->no_meja,
                'id_user'=>$req->id_user,
                'keterangan'=>$req->keterangan,
                'status_order'=>$req->status_order,
            ];

        $result = Order::where('id_order',$req->id_order)->update($field);

        if ($result) {
            alert()->success('Berhasil Mengupdate Data.', 'Terupdate!')->autoclose(4000);
            return redirect()->route('admin.order');
        } else {
            alert()->info('Harap Periksa lagi data Formulir anda.','Tidak Tersimpan!')->autoclose(4000);
        }

    }

    public function delete(Request $req)
    {
        $result = Order::find($req->id_order);
        $result->transaksi()->delete();

        if ($result->delete() ){
            alert()->success('Data Berhasil Terhapus dari Database.', 'Terhapus!')->autoclose(3000);
            return redirect()->route('admin.order');
        }
        
    }

    public function entri(Request $req)
    {     
        $orders = Order::where('status_order','Pending')->orderBy('updated_at','desc')->get();
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
        $orders = Order::where('id',$id)->first();
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
