@extends('admin.main2')
@section('title','Pembayaran')
@push('css')
<link rel="stylesheet" href="{{url('polished/js/swal/sweetalert2.min.css')}}">
@endpush
@section('content')

<div class="container-fluid">

  <div class="row">

        <div class=" col-lg-5 mx-auto">

		      <div class="row">
		        <div class="card">

		            <div class="card-header bg-success text-white pb-1">
		                <h5>Tambah Transaksi</h5>
		            </div>

		            <div class="card-body">

		            	@if(session('result') == 'success')
						<div class="alert alert-success" role="alert">
						  <h4 class="alert-heading">Berhasil!</h4>Anda Telah Berhasil Melakukan Transaksi.
						  <a href="{{route('getFinish', ['id' => $orders->id])}}" class="btn btn-success btn-lg">Clear Transaction.</a>
						</div>
						@elseif(session('result') == 'fail')
						<div class="alert alert-danger data-dismissible" role="alert">
						  <h4 class="alert-heading">Uang Anda Kurang!</h4>Ada Kesalahan Saat Menginputkan, Silahkan Di Check Kembali.
						  <button type="button" class="close" data-dismiss="alert">&times;</button>
						</div>
						@endif
					
		            	<b>Tagihan Anda</b> <strong class="text-success-darker">Rp. {{number_format($orders->total),0,',','.'}}</strong> <b>(Silahkan Masukan Nominal Sesuai Tagihan anda)</b>
		            	<input type="hidden" name="" id="txtTagihan" value="{{$orders->total}}">

		              <form id="form-bayar" action="{{route('bayar',['id' => $orders->id])}}" method="POST">
		              	@csrf
		              	<!-- Tagihan -->
		              	<input type="hidden" id="txtTotal" class="form-control" value="{{$orders->total}}">

		              	<!-- Tanggal -->
		              	<input type="hidden" name="tanggal_transaksi" value="{{date('Y-m-d')}}">

		              	<!-- Ambil ID USER -->
		              	<input type="hidden" name="user_id" value="{{$orders->id_user}}">

		              	<!-- Ambil ID ORDER -->
		              	<input type="hidden" name="order_id" value="{{$orders->id}}">

		              	<label>Uang</label>
		                <div class="input-group mb-3">
							<div class="input-group-prepend">
							  <span class="input-group-text bg-success-lighter text-white">Rp.</span>
							</div>
							<input type="number" name="total_bayar" id="txtBayar" class="form-control" placeholder="Uang Masuk">
						</div>
						<label>Kembalian</label>
						<div class="input-group mb-3">
							<div class="input-group-prepend">
							  <span class="input-group-text bg-success-lighter text-white">Rp.</span>
							</div>
							<input type="number" class="form-control txtKembalian" id="txtKembali" placeholder="Uang Kembalian" disabled="">

							<input type="hidden" name="kembalian" class="txtKembalian">
						</div>
						
		                <div class="form-group">
		                  <button id="btnPay" type="submit" class="btn btn-success kasir">Bayar</button>
		                </div>

		              </form>

			        </div>    
				</div>
		      </div>
		</div>
  </div>


	
</div>

@endsection

@push('js')
<script src="{{url('polished/js/swal/sweetalert2.all.min.js')}}"></script>
<script type="text/javascript">
  const bayar = document.getElementById('txtBayar');
  const kembali = document.getElementById('txtKembali');
  const tagihan = document.getElementById('txtTagihan');

  bayar.addEventListener('input', function(event) {
    var nilaiBayar = parseFloat(bayar.value);
    var nilaiTagihan = parseFloat(tagihan.value);
    var kembalian = nilaiBayar - nilaiTagihan;
    kembalian = Math.max(kembalian, 0);
    kembali.value = kembalian;
});
	$('#btnPay').on('click', function (e) {
		e.preventDefault();
      Swal.fire({
        title: 'Yakin?',
        text: "Apakah anda yakin untuk membayar tagihan ini?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya yakin!'
      }).then((result) => {
        if (result.value) {

          var total = $("#txtTotal").val();
				var bayar= $("#txtBayar").val();
				var kembalian = parseInt(bayar) - parseInt(total);
				$(".txtKembalian").val(kembalian);

				$("#form-bayar").submit();

        }
      })

    });

	

	
</script>

@endpush
