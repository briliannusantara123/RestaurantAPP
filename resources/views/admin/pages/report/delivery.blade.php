@extends('layouts.report')
@section('title','Your Delivery')
@section('content')
<style type="text/css">
	/* Gaya umum */
.some-element {
    width: 100%;
    padding: 10px;
}

/* Media query untuk layar berukuran kecil (misalnya ponsel) */
@media screen and (max-width: 768px) {
    .some-element {
        width: 50%;
    }
}

</style>

<div class="some-element">
<div class="row contacts">
	    <div class="col invoice-to">
	        <div class="text-gray-light">INVOICE TO:</div>
	        <h2 class="to">{{Auth::user()->fullname}}</h2>
	    </div>
	    <div class="col invoice-details">
	        <h1 class="invoice-id">{{$orders->kode_order}}</h1>
	        <div class="date">Date of Invoice: {{date('d F Y - H:i',strtotime($orders->created_at))}}</div>

	    </div>
	</div>

    <h2>NO MEJA	: {{$orders->no_meja}}</h2>
    <hr>
    <h2>STATUS : {{$orders->status_order}}</h2>
    <br>
	
	
	<div class="notices">
	    <div>NOTICE:</div>
	    <div class="notice">Silahkan Menuju Kasir Dengan Menunjukan Bukti Order Ini.</div>
	</div>
</div>

@endsection
@if($orders->status_order == 'Pending')
<script type="text/javascript">
	// Pindah ke halaman lain setelah 3 detik
setTimeout(function() {
    window.location.href = "{{route('history')}}";
}, 1000); // 3000 milidetik = 3 detik

</script>
@else
<script type="text/javascript">
    // Auto Refresh Dashboard
     setTimeout(function(){
         location.reload();
     },3000);
</script>
@endif
