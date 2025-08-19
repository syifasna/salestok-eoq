<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="" method="post" class="form-horizontal">
            @csrf
            @method('post')

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="nama_produk" class="col-lg-2 col-lg-offset-1 control-label">Nama</label>
                        <div class="col-lg-6">
                            <input type="text" name="nama_produk" id="nama_produk" class="form-control" required
                                autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_kategori" class="col-lg-2 col-lg-offset-1 control-label">Kategori</label>
                        <div class="col-lg-6">
                            <select name="id_kategori" id="id_kategori" class="form-control" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategori as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-lg-offset-1 control-label">Bahan Baku</label>
                        <div class="col-lg-6">
                            <div id="list-bahan">
                                <div class="d-flex align-items-center mb-2">
                                    <select name="bahan_baku[0][id]" class="form-control me-2">
                                        <option value="">-- Pilih Bahan Baku --</option>
                                        @foreach ($bahanBaku as $bb)
                                            <option value="{{ $bb->id }}">{{ $bb->nama }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="bahan_baku[0][jumlah]" class="form-control me-2"
                                        placeholder="Jumlah">
                                    <button type="button" class="btn btn-success btn-sm add-bahan">+</button>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="form-group row">
                        <label for="merk" class="col-lg-2 col-lg-offset-1 control-label">Merk</label>
                        <div class="col-lg-6">
                            <input type="text" name="merk" id="merk" class="form-control">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="harga_beli" class="col-lg-2 col-lg-offset-1 control-label">Harga Beli</label>
                        <div class="col-lg-6">
                            <input type="number" name="harga_beli" id="harga_beli" class="form-control" required>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="harga_jual" class="col-lg-2 col-lg-offset-1 control-label">Harga Jual</label>
                        <div class="col-lg-6">
                            <input type="number" name="harga_jual" id="harga_jual" class="form-control" required>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="diskon" class="col-lg-2 col-lg-offset-1 control-label">Diskon</label>
                        <div class="col-lg-6">
                            <input type="number" name="diskon" id="diskon" class="form-control" value="0">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="stok" class="col-lg-2 col-lg-offset-1 control-label">Stok</label>
                        <div class="col-lg-6">
                            <input type="number" name="stok" id="stok" class="form-control" required
                                value="0">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-flat btn-primary"><i class="fa fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal"><i
                            class="fa fa-arrow-circle-left"></i> Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).on('click', '.add-bahan', function() {
        let index = $('#list-bahan .d-flex').length;
        let html = `
        <div class="d-flex align-items-center mb-2">
            <select name="bahan_baku[${index}][id]" class="form-control me-2">
                <option value="">-- Pilih Bahan Baku --</option>
                @foreach ($bahanBaku as $bb)
                    <option value="{{ $bb->id }}">{{ $bb->nama }}</option>
                @endforeach
            </select>
            <input type="number" name="bahan_baku[${index}][jumlah]" class="form-control me-2" placeholder="Jumlah">
            <button type="button" class="btn btn-danger btn-sm remove-bahan">-</button>
        </div>
        `;
        $('#list-bahan').append(html);
    });

    $(document).on('click', '.remove-bahan', function() {
        $(this).closest('.d-flex').remove();
    });
</script>
