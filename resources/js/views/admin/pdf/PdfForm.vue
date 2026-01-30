<script setup lang="ts">
import { reactive, ref } from "vue";
import jsPDF from "jspdf";
import html2canvas from "html2canvas";
import PdfInput from "./PdfInput.vue";
import PdfPreview from "./PdfPreview.vue";

export interface PdfModel {
    city: string | null
    date: Date | null
    subjectAddress: string | null
    paragraphOne: string | null
    paragraphTwo: string | null
    paragraphThree: string | null
    applicant: string | null
}

function printPdf() {
  window.print();
}

const pdfModel = reactive<PdfModel>({
  city:  null,
  date:  null,
  subjectAddress:  null,
  paragraphOne:  null,
  paragraphTwo:  null,
  paragraphThree:  null,
  applicant:  null,
});



const previewRef = ref<InstanceType<typeof PdfPreview>>();

async function exportPdf() {
  const el = previewRef.value?.printArea;
  if (!el) return;

  const canvas = await html2canvas(el, {
    scale: 2,
    backgroundColor: "#fff",
  });

  const img = canvas.toDataURL("image/png");
  const pdf = new jsPDF("p", "mm", "a4");

  const w = pdf.internal.pageSize.getWidth();
  const h = (canvas.height * w) / canvas.width;

  pdf.addImage(img, "PNG", 0, 0, w, h);
  pdf.save("job-application-letter.pdf");
}
</script>
<template>
  <div class="grid grid-cols-2 gap-4">
    <!-- left form -->
    <PdfInput :model="pdfModel" @print="printPdf"/>

    <!-- right preview -->
    <PdfPreview :data="pdfModel"  />
  </div>
</template>
