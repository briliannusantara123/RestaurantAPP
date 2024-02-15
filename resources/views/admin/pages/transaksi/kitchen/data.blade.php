@extends('admin.main2')
@section('title','Kitchen')

@section('content')

<div class="container">
  <h1>Entri Pesanan Pelanggan</h1>

<table class="table table-bordered" id="datatabled">
  <thead class="border-0">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Kode Order</th>
      <th scope="col">Masakan</th>
      <th scope="col">Jumlah</th>
      <th scope="col">Keterangan</th>
      <th scope="col">Dipesan</th>
      <th scope="col">Aksi</th>
    </tr>
  </thead>
  <tbody>
    @foreach($orders as $order)
    <tr>
      <th scope="row">{{$loop->iteration}}</th>
      <td>{{$order->kode_order}}</td>
      <td>{{$order->nama_masakan}}</td>
      <td>{{$order->qty}}</td>
      <td>{{$order->keterangan}}</td>
      <td>{{\Carbon\Carbon::parse($order->created_at)->diffForHumans()}}</td>
      <td>
        <!-- <form method='post' action="{{route('payment', ['id_order' => $order->id_order] )}}">
          @csrf
          <button class="btn btn-success" type="submit">Bayar</button>
        </form> -->
        @if($order->status == 'Pending')
          <form action="{{ route('proses', ['id' => $order->id, 'tipe' => 'proses']) }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-warning">Proses</button>
          </form>

        @elseif($order->status == 'Di Proses')
          <form action="{{ route('proses', ['id' => $order->id, 'tipe' => 'selesai']) }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-success">Selesai</button>
          </form>
        @else
          <button type="button" class="btn btn-success">{{$order->status}}</button>
        @endif
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
     },60000); // 5000 milliseconds atau 5 seconds.
</script>
@endpush