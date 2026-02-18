<script setup lang="ts">
import { inject, computed } from "vue";
import { LETTER_DATA_KEY } from "@/views/admin/doc/doc-types";
import type { LurahConfig } from "@/service/ConfigService";

const props = defineProps<{ config: LurahConfig | null }>();
const letterData = inject(LETTER_DATA_KEY, null);
const data = computed(
  () => (letterData?.value ?? {}) as Record<string, string>,
);

const tempatTglLahir = computed(() => {
  const t = data.value.tempat_lahir;
  const d = data.value.tgl_lahir;
  if (t && d) return `${t}, ${d}`;
  return t || d || "—";
});

function today() {
  return new Date().toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}

const logoUrl = computed(() => props.config?.logo ?? null);

function formatContact(raw?: string | null): string {
  if (!raw) return "[contact]";
  const digits = raw.replace(/\D/g, "");
  if (digits.length < 8) return raw;
  return `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6)}`;
}
</script>

<template>
  <div class="preview-wrapper">
    <div class="surat-scaler">
      <div class="surat-page">
        <!-- KOP SURAT -->
        <table class="kop-table">
          <tbody>
            <tr>
              <td class="kop-side">
                <img
                  v-if="logoUrl"
                  :src="logoUrl"
                  alt="Logo"
                  class="kop-logo"
                />
                <svg
                  v-else
                  viewBox="0 0 54 40"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                  class="kop-logo"
                >
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M17.1637 19.2467C17.1566 19.4033 17.1529 19.561 17.1529 19.7194C17.1529 25.3503 21.7203 29.915 27.3546 29.915C32.9887 29.915 37.5561 25.3503 37.5561 19.7194C37.5561 19.5572 37.5524 19.3959 37.5449 19.2355C38.5617 19.0801 39.5759 18.9013 40.5867 18.6994L40.6926 18.6782C40.7191 19.0218 40.7326 19.369 40.7326 19.7194C40.7326 27.1036 34.743 33.0896 27.3546 33.0896C19.966 33.0896 13.9765 27.1036 13.9765 19.7194C13.9765 19.374 13.9896 19.0316 14.0154 18.6927L14.0486 18.6994C15.0837 18.9062 16.1223 19.0886 17.1637 19.2467ZM33.3284 11.4538C31.6493 10.2396 29.5855 9.52381 27.3546 9.52381C25.1195 9.52381 23.0524 10.2421 21.3717 11.4603C20.0078 11.3232 18.6475 11.1387 17.2933 10.907C19.7453 8.11308 23.3438 6.34921 27.3546 6.34921C31.36 6.34921 34.9543 8.10844 37.4061 10.896C36.0521 11.1292 34.692 11.3152 33.3284 11.4538ZM43.826 18.0518C43.881 18.6003 43.9091 19.1566 43.9091 19.7194C43.9091 28.8568 36.4973 36.2642 27.3546 36.2642C18.2117 36.2642 10.8 28.8568 10.8 19.7194C10.8 19.1615 10.8276 18.61 10.8816 18.0663L7.75383 17.4411C7.66775 18.1886 7.62354 18.9488 7.62354 19.7194C7.62354 30.6102 16.4574 39.4388 27.3546 39.4388C38.2517 39.4388 47.0855 30.6102 47.0855 19.7194C47.0855 18.9439 47.0407 18.1789 46.9536 17.4267L43.826 18.0518ZM44.2613 9.54743L40.9084 10.2176C37.9134 5.95821 32.9593 3.1746 27.3546 3.1746C21.7442 3.1746 16.7856 5.96385 13.7915 10.2305L10.4399 9.56057C13.892 3.83178 20.1756 0 27.3546 0C34.5281 0 40.8075 3.82591 44.2613 9.54743Z"
                    fill="var(--primary-color)"
                  />
                </svg>
              </td>
              <td class="kop-center">
                <div class="kop-line sm">
                  PEMERINTAH KOTA {{ (config?.city ?? "[kota]").toUpperCase() }}
                </div>
                <div class="kop-line sm">
                  KECAMATAN
                  {{ (config?.district ?? "[kecamatan]").toUpperCase() }}
                </div>
                <div class="kop-line lg">
                  KELURAHAN {{ (config?.name ?? "[kelurahan]").toUpperCase() }}
                </div>
                <div class="kop-line xs">
                  Alamat : {{ config?.address ?? "[alamat]" }} Telp.
                  {{ formatContact(config?.contact) }}
                  {{ config?.city ?? "[kota]" }}
                  {{ config?.pos_code ?? "[pos code]" }}
                </div>
              </td>
              <td class="kop-side"></td>
            </tr>
          </tbody>
        </table>
        <hr class="kop-divider" />

        <!-- JUDUL -->
        <div class="surat-title">
          <div class="surat-title-text">SURAT KETERANGAN KEMATIAN</div>
          <div class="surat-no">
            Nomor: {{ data.no_surat || "___/___/___/____" }}
          </div>
        </div>

        <p class="pembuka">
          Yang bertanda tangan di bawah ini, Lurah
          <b>{{ config?.name ?? "[kelurahan]" }}</b
          >, Kecamatan <b>{{ config?.district ?? "[kecamatan]" }}</b
          >, Kota <b>{{ config?.city ?? "[kota]" }}</b
          >, Provinsi <b>{{ config?.province ?? "[provinsi]" }}</b
          >, menerangkan bahwa:
        </p>

        <!-- DATA PEMOHON -->
        <table class="perihal-table">
          <tbody>
            <tr>
              <td>Nama Lengkap</td>
              <td>:</td>
              <td>{{ data.nama_pemohon || "—" }}</td>
            </tr>
            <tr>
              <td>Tempat / Tanggal Lahir</td>
              <td>:</td>
              <td>{{ tempatTglLahir }}</td>
            </tr>
            <tr>
              <td>Jenis Kelamin</td>
              <td>:</td>
              <td>{{ data.jenis_kelamin || "—" }}</td>
            </tr>
            <tr>
              <td>Agama</td>
              <td>:</td>
              <td>{{ data.agama || "—" }}</td>
            </tr>
            <tr>
              <td>Status Pernikahan</td>
              <td>:</td>
              <td>{{ data.married_status || "—" }}</td>
            </tr>
            <tr>
              <td>Pekerjaan</td>
              <td>:</td>
              <td>{{ data.occupation || "—" }}</td>
            </tr>
            <tr>
              <td>Alamat</td>
              <td>:</td>
              <td>{{ data.alamat || "—" }}</td>
            </tr>
          </tbody>
        </table>
        <div class="surat-isi">
          <p>
            Orang diatas adalah benar warga Kelurahan
            {{ data.kelurahan || "—" }} dan telah meninggal dunia yaitu:
          </p>
        </div>
        <table class="perihal-table">
          <tbody>
            <tr>
              <td>Pada Hari</td>
              <td>:</td>
              <td>{{ data.nama_pemohon || "—" }}</td>
            </tr>
            <tr>
              <td>Tanggal</td>
              <td>:</td>
              <td>{{ tempatTglLahir }}</td>
            </tr>
            <tr>
              <td>Meninggal Karena</td>
              <td>:</td>
              <td>{{ data.jenis_kelamin || "—" }}</td>
            </tr>
            <tr>
              <td>Di</td>
              <td>:</td>
              <td>{{ data.agama || "—" }}</td>
              <!-- lokasi meninggal -->
            </tr>
          </tbody>
        </table>
        <!-- ISI -->
        <div class="surat-isi">
          <p>
            Orang diatas adalah benar warga Kelurahan
            {{ data.kelurahan || "—" }} dan telah meninggal dunia yaitu:
          </p>
          <p class="penutup">
            Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana
            mestinya.
          </p>
        </div>

        <!-- TTD -->
        <div class="ttd-area">
          <div class="ttd-block">
            <div>{{ config?.city ?? "" }}, {{ today() }}</div>
            <div>Lurah {{ config?.name ?? "" }}</div>
            <div class="ttd-space"></div>
            <div class="ttd-name">
              (. . . . . . . . . . . . . . . . . . . . . . . . )
            </div>
            <div class="ttd-name">NIP. . . . . . . . . . . . . . . . .</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.preview-wrapper {
  background: #e5e7eb;
  padding: 24px;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  min-height: 100%;
}
.surat-scaler {
  --scale: 0.72;
  transform: scale(var(--scale));
  transform-origin: top center;
  width: 794px;
  flex-shrink: 0;
  margin-bottom: calc(794px * (var(--scale) - 1));
}
.surat-page {
  background: #fff;
  width: 794px;
  min-height: 1123px;
  padding: 2cm 2.5cm;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.12);
  font-family: "Times New Roman", Times, serif;
  font-size: 12pt;
  color: #000;
}
.kop-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 6px;
}
.kop-side {
  width: 90px;
  vertical-align: middle;
}
.kop-center {
  text-align: center;
  vertical-align: middle;
}
.kop-logo {
  width: 75px;
  height: 75px;
  display: block;
}
.kop-line {
  line-height: 1.4;
}
.kop-line.lg {
  font-size: 16pt;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 1.5px;
}
.kop-line.sm {
  font-size: 11pt;
  letter-spacing: 1px;
}
.kop-line.xs {
  font-size: 9pt;
  margin-top: 4px;
}
.kop-divider {
  border: none;
  border-top: 3px solid #000;
  margin-bottom: 20px;
}
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
.pembuka {
  margin-top: 16px;
  line-height: 1.8;
  text-align: justify;
  margin-bottom: 4px;
}
.perihal-table {
  width: 100%;
  margin: 12px 0;
  border-collapse: collapse;
  line-height: 1.7;
}
.perihal-table td {
  vertical-align: top;
  padding: 2px 4px;
}
.perihal-table td:first-child {
  width: 160px;
  white-space: nowrap;
}
.perihal-table td:nth-child(2) {
  width: 10px;
  text-align: center;
  white-space: nowrap;
}
.perihal-table td:last-child {
  width: auto;
}
.surat-isi {
  line-height: 1.8;
  text-align: justify;
  margin-top: 12px;
}
.surat-isi p {
  margin-bottom: 12px;
}
.ttd-area {
  display: flex;
  justify-content: flex-end;
  margin-top: 40px;
}
.ttd-block {
  text-align: center;
  width: 220px;
  line-height: 1.7;
}
.ttd-space {
  height: 70px;
}
.ttd-name {
  font-weight: bold;
  padding-top: 4px;
}
</style>
