@extends('layouts.main2')
@section('title','Keranjang Anda')
@push('css')
<link rel="stylesheet" href="{{url('polished/js/swal/sweetalert2.min.css')}}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
@endpush
@section('content')

<div class="container" id="cart">
	<h1 align="center"><span class="oi oi-cart"></span> Keranjang</h1>
	
	@if($total)

	
		<div class="row">

			<div class="col-md-9 mx-auto">
				<a href="{{route('cancel')}}" class="btn btn-danger cancel mr-2 mb-2"><span class="oi oi-trash"></span> Batal Memesan</a><a class="btn btn-success mb-2" href="{{route('menu-masakan')}}"><span class="oi oi-arrow-circle-left"></span> Pesan Menu Lagi</a>
				<div class="table-responsive-md">
					<table class="table">
					  <thead class="thead-dark">
					    <tr>
					      <th scope="col">#</th>
					      <th scope="col">Gambar</th>
					      <th scope="col">Nama Masakan</th>
					      <th scope="col">Harga Satuan</th>
					      <th scope="col">Jumlah Pesanan</th>
					      <th scope="col">Keterangan</th>
					      <th scope="col">Subtotal</th>
					      <th scope="col">Aksi</th>
					    </tr>
					  </thead>
					  <tbody>
					  	@foreach($data as $dt)
					    <tr>
					      <th scope="row">{{$loop->iteration}}</th>
					      <td>
					      	<img src="{{url('storage/gambar/'.$dt->gambar)}}" alt="Gambar Masakan" class="img-thumnail" width="50px">
					      </td>
					      <td>{{$dt->nama_masakan}}</td>
					      <td>Rp.{{number_format($dt->harga,0,',','.')}},</td>
					      <td>
					      	<a class="btn btn-danger btn-sm" href="{{route('reducebyone', ['id' => $dt->id])}}"><span class="oi oi-minus"></span></a>
					      	<span class="btn btn-secondary" disabled><b>{{$dt->qty}}</b></span>
					      	<a class="btn btn-success btn-sm" href="{{route('addone', ['id' => $dt->id])}}"><i class="oi oi-plus" aria-hidden="true"></i></a>
					      </td>
					      @if($dt->keterangan)
					      	<td>{{$dt->keterangan}} <button class="btn btn-warning btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#noteedit{{$dt->id}}"><span class="oi oi-pencil"></button></td>
					      @else
					      	<td><button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#note{{$dt->id}}">Tambah Keterangan</button></td>
					      @endif
					      
					      <td>Rp.{{number_format($dt->harga * $dt->qty,0,',','.')}},</td>
					      <td>
							  <a class="btn btn-danger btn-sm hapusdata" href="{{route('remove.items', ['id' => $dt->id])}}"><span class="oi oi-x"></span></a>
					      </td>
					    </tr>
					    @endforeach
					  </tbody>
					</table>
				</div>
				<strong class="float-right" style="color: green; text-transform: uppercase;">Total : Rp.{{number_format($total,0,',','.')}},</strong>
				<br>
				
				<a href="" class="btn btn-success float-right" data-bs-toggle="modal" data-bs-target="#checkout"><span class="oi oi-check"></span> Checkout</a>
			 </div>	
					
		</div>
	@else
		<div class="row">
			<div class="col pt-5 mx-auto">
				<h3 class="text-muted">Tidak Ada Pesanan Dikeranjang :(</h3>
				<br>
				<a href="{{route('menu-masakan')}}" class="btn btn-success">Ke Menu Masakan</a>
			</div>
		</div>
	@endif
<!-- Modal -->
@foreach($data as $dt)
<div class="modal fade" id="note{{$dt->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success-darker">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Keterangan</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{route('note', ['id' => $dt->id])}}" enctype="multipart/form-data">
			@csrf
      	<label>Keterangan</label>
       <textarea name="keterangan" class="form-control"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
        <button type="submit" class="btn btn-success">Simpan</button>
      </div>
      </form>
    </div>
  </div>
</div>
</div>
@endforeach
@foreach($data as $dt)
<div class="modal fade" id="noteedit{{$dt->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success-darker">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Ubah Keterangan</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{route('note', ['id' => $dt->id])}}" enctype="multipart/form-data">
			@csrf
      	<label>Keterangan</label>
       <textarea name="keterangan" class="form-control">{{$dt->keterangan}}</textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
        <button type="submit" class="btn btn-success">Ubah</button>
      </div>
      </form>
    </div>
  </div>
</div>
</div>
@endforeach
<div class="modal fade" id="checkout" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success-darker">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Konfirmasi Checkout</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{route('checkout')}}" enctype="multipart/form-data">
			@csrf
      	<label>Masukan nomer meja anda</label>
       <input type="number" name="nomeja" class="form-control">
       @foreach($data as $d)
       <input type="hidden" name="id_masakan[]" value="{{$d->id_masakan}}">
       <input type="hidden" name="qty[]" value="{{$d->qty}}">
       <input type="hidden" name="keterangan[]" value="{{$d->keterangan}}">
       <input type="hidden" name="subtotal[]" value="{{$d->harga * $d->qty}}">
       @endforeach
       <input type="hidden" name="total" value="{{$total}}">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
        <button type="submit" class="btn btn-success">Simpan</button>
      </div>
      </form>
    </div>
  </div>
</div>
</div>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
<script type="text/javascript">
    $('.cancel').on('click', function (e) {

      e.preventDefault();
      const href = $(this).attr('href');

      Swal.fire({
        title: 'Batal memesan?',
        text: "Apakah anda ingin membersihkan semua data dikeranjang?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya bersihkan!'
      }).then((result) => {
        if (result.value) {
          document.location.href = href;
        }
      })

    });  
    $('.hapusdata').on('click', function (e) {

      e.preventDefault();
      const href = $(this).attr('href');

      Swal.fire({
        title: 'Hapus masakan di dalam cart?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'green',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya hapus!'
      }).then((result) => {
        if (result.value) {
          document.location.href = href;
        }
      })

    });     

</script>
@endpush