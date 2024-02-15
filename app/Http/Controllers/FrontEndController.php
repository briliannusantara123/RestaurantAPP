<?php

namespace App\Http\Controllers;

use App\Masakan;
use App\Cart;
use App\Order;
use App\DetailOrder;
use App\Kategori;
use Auth;
use Alert;
use Illuminate\Support\Facades\DB;
use Session;

use Illuminate\Http\Request;

class FrontEndController extends Controller
{
    public function index()
    {
    	return view('frontend2.menu');
    }

    public function menu(Request $req)
    {   
        $id_user = Auth::user()->id;
        $count = DB::table('cart')
                    ->where('id_user',$id_user)
                    ->count();
    	$data = Masakan::join('kategori','kategori.id','masakan.kategori_id')
            ->orWhere('nama_masakan','like',"%{$req->keyword}%")
            ->orWhere('kategori.id',$req->kategori_id)
            ->select('masakan.*','nama_kategori')
            ->orderBy('updated_at','desc')
            ->paginate(9);
            return view('frontend2.menu', compact('data','count'));
    }

    public function showCategory($id)
    {
        $id_user = Auth::user()->id;
        $count = DB::table('cart')
                    ->where('id_user',$id_user)
                    ->count();
        $data = Masakan::where('kategori_id', $id)
        ->join('kategori','kategori.id','masakan.kategori_id')
        ->select('masakan.*','nama_kategori')
        ->paginate(9);
        return view('frontend2.menu', compact('data','count'));
    }

    public function showItem(Request $req, $id)
    {
        $oldCart = Session::has('cart') ? Session::get('cart') : null;

        $data = Masakan::where('id',$id)->first();
        return view('frontend2.show', ['data'=>$data]);
    }

    public function AddToCart(Request $req, $id)
    {
        $id_user = Auth::user()->id;
        $masakan = Masakan::find($id);
        $cart = Cart::where('id_masakan', $id)->where('id_user', $id_user)->first();

        if ($cart) {
            // Jika item sudah ada di keranjang, tambahkan satu ke jumlahnya dan update
            $cart->qty = $cart->qty + 1;
            $cart->save(); // Update data yang sudah ada di database
        } else {
            // Jika item belum ada di keranjang, tambahkan baru
            $result = new Cart;
            $result->id_user = $id_user;
            $result->id_masakan = $masakan->id;
            $result->qty = 1;
            $result->save(); // Simpan data baru ke database
        }

        
        // Periksa apakah data berhasil disimpan
        
        alert()->success('Berhasil menambahkan masakan ke dalam keranjang belanja', 'Berhasil!')->autoclose(2000);
            
        
        // $data = Masakan::findOrFail($id);
        // $oldCart = Session::has('cart') ? Session::get('cart') : null;
        // $cart = new Cart($oldCart);
        // $cart->add($data, $data->id);

        // $req->session()->put('cart', $cart);
        //return json_encode($req->session()->get('cart'));

        return back()->with('result','success');
        
    }

    public function getRemoveItem($id)
    {
        $cart = Cart::find($id);
        $cart->delete($cart);
        return redirect()->route('shopping.cart')->with('success', 'Item berhasil dihapus dari keranjang belanja.');
    }


    public function getReduceByOne($id)
    {
        $cart = Cart::where('id', $id)->first();
        if ($cart->qty == 1) {
            $cart = Cart::find($id);
            $cart->delete($cart);
        }else{
            $cart->qty = $cart->qty - 1;
            $cart->save();
        }
        return redirect()->route('shopping.cart');
    }

    public function getAddOne($id)
    {
        $cart = Cart::where('id', $id)->first();
        $cart->qty = $cart->qty + 1;
        $cart->save();
        return redirect()->route('shopping.cart');
    }

    public function getCart()
    {
        $id_user = Auth::user()->id;
        $count = DB::table('cart')
                    ->where('id_user',$id_user)
                    ->count();
        $data = DB::table('cart')->join('masakan', 'cart.id_masakan', '=', 'masakan.id')
                ->select('cart.*','masakan.harga','masakan.gambar','masakan.nama_masakan','masakan.id as id_masakan')
                ->where('cart.id_user', $id_user)
                ->get();
        $total = 0;

        foreach ($data as $item) {
            $subtotal = $item->harga * $item->qty;
            $total += $subtotal;
        }

        return view('frontend2.shopping-cart', ['data' => $data,'total' => $total,'count' => $count]);
        //return response()->json(['data' => $cart->items, 'totalPrice'=>$cart->totalPrice]); 
    }

    public function AddNote(Request $request,$id)
    {
        $cart = Cart::where('id', $id)->first();
        $cart->keterangan = $request->keterangan;
        $cart->save();
        alert()->success('Berhasil menambahkan keterangan', 'Berhasil')->autoclose(2000);
        return redirect()->route('shopping.cart');
    }
    public function destroy()
    {
        Session::forget('cart');
        return redirect()->route('menu-masakan')->with('result','clear');
    }

    public function getCheckout()
    {
        $id_user = Auth::user()->id;
        $data = DB::table('cart')->join('masakan', 'cart.id_masakan', '=', 'masakan.id')
                ->select('cart.*','masakan.harga','masakan.gambar','nama_masakan')
                ->where('cart.id_user', $id_user)
                ->get();
        $total = 0;

        foreach ($data as $item) {
            $subtotal = $item->harga * $item->qty;
            $total += $subtotal;
        }
        return view('frontend2.checkout', ['data' => $data, 'total' => $total]);
        //return response()->json(['data'=> $cart->items, 'total' => $total]);
    }

    public function postCheckout(Request $req)
{
    // Buat kode order
    $id_user = Auth::user()->id;
    $cart = Cart::where('id_user',$id_user);
    $cart->delete($cart);

    $count = DB::table('cart')->count();
    $blt = date('ym');
    $kode_ord = 'ORD' . $blt . str_pad($count + 1, 5, '0', STR_PAD_LEFT);

    $order = new Order;
    $order->kode_order = $kode_ord;
    $order->no_meja = $req->nomeja;
    $order->id_user = $id_user;
    $order->total = $req->total;
    $order->status_order = 'Menunggu Pembayaran';
    $order->save();
    $order->refresh();

    $data = [];

for ($i = 0; $i < count($req->id_masakan); $i++) {
    $data[] = [
        'id_order' => $order->id,
        'id_masakan' => $req->id_masakan[$i],
        'qty' => $req->qty[$i],
        'keterangan' => $req->keterangan[$i],
        'subtotal' => $req->subtotal[$i],
        'status' => 'Pending'
    ];
}

DB::table('order_details')->insert($data);


    alert()->success('Silahkan lakukan Pembayaran ke Kasir!.', 'Order Berhasil')->persistent('oke');
    return redirect()->route('thankyou')->with('result','success');
}


    public function thanks()
    {
        $id_user = Auth::user()->id;
        $count = DB::table('cart')
                    ->where('id_user',$id_user)
                    ->count();
        $order = DB::table('orders')
                ->where('id_user', $id_user)
                ->where('status_order','Menunggu Pembayaran')
                ->first();
        $od = DB::table('order_details')
                ->join('masakan', 'order_details.id_masakan', '=', 'masakan.id')
                ->select('order_details.*','masakan.harga','masakan.gambar','masakan.nama_masakan')
                ->where('id_order', $order->id)
                ->get();
        return view('frontend2.thanks', ['order' => $order,'od' => $od,'count' => $count]);
    }

    public function history()
    {
        $id_user = Auth::user()->id;
        $count = DB::table('cart')
                    ->where('id_user',$id_user)
                    ->count();
        $orders = DB::table('orders')
                ->where('id_user', $id_user)
                ->orderBy('updated_at','desc')
                ->get();

        $orderDetails = [];
        foreach ($orders as $o) {
            $od = DB::table('order_details')
                ->join('masakan', 'order_details.id_masakan', '=', 'masakan.id')
                ->select('order_details.*','masakan.harga','masakan.gambar','masakan.nama_masakan')
                ->where('id_order', $o->id)
                ->get();

            // Simpan hasil dari setiap iterasi dalam array
            $orderDetails[$o->id] = $od;
        }
        
        return view('frontend2.history', compact('orders', 'orderDetails','count'));   
    }

}
