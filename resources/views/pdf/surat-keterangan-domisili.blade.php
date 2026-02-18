@php
function formatContactDom($raw) {
    if (!$raw) return '';
    $digits = preg_replace('/\D/', '', $raw);
    if (strlen($digits) < 8) return $raw;
    return '(' . substr($digits,0,3) . ') ' . substr($digits,3,3) . '-' . substr($digits,6);
}
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: "Times New Roman", Times, serif; font-size: 12pt; color: #000; padding: 2cm 2.5cm; }

    /* KOP */
    .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    .kop-side { width: 90px; vertical-align: middle; }
    .kop-center { text-align: center; vertical-align: middle; }
    .kop-logo { width: 75px; height: 75px; display: block; }
    .kop-line-sm { font-size: 11pt; letter-spacing: 1px; line-height: 1.4; }
    .kop-line-lg { font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1.5px; line-height: 1.4; }
    .kop-line-xs { font-size: 9pt; margin-top: 4px; line-height: 1.4; }
    .kop-divider { border: none; border-top: 3px solid #000; margin-bottom: 20px; }

    /* JUDUL */
    .surat-title { text-align: center; margin-bottom: 16px; }
    .surat-title-text { font-size: 11pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; letter-spacing: 2px; }
    .surat-no { font-size: 11pt; margin-top: 4px; }

    /* PEMBUKA */
    .pembuka { margin-top: 16px; line-height: 1.8; text-align: justify; margin-bottom: 4px; }

    /* TABEL DATA */
    .data-table { width: 100%; margin: 12px 0; border-collapse: collapse; line-height: 1.7; }
    .data-table td { vertical-align: top; padding: 2px 4px; }
    .data-table td:first-child { width: 160px; white-space: nowrap; }
    .data-table td:nth-child(2) { width: 10px; text-align: center; white-space: nowrap; }
    .data-table td:last-child { width: auto; }

    /* ISI */
    .surat-isi { line-height: 1.8; text-align: justify; margin-top: 12px; }
    .surat-isi p { margin-bottom: 12px; }

    /* TTD */
    .ttd-area { margin-top: 40px; text-align: right; }
    .ttd-block { display: inline-block; text-align: center; width: 220px; line-height: 1.7; }
    .ttd-space { height: 70px; }
    .ttd-name { font-weight: bold; margin-top: 4px; }
    .ttd-nip { font-size: 10pt; }
  </style>
</head>
<body>

  {{-- KOP --}}
  <table class="kop-table">
    <tr>
      <td class="kop-side">
        @if (!empty($config->logo_base64))
          <img src="{{ $config->logo_base64 }}" class="kop-logo">
        @elseif (!empty($config->logo))
          <img src="{{ $config->logo }}" class="kop-logo">
        @endif
      </td>
      <td class="kop-center">
        <div class="kop-line-sm">PEMERINTAH KOTA {{ strtoupper($config->city ?? '') }}</div>
        <div class="kop-line-sm">KECAMATAN {{ strtoupper($config->district ?? '') }}</div>
        <div class="kop-line-lg">KELURAHAN {{ strtoupper($config->name ?? '') }}</div>
        <div class="kop-line-xs">
          Alamat : {{ $config->address ?? '' }}
          Telp. {{ formatContactDom($config->contact ?? '') }}
          {{ $config->city ?? '' }} {{ $config->pos_code ?? '' }}
        </div>
      </td>
      <td class="kop-side"></td>
    </tr>
  </table>
  <hr class="kop-divider">

  {{-- JUDUL --}}
  <div class="surat-title">
    <div class="surat-title-text">SURAT KETERANGAN DOMISILI</div>
    <div class="surat-no">Nomor: {{ $letter->no_surat ?? '' }}</div>
  </div>

  {{-- PEMBUKA --}}
  <p class="pembuka">
    Yang bertanda tangan di bawah ini, Lurah <b>{{ $config->name ?? '' }}</b>,
    Kecamatan <b>{{ $config->district ?? '' }}</b>,
    Kota <b>{{ $config->city ?? '' }}</b>,
    Provinsi <b>{{ $config->province ?? '' }}</b>, menerangkan bahwa:
  </p>

  {{-- DATA PEMOHON --}}
  <table class="data-table">
    <tr>
      <td>Nama Lengkap</td><td>:</td><td>{{ $letter->nama_pemohon ?? '—' }}</td>
    </tr>
    <tr>
      <td>NIK</td><td>:</td><td>{{ $letter->nik ?? '—' }}</td>
    </tr>
    <tr>
      <td>Tempat / Tanggal Lahir</td><td>:</td>
      <td>
        @if(!empty($letter->tempat_lahir) && !empty($letter->tgl_lahir))
          {{ $letter->tempat_lahir }}, {{ $letter->tgl_lahir }}
        @else
          {{ $letter->tempat_lahir ?? $letter->tgl_lahir ?? '—' }}
        @endif
      </td>
    </tr>
    <tr>
      <td>Jenis Kelamin</td><td>:</td><td>{{ $letter->jenis_kelamin ?? '—' }}</td>
    </tr>
    <tr>
      <td>Agama</td><td>:</td><td>{{ $letter->agama ?? '—' }}</td>
    </tr>
    <tr>
      <td>Warganegara</td><td>:</td><td>{{ $letter->warganegara ?? 'Indonesia' }}</td>
    </tr>
    <tr>
      <td>Status Pernikahan</td><td>:</td><td>{{ $letter->married_status ?? '—' }}</td>
    </tr>
    <tr>
      <td>Pekerjaan</td><td>:</td><td>{{ $letter->occupation ?? '—' }}</td>
    </tr>
  </table>

  {{-- ISI SURAT --}}
  <div class="surat-isi">
    <p>
      Adalah benar warga kami dan berdomisili di RT.{{ $letter->rt ?? '—' }} RW.{{ $letter->rw ?? '—' }},
      Kelurahan {{ $letter->kelurahan ?? $config->name ?? '—' }},
      Kecamatan {{ $letter->kecamatan ?? $config->district ?? '—' }},
      Kota {{ $config->city ?? '—' }}.
      Surat keterangan ini dibuat untuk keperluan <b>{{ $letter->keperluan ?? '' }}</b>.
    </p>
    <p>
      Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.
    </p>
  </div>

  {{-- TTD --}}
  <div class="ttd-area">
    <div class="ttd-block">
      <div>{{ $config->city ?? '' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
      <div style="margin-top:4px;">Lurah {{ $config->name ?? '' }}</div>
      <div class="ttd-space"></div>
      <div class="ttd-name">( {{ $config->lurah_name ?? '. . . . . . . . . . . . . . . . . . . . . . . .' }} )</div>
      <div class="ttd-nip">NIP. {{ $config->lurah_nip ?? '. . . . . . . . . . . . . . . . .' }}</div>
    </div>
  </div>

</body>
</html>