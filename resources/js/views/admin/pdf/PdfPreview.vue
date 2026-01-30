<script setup lang="ts">
import { ref } from "vue";
import { PdfModel } from "./PdfForm.vue";

const props = defineProps<{ data: PdfModel }>();

const printArea = ref<HTMLElement | null>(null);

defineExpose({
  printArea,
});

function formatDate(date?: Date | null) {
  if (!date) return "Tanggal";
  return date.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}
</script>



<template>
<div
  ref="printArea"
  class="print-area border p-4 bg-white rounded shadow-sm"
>
    <div class="flex flex-col items-center">
      <span class="text-3xl font-semibold mb-10"> Job Application Letter </span>

      <span class="ms-auto mb-7">
        {{ props.data.city || "Kota" }},
        {{ formatDate(props.data.date) }}
      </span>

      <span class="me-auto whitespace-pre-wrap mb-7">
        {{ props.data.subjectAddress || "penerima\nalamat perusahaan" }}
      </span>

      <span class="me-auto whitespace-pre-wrap mb-7">
        {{ props.data.paragraphOne || "Paragraf pembukaan" }}
      </span>

      <span class="me-auto whitespace-pre-wrap mb-7">
        {{ props.data.paragraphTwo || "Paragraf isi" }}
      </span>

      <span class="me-auto whitespace-pre-wrap mb-7">
        {{ props.data.paragraphThree || "Paragraf penutup" }}
      </span>

      <span class="me-auto mb-14">Dengan Hormat</span>
      <span class="me-auto">
        {{ props.data.applicant || "Nama Penulis" }}
      </span>
    </div>
  </div>
</template>

<style scoped>
@media print {
  body {
    margin: 0;
  }

  body * {
    visibility: hidden;
  }

  .print-area,
  .print-area * {
    visibility: visible;
  }

  .print-area {
    position: absolute;
    left: 0;
    top: 0;
    width: 210mm;
    min-height: 297mm;
    padding: 2cm;
    background: white;
    box-shadow: none;
    border: none;

    font-family: "Times New Roman", serif;
    font-size: 12pt;
    line-height: 1.6;
  }

  @page {
    size: A4;
    margin: 0;
  }
}

</style>
