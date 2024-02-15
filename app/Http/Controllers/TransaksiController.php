<?php

namespace App\Http\Controllers;

use App\Transaksi;
use App\Order;
use App\DetailOrder;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        $data = Transaksi::join('users', 'transactions.user_id', 'users.id')
            ->join('orders', 'transactions.id_order', '=', 'orders.id')
            ->select('transactions.*', 'users.fullname', 'orders.*')
            ->orderBy('transactions.updated_at','desc')
            ->get();
            return view('admin.pages.transaksi.data', ['data'=>$data]);
    }

    public function delete(Request $req)
    {
        $result = Transaksi::find($req->id);

        if ($result->delete() ){
            alert()->success('Data Berhasil Terhapus dari Database.', 'Terhapus!')->autoclose(3000);
            return redirect()->route('admin.transaksi');
        }
    }
    public function kitchen(Request $req)
    {
        $orders = DB::table('order_details')
                    ->join('masakan', 'order_details.id_masakan', '=', 'masakan.id')
                    ->join('orders', 'order_details.id_order', '=', 'orders.id')
                    ->select('order_details.*', 'masakan.harga', 'masakan.gambar', 'masakan.nama_masakan','orders.kode_order')
                    ->whereIn('status', ['Pending','Di Proses'])
                    ->orderBy('updated_at','desc')
                    ->get();

        return view('admin.pages.transaksi.kitchen.data', compact('orders'));
    }
    public function proses(Request $req, $id, $tipe)
    {
        $orders = DetailOrder::where('id', $id)->first();
        if ($tipe == 'proses') {
            $orders->update(['status' => 'Di Proses']);
        }elseif ($tipe == 'selesai') {
            $orders->update(['status' => 'Selesai']);
        }
        
        alert()->success('Status Makanan Berhasil di Ubah','Berhasil')->persistent('oke');
        return redirect()->route('kitchen');
    }

    public function kasir(Request $req)
    {
        $orders = Order::where('status_order', 'Menunggu Pembayaran')
                ->orderBy('updated_at', 'desc')
                ->get();

        $od = [];
        foreach ($orders as $order) {
            $orderDetails = DB::table('order_details')
                                ->join('masakan', 'order_details.id_masakan', '=', 'masakan.id')
                                ->select('order_details.*', 'masakan.harga', 'masakan.gambar', 'masakan.nama_masakan')
                                ->where('id_order', $order->id)
                                ->get();
            $od[$order->id] = $orderDetails;
        }

        return view('admin.pages.transaksi.kasir.data', compact('orders','od'));
    }

    public function payment(Request $req, $id)
    {
        $orders =  Order::where('id', $id)->first();
        return view('admin.pages.transaksi.kasir.payment', ['orders'=>$orders]);
    }

    public function bayar(Request $req,$id)
    {      
        $count = DB::table('transactions')->count();
        $blt = date('ym');
        $kode_ord = 'INV' . $blt . str_pad($count + 1, 5, '0', STR_PAD_LEFT);

        $transaksi = new Transaksi;
        $transaksi->kode_transaksi = $kode_ord;
        $transaksi->user_id = $req->user_id;
        $transaksi->id_order = $req->order_id;
        $transaksi->total_bayar = $req->total_bayar;
        $transaksi->kembalian = $req->kembalian;
        $transaksi->tanggal_transaksi = $req->tanggal_transaksi;

        if ($req->kembalian < 0) {
            return back()->with('result','fail');
        } else {
            
            $transaksi->save();
            $orders = Order::where('id', $id)->first();
            $orders->update(['status_order' => 'Pending']);
            alert()->success('Transaksi Telah Berhasil!.','Berhasil')->persistent('oke');
            return redirect()->route('cashier');
        }
    }

    public function getFinish($id_order)
    {
        $orders = Order::where('id_order', $id_order)->first();
        $orders->update(['status_order' => 'Pending']);
        alert()->success('Transaksi Telah Berhasil!.','Berhasil')->persistent('oke');
        return redirect()->route('cashier');
    }
}
