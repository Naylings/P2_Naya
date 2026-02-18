@php
function formatContact($raw) {
    if (!$raw) return '';
    $digits = preg_replace('/\D/', '', $raw);
    if (strlen($digits) < 8) return $raw;
    return '(' . substr($digits,0,3) . ') ' .
           substr($digits,3,3) . '-' .
           substr($digits,6);
}
@endphp


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: "Times New Roman", Times, serif;
      font-size: 12pt;
      color: #000;
      padding: 2cm 2.5cm;
    }

    /* ====== KOP SURAT ====== */
    .kop-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
    }
    
    
    .kop-side {
      width: 90px; /* HARUS sama dengan lebar logo */
    }
    
    .kop-table td {
      vertical-align: middle;
    }
    
    .kop-logo-cell {
      width: 90px;
    }
    
    .kop-logo {
      width: 70px;
      height: 70px;
    }
    
    .kop-text {
      text-align: center;
    }
    
    .kop-line-sm {
      font-size: 11pt;
      letter-spacing: 1px;
      line-height: 1.4;
    }
    
    .kop-center {
      text-align: center;
    }
    
    .kop-line-lg {
      font-size: 18pt;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 2px;
      line-height: 1.4;
    }
    
    .kop-line-xs {
      font-size: 9pt;
      margin-top: 2px;
      line-height: 1.4;
    }

    .kop-side {
      width: 90px; /* HARUS sama dengan lebar logo */
    }

    /* ====== JUDUL ====== */
    .surat-title {
      text-align: center;
      margin-bottom: 16px;
    }
    .surat-title-text {
      font-size: 11pt;
      font-weight: bold;
      text-transform: uppercase;
      text-decoration: underline;
      letter-spacing: 2px;
    }
    .surat-no {
      font-size: 11pt;
      margin-top: 4px;
    }

    /* ====== PEMBUKA ====== */
    .pembuka {
      margin-top: 16px;
      line-height: 1.8;
      text-align: justify;
    }

    /* ====== TABEL DATA ====== */
    .data-table {
      width: 100%;
      margin: 16px 0;
      border-collapse: collapse;
      line-height: 1.8;
      table-layout: auto;
    }
    .data-table td {
      vertical-align: top;
      text-align: left;
      padding: 2px 4px;
    }
    .data-table td:first-child {
      white-space: nowrap;
      width: 1%;
    }
    .data-table td:nth-child(2) {
      width: 1%;
      white-space: nowrap;
      text-align: center;
      padding: 2px 6px;
    }
    .data-table td:last-child {
      width: auto;
    }

    /* ====== ISI ====== */
    .surat-isi {
      line-height: 1.8;
      text-align: justify;
    }
    .surat-isi p {
      margin-bottom: 12px;
    }

    /* ====== TTD ====== */
    .ttd-area {
      margin-top: 60px;
      text-align: right;
    }
    
    .ttd-block {
      display: inline-block;
      text-align: center;
      width: 250px;
      line-height: 1.8;
    }
    
    .ttd-space {
      height: 80px;
    }
    
    .ttd-name {
      font-weight: bold;
      margin-top: 4px;
    }
    
    .ttd-nip {
      font-size: 10pt;
    }

  </style>
</head>
<body>

  {{-- ====== KOP SURAT ====== --}}
  <table class="kop-table">
    <tr>
      {{-- KIRI (LOGO) --}}
      <td class="kop-side">
        @if (!empty($config->logo_base64))
          <img src="{{ $config->logo_base64 }}" class="kop-logo">
        @elseif (!empty($config->logo))
          <img src="{{ public_path($config->logo) }}" class="kop-logo">
        @endif
      </td>
  
      {{-- TENGAH (TEKS) --}}
      <td class="kop-center">
        <div class="kop-line-sm">
          PEMERINTAH KOTA {{ strtoupper($config->city ?? '') }}
        </div>
        <div class="kop-line-sm">
          KECAMATAN {{ strtoupper($config->district ?? '') }}
        </div>
        <div class="kop-line-lg">
          KELURAHAN {{ strtoupper($config->name ?? '') }}
        </div>
        <div class="kop-line-xs">
          Alamat : {{ $config->address ?? '' }}
          Telp. {{ formatContact($config->contact ?? '') }}
          {{ $config->city ?? '' }} {{ $config->pos_code ?? '' }}
        </div>
      </td>
  
      {{-- KANAN (SPACER, WAJIB ADA) --}}
      <td class="kop-side"></td>
    </tr>
  </table>
  
  <hr class="kop-divider">



  {{-- ====== JUDUL ====== --}}
  <div class="surat-title">
    <div class="surat-title-text">SURAT KETERANGAN TIDAK MAMPU</div>
    <div class="surat-no">Nomor: {{ $letter->no_surat ?? '' }}</div>
  </div>

  {{-- ====== PEMBUKA ====== --}}
  <p class="pembuka">
    Yang bertanda tangan di bawah ini, Lurah <b>{{ $config->name ?? '' }}</b>,
    Kecamatan <b>{{ $config->district ?? '' }}</b>,
    Kota <b>{{ $config->city ?? '' }}</b>,
    Provinsi <b>{{ $config->province ?? '' }}</b>, menerangkan bahwa:
  </p>

  {{-- ====== DATA PEMOHON ====== --}}
  <table class="data-table">
    <tr>
      <td>Nama Lengkap</td>
      <td>:</td>
      <td>{{ $letter->nama_pemohon ?? '—' }}</td>
    </tr>
    <tr>
      <td>NIK</td>
      <td>:</td>
      <td>{{ $letter->nik ?? '—' }}</td>
    </tr>
    <tr>
      <td>Tempat / Tanggal Lahir</td>
      <td>:</td>
      <td>
        @if (!empty($letter->tempat_lahir) && !empty($letter->tgl_lahir))
          {{ $letter->tempat_lahir }}, {{ $letter->tgl_lahir }}
        @else
          {{ $letter->tempat_lahir ?? $letter->tgl_lahir ?? '—' }}
        @endif
      </td>
    </tr>
    <tr>
      <td>Jenis Kelamin</td>
      <td>:</td>
      <td>{{ $letter->jenis_kelamin ?? '—' }}</td>
    </tr>
    <tr>
      <td>Agama</td>
      <td>:</td>
      <td>{{ $letter->agama ?? '—' }}</td>
    </tr>
    <tr>
      <td>Status Pernikahan</td>
      <td>:</td>
      <td>{{ $letter->married_status ?? '—' }}</td>
    </tr>
    <tr>
      <td>Pekerjaan</td>
      <td>:</td>
      <td>{{ $letter->occupation ?? '—' }}</td>
    </tr>
    <tr>
      <td>No. KK</td>
      <td>:</td>
      <td>{{ $letter->no_kk ?? '—' }}</td>
    </tr>
    <tr>
      <td>Alamat</td>
      <td>:</td>
      <td>{{ $letter->alamat ?? '—' }}</td>
    </tr>
    <tr>
      <td>RT / RW</td>
      <td>:</td>
      <td>{{ $letter->rt_rw ?? '—' }}</td>
    </tr>
  </table>

  {{-- ====== ISI SURAT ====== --}}
  <div class="surat-isi">
    <p>
      Adalah benar warga Kelurahan <b>{{ $config->name ?? '' }}</b>
      yang tercatat sebagai warga kurang / tidak mampu di lingkungan kami.
      Surat keterangan ini dibuat untuk keperluan
      <b>{{ $letter->keperluan ?? '' }}</b>.
    </p>
    <p>
      Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.
    </p>
  </div>

  {{-- ====== TTD ====== --}}
  <div class="ttd-area">
    <div class="ttd-block">
      <div>
        {{ $config->city ?? '' }},
        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
      </div>
  
      <div style="margin-top:4px;">
        Lurah {{ $config->name ?? '' }}
      </div>
  
      <div class="ttd-space"></div>
  
      <div class="ttd-name">
        ( {{ $config->lurah_name ?? '. . . . . . . . . . . . . . . . . . . . . . . .' }} )
      </div>
  
      <div class="ttd-nip">
        NIP. {{ $config->lurah_nip ?? '. . . . . . . . . . . . . . . . .' }}
      </div>
    </div>
  </div>


</body>
</html>