@extends('layouts.master')

@section('title', 'Hitung EOQ')

@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Hitung EOQ</h3>
      </div>
      <div class="box-body">
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul style="margin:0;">
              @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('eoq.store') }}" class="form-horizontal">
          @csrf

          <div class="form-group">
            <label class="col-sm-4 control-label">Bahan Baku</label>
            <div class="col-sm-6">
              <select name="bahan_baku_id" class="form-control" required>
                <option value="">-- Pilih Bahan Baku --</option>
                @foreach ($bahanBaku as $bb)
                  <option value="{{ $bb->id }}">{{ $bb->nama }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-4 control-label">Permintaan Tahunan (D)</label>
            <div class="col-sm-6">
              <input type="number" name="permintaan_tahunan" class="form-control" min="1" required placeholder="cth: 1200">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-4 control-label">Biaya Pemesanan / Order (S)</label>
            <div class="col-sm-6">
              <input type="number" step="0.01" name="biaya_pemesanan" class="form-control" min="0.01" required placeholder="cth: 50000">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-4 control-label">Biaya Penyimpanan / Unit / Tahun (H)</label>
            <div class="col-sm-6">
              <input type="number" step="0.01" name="biaya_penyimpanan" class="form-control" min="0.01" required placeholder="cth: 1500">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-4 control-label">Lead Time (hari)</label>
            <div class="col-sm-6">
              <input type="number" name="lead_time" class="form-control" min="0" required placeholder="cth: 7">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-4 control-label">Pemakaian Harian (opsional)</label>
            <div class="col-sm-6">
              <input type="number" name="pemakaian_harian" class="form-control" min="0" placeholder="cth: 5 (biarkan kosong jika pakai D/365)">
            </div>
          </div>

          <div class="box-footer">
            <button class="btn btn-primary btn-flat"><i class="fa fa-calculator"></i> Hitung & Simpan</button>
            <a href="{{ route('eoq.index') }}" class="btn btn-warning btn-flat">Kembali</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
