<x-theme.app title="{{$title}}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <a href="{{route('export_jurnal',['tgl1'=> $tgl1, 'tgl2'=>$tgl2, 'id_proyek' => $id_proyek])}}"
                    class="float-end btn btn-success"><i class="fas fa-file-excel"></i> Export</a>
                <x-theme.button modal="T" href="{{route('jurnal.add')}}" icon="fa-plus" addClass="float-end"
                    teks="Buat Baru" />
                <x-theme.button modal="Y" idModal="view" icon="fa-filter" addClass="float-end" teks="" />

            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <table class="table table-hover" id="table1">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>Tanggal</th>
                        <th>No Nota</th>
                        <th>Akun</th>
                        <th>Keterangan</th>
                        <th style="text-align: right">Debit</th>
                        <th style="text-align: right">Kredit</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jurnal as $no => $a)
                    <tr>
                        <td>{{$no + 1}}</td>
                        <td>{{date('d-m-Y',strtotime($a->tgl))}}</td>
                        <td>{{$a->no_nota}}</td>
                        <td>{{$a->akun->nm_akun}}</td>
                        <td>{{$a->ket}}</td>
                        <td align="right">{{number_format($a->debit,0)}}</td>
                        <td align="right">{{number_format($a->kredit,0)}}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <span class="btn btn-sm" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v text-primary"></i>
                                </span>
                                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <li><a class="dropdown-item text-primary edit_akun"
                                            href="{{route('edit_jurnal',['no_nota' => $a->no_nota])}}"><i
                                                class="me-2 fas fa-pen"></i>Edit</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item  text-danger delete_nota" no_nota="{{$a->no_nota}}"
                                            href="#" data-bs-toggle="modal" data-bs-target="#delete"><i
                                                class="me-2 fas fa-trash"></i>Delete
                                        </a>
                                    </li>
                                    <li><a class="dropdown-item  text-info" href="#"><i
                                                class="me-2 fas fa-search"></i>Detail</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <form action="" method="get">
            <x-theme.modal title="Filter Jurnal Umum" idModal="view">
                <div class="row">
                    <div class="col-lg-12">

                        <table width="100%" cellpadding="10px">
                            <tr>
                                <td>Tanggal</td>
                                <td>
                                    <label for="">Dari</label>
                                    <input type="date" name="tgl1" class="form-control">
                                </td>
                                <td>
                                    <label for="">Sampai</label>
                                    <input type="date" name="tgl2" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td>Proyek</td>
                                <td colspan="2">
                                    <select name="id_proyek" id="selectView" class="">
                                        <option value="0">All</option>
                                        @foreach ($proyek as $p)
                                        <option value="{{$p->id_proyek}}">{{$p->nm_proyek}}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

            </x-theme.modal>
        </form>

        <form action="{{route('jurnal-delete')}}" method="get">
            <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <h5 class="text-danger ms-4 mt-4"><i class="fas fa-trash"></i> Hapus Data</h5>
                                <p class=" ms-4 mt-4">Apa anda yakin ingin menghapus ?</p>
                                <input type="hidden" class="no_nota" name="no_nota">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>




    </x-slot>
    @section('scripts')
    <script>
        $(document).ready(function () {
            $('.delete_nota').click(function () { 
                var no_nota = $(this).attr('no_nota');
                $('.no_nota').val(no_nota);
                
            });
            $('.selectView').select2({
        dropdownParent: $('#view .modal-content')
    });
        });
    </script>
    @endsection
</x-theme.app>