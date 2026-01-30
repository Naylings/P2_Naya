<script setup lang="ts">
import { reactive } from "vue";
import PdfInput from "./PdfInput.vue";
import PdfPreview from "./PdfPreview.vue";

export interface PdfModel {
    city: string | null
    date: date | null
    subjectAddress: string | null
    paragraphOne: string | null
    paragraphTwo: string | null
    paragraphThree: string | null
    applicant: string | null
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
</script>
<template>
  <div class="grid grid-cols-2 gap-4">
    <!-- left form -->
    <PdfInput :model="pdfModel" />

    <!-- right preview -->
    <PdfPreview :data="pdfModel" />
  </div>
</template>
