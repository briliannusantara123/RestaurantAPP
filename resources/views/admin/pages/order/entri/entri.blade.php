
@extends('admin.main2')
@section('title','Waiter')
@push('css')
<link rel="stylesheet" href="{{url('polished/js/swal/sweetalert2.min.css')}}">
@endpush

@section('content')

<div class="container">

  <div class="col-lg-12">
      <h1>Entri Pesanan</h1>
        <div class="table-responsive-md">
          <table class="table table-bordered" id="datatabled">
              <thead class="border-0">
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Kode Order</th>
                  <th scope="col">No Meja</th>
                  <th scope="col">Dipesan pada</th>
                  <th scope="col">Item</th>
                  <th scope="col">Total</th>
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
                            <thead class="border-0">
                                <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">Masakan</th>
                                  <th scope="col">Jumlah</th>
                                  <th scope="col">Keterangan</th>
                                  <th scope="col">Harga</th>
                                  <th scope="col">Status</th>
                                </tr>
                              </thead>
                              <tbody>
                                  @foreach($od as $orderId => $items)
                                        @foreach($items as $item)
                                            <tr>
                                                <th scope="row">{{$loop->iteration}}</th>
                                                <td>{{$item->nama_masakan}}</td>
                                                <td>{{$item->qty}}</td>
                                                <td>{{$item->keterangan}}</td>
                                                <td>Rp.{{ number_format($item->harga, 0, ',', '.') }}</td>
                                                <td>
                                                  @if($item->status == 'Pending')
                                                    <span class='badge badge-danger text-light'>{{$item->status}}</span>

                                                  @elseif($item->status == 'Di Proses')
                                                    <span class='badge badge-warning text-dark'>{{$item->status}}</span>
                                                  @else
                                                    <span class='badge badge-success'>{{$item->status}}</span>
                                                  @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
  
                              </tbody>
                          </table>
                      </td>
                      <td class="text-success-darkest">Rp. {{number_format($order->total, 0, ',', '.')}}</td> <!-- Memperbaiki tanda kurung penutup di sini -->

                      <td>
                          <a class="btn btn-success waiter" href="{{route('entri.accept', ['id'=>$order->id])}}">Antarkan Pesanan</a> <!-- Memperbaiki properti yang digunakan di sini -->
                      </td>
                  </tr>
              @endforeach

              </tbody>
          </table>
        </div>
          
    </div>

</div>

@endsection

@push('js')
<script src="{{url('polished/js/swal/sweetalert2.all.min.js')}}"></script>
<script type="text/javascript">
    $('.waiter').on('click', function (e) {

      e.preventDefault();
      const href = $(this).attr('href');

      Swal.fire({
        title: 'Sudah Diantar?',
        text: "Apakah Pesanan Ini Sudah Diantarkan",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya Sudah!'
      }).then((result) => {
        if (result.value) {
          document.location.href = href;
        }
      })

    });

    // Auto Refresh Dashboard
     setTimeout(function(){
         location.reload();
     },60000); // 5000 milliseconds atau 5 seconds.
</script>
<!-- onclick="return confirm('Sudah Diantar Waiter?')" -->
@endpush