<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\TelurExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class Produk_telurController extends Controller
{
    public function index(Request $r)
    {
        $id_gudang = $r->id_gudang ?? 1;
        $tgl = date('Y-m-d');

        $tanggal = $r->tgl ?? date("Y-m-d", strtotime("-1 day", strtotime($tgl)));

        $cek = DB::selectOne("SELECT a.check FROM stok_telur as a
                WHERE a.tgl = '$tanggal' and a.id_gudang = '1' and
                a.id_kandang != '0'
                group by a.tgl;");
        $cekTransfer = DB::selectOne("SELECT a.check FROM stok_telur as a
                WHERE a.tgl = '$tanggal' and a.id_gudang = '2' and a.pcs != '0'
                group by a.tgl;");
        $cekPenjualanTelur = DB::selectOne("SELECT a.cek FROM invoice_telur as a
                WHERE a.tgl = '$tanggal' and a.lokasi = 'mtd'
                group by a.tgl;");
        $cekPenjualanUmum = DB::selectOne("SELECT a.cek FROM penjualan_agl as a
                WHERE a.tgl = '$tanggal'
                group by a.tgl;");

        $data = [
            'title' => 'Dashboard Telur',
            'produk' => DB::table('telur_produk')->get(),
            'id_gudang' => $id_gudang,
            'tanggal' => $tanggal,
            'cekStokMasuk' => $cek,
            'cekTransfer' => $cekTransfer,
            'cekPenjualanTelur' => $cekPenjualanTelur,
            'cekPenjualanUmum' => $cekPenjualanUmum,
            'kandang' => DB::table('kandang')->get(),
            'gudang' => DB::table('gudang_telur')->get(),
            'penjualan_cek_mtd' => DB::selectOne("SELECT sum(a.total_rp) as ttl_rp FROM invoice_telur as a where a.cek ='Y' and a.lokasi ='mtd';"),
            'penjualan_blmcek_mtd' => DB::selectOne("SELECT sum(a.ttl_rp) as ttl_rp , count(a.no_nota) as jumlah
            FROM 
                (
                    SELECT a.no_nota, sum(a.total_rp) as ttl_rp
                    FROM invoice_telur as a
                    where a.cek ='T' and a.lokasi ='mtd'
                    group by a.no_nota
                ) as a;"),
            'penjualan_umum_mtd' => DB::selectOne("SELECT sum(a.total_rp) as ttl_rp FROM penjualan_agl as a where a.cek ='Y' and a.lokasi ='mtd';"),
            'penjualan_umum_blmcek_mtd' => DB::selectOne("SELECT sum(a.total_rp) as ttl_rp , COUNT(a.urutan) as jumlah
            FROM (
            SELECT a.urutan, sum(a.total_rp) as total_rp
                FROM penjualan_agl as a 
            where a.cek ='T' and a.lokasi ='mtd'
            group by a.urutan
            ) as a;"),
            'penjualan_ayam_mtd' => DB::selectOne("SELECT sum(a.h_satuan * a.qty) as ttl_rp FROM invoice_ayam as a where a.cek ='Y' and a.lokasi ='mtd';"),
            'penjualan_ayam_blmcek_mtd' => DB::selectOne("SELECT sum(a.h_satuan * a.qty) as ttl_rp , COUNT(a.urutan) as jumlah
            FROM (
            SELECT a.urutan, sum(a.h_satuan * a.qty) as total_rp, a.h_satuan,a.qty
                FROM invoice_ayam as a 
            where a.cek ='T' and a.lokasi ='mtd'
            group by a.urutan
            ) as a;"),
            'opname_cek_mtd' => DB::selectOne("SELECT sum(a.total_rp) as ttl_rp FROM invoice_telur as a where a.cek ='Y' and a.lokasi ='opname';"),
            'opname_blmcek_mtd' => DB::selectOne("SELECT sum(a.total_rp) as ttl_rp , count(a.no_nota) as jumlah
            FROM ( SELECT a.no_nota, sum(a.total_rp) as total_rp
                  FROM invoice_telur as a 
                  where a.cek ='T' and a.lokasi ='opname'
                  group by a.no_nota
                ) as a;"),

        ];
        return view('produk_telur.dashboard', $data);
    }
    public function CheckMartadah(Request $r)
    {
        if ($r->cek == 'T') {
            DB::table('stok_telur')->where(['tgl' => $r->tgl, 'id_gudang' => '1'])->where('id_kandang', '!=', '0')->update(['check' => 'Y']);
        } else {
            DB::table('stok_telur')->where(['tgl' => $r->tgl, 'id_gudang' => '1'])->where('id_kandang', '!=', '0')->update(['check' => 'T']);
        }
        return redirect()->route('produk_telur', ['tgl' => $r->tgl])->with('sukses', 'Data berhasil di save');
    }
    public function CheckAlpa(Request $r)
    {
        if ($r->cek == 'T') {
            DB::table('stok_telur')->where([['tgl', $r->tgl],  ['jenis', 'tf']])->update(['check' => 'Y']);
        } else {
            DB::table('stok_telur')->where([['tgl', $r->tgl],  ['jenis', 'tf']])->update(['check' => 'T']);
        }
        return redirect()->route('produk_telur', ['tgl' => $r->tgl])->with('sukses', 'Data berhasil di save');
    }

    public function HistoryMtd(Request $r)
    {
        $today = date("Y-m-d");
        $enamhari = date("Y-m-d", strtotime("-6 days", strtotime($today)));
        if (empty($r->tgl1)) {
            $tgl1 = $enamhari;
            $tgl2 = date('Y-m-d');
        } else {
            $tgl1 = $r->tgl1;
            $tgl2 = $r->tgl2;
        }

        $data = [
            'produk' => DB::table('telur_produk')->get(),
            'gudang' => DB::table('gudang_telur')->get(),
            'invoice' => DB::select("SELECT a.id_kandang, a.tgl, b.nm_kandang
            FROM stok_telur as a 
            left join kandang as b on b.id_kandang = a.id_kandang
            where a.tgl BETWEEN '$tgl1' and '$tgl2' and a.id_gudang='1' and a.nota_transfer in('0',' ')
            group by a.tgl, a.id_kandang"),
            'tgl1' => $tgl1,
            'tgl2' => $tgl2
        ];
        return view('produk_telur.history', $data);
    }

    public function edit_telur_dashboard(Request $r)
    {
        $data = [
            'invoice' => DB::select("SELECT a.id_produk_telur, b.id_stok_telur, a.nm_telur, b.pcs, b.kg
            FROM telur_produk as a 
            left join (
                  SELECT a.*
                  FROM stok_telur as a 
                  where a.id_kandang = '$r->id_kandang' and a.tgl = '$r->tgl'
            ) as b on b.id_telur = a.id_produk_telur"),
            'kandang' => DB::table('kandang')->where('id_kandang', $r->id_kandang)->first(),
            'tgl' => $r->tgl
        ];
        return view('produk_telur.edit_mtd', $data);
    }

    public function export(Request $r)
    {
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;


        $total = DB::selectOne("SELECT count(a.id_kandang) as jumlah
        FROM stok_telur as a 
        where a.tgl BETWEEN '$tgl1' and '$tgl2' and a.id_gudang = '1'
        GROUP by a.id_stok_telur
        ");

        $totalrow = $total->jumlah;

        return Excel::download(new TelurExport($tgl1, $tgl2, $totalrow), 'Telur.xlsx');
    }



    public function HistoryAlpa(Request $r)
    {
        $today = date("Y-m-d");
        $enamhari = date("Y-m-d", strtotime("-6 days", strtotime($today)));
        if (empty($r->tgl1)) {
            $tgl1 = $enamhari;
            $tgl2 = date('Y-m-d');
        } else {
            $tgl1 = $r->tgl1;
            $tgl2 = $r->tgl2;
        }

        $data = [
            'produk' => DB::table('telur_produk')->get(),
            'gudang' => DB::table('gudang_telur')->get(),
            'invoice' => DB::select("SELECT a.id_kandang, a.tgl, b.nm_kandang
            FROM stok_telur as a 
            left join kandang as b on b.id_kandang = a.id_kandang
            where a.tgl BETWEEN '$tgl1' and '$tgl2' and a.id_gudang='2'
            group by a.tgl"),
            'tgl1' => $tgl1,
            'tgl2' => $tgl2
        ];
        return view('produk_telur.history_alpa', $data);
    }
}
