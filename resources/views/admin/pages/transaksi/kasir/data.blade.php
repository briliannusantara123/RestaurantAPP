@extends('admin.main2')
@section('title','Cashier')

@section('content')

<div class="container">
	<h1>Entri Pesanan Pelanggan</h1>

<table class="table table-bordered" id="datatabled">
  <thead class="border-0">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Kode Order</th>
      <th scope="col">No Meja</th>
      <th scope="col">Dipesan pada</th>
      <th scope="col">Item</th>
      <th scope="col">Aksi</th>
    </tr>
  </thead>
  <tbody>
    @foreach($orders as $order)
    <tr>
      <th scope="row">{{$loop->iteration}}</th>
      <td>{{$order->kode_order}}</td>
      <td>{{$order->no_meja}}</td>
      <td>{{\Carbon\Carbon::parse($order->created_at)->diffForHumans()}}</td>
      <td>
        <table class="table shadow-0">
            <tbody>
              @foreach($od as $orderId => $items)
                  @foreach($items as $item)
                      <tr>
                          <th scope="row">{{$loop->iteration}}</th>
                          <td>{{$item->nama_masakan}}</td>
                          <td>{{$item->qty}}</td>
                          <td>Rp.{{ number_format($item->harga * $item->qty , 0, ',', '.') }}</td>
                      </tr>
                  @endforeach
              @endforeach

            </tbody>
        </table>
      </td>
      <td>
        <!-- <form method='post' action="{{route('payment', ['id_order' => $order->id_order] )}}">
          @csrf
          <button class="btn btn-success" type="submit">Bayar</button>
        </form> -->
        <a class="btn btn-success" href="{{route('payment', ['id_order' => $order->id])}}">Bayar</a>
       <!--  <a href="{{route('payment', ['id_order' => $order->id_order] )}}">Bayar</a> -->
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
	

</div>

@endsection

@push('js')
<script type="text/javascript">
    // Auto Refresh Dashboard
     setTimeout(function(){
         location.reload();
     },7000); // 5000 milliseconds atau 5 seconds.
</script>
@endpush