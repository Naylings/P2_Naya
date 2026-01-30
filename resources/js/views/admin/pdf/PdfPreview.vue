<script setup lang="ts">
import { PdfModel } from "./PdfForm.vue";

const props = defineProps<{ data: PdfModel }>();


function formatDate(iso?: string) {
  if (!iso) return "Tanggal";
  const date = new Date(iso);
  if (isNaN(date.getTime())) return iso; 

  
  return date.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}
</script>

<template>
  <div class="border p-4 bg-white rounded shadow-sm">
    <div class="flex flex-col items-center ">
      <span class="text-3xl font-semibold mb-10">Job Application Letter</span><br />
      <span class="ms-auto mb-7"
        >{{ props.data.city ? props.data.city : "Kota" }},
        {{ props.data.date ? formatDate(props.data.date) : "Tanggal" }}</span
      >
      <span class="me-auto whitespace-pre-wrap mb-7">{{
        props.data.subjectAddress ? props.data.subjectAddress : 'penerima\nalamat perusahaan'
      }}</span>
      
      <span class="me-auto whitespace-pre-wrap mb-7">{{
        props.data.paragraphOne ? props.data.paragraphOne : "ini paragraph satu, pembukaan, biasanya berisi dari mana kalian mendapatkan atau mengetahui suatu lowongan kerja yang kalian incar"
      }}</span>
      
      <span class="me-auto whitespace-pre-wrap mb-7">{{
        props.data.paragraphTwo ? props.data.paragraphTwo : "Ini paragraph dua, isinya, berisi tentang bagaimana kalian bisa berguna untukposisi atau pekerjaan yang kalian incar"
      }}</span>
      
      <span class="me-auto whitespace-pre-wrap mb-7">{{
        props.data.paragraphThree ? props.data.paragraphThree : "Ini paragraph ketiga, tentang pnutup, disini kalian biasanya isi dengan ucapan harapan dapat bekerja dan interview, serta terimakasih"
      }}</span>
      <span class="me-auto mb-14">Dengan Hormat</span>
      <span class="me-auto">{{
        props.data.applicant ? props.data.applicant : "Nama Penuis"
      }}</span>

      
    </div>
  </div>
</template>

<style scoped>
/* keep styling minimal and compatible with Tailwind (used in project) */
/* add small fallback for environments without tailwind */
.border {
  border: 1px solid #e5e7eb;
}
.bg-white {
  background: #fff;
}
.rounded {
  border-radius: 0.375rem;
}
.shadow-sm {
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}
.p-4 {
  padding: 1rem;
}
.w-28 {
  width: 7rem;
}
.h-28 {
  height: 7rem;
}
.text-xs {
  font-size: 0.75rem;
}
.text-sm {
  font-size: 0.875rem;
}
.text-lg {
  font-size: 1.125rem;
}
.font-semibold {
  font-weight: 600;
}
.font-medium {
  font-weight: 500;
}
.text-gray-400 {
  color: #9ca3af;
}
.text-gray-500 {
  color: #6b7280;
}
.text-gray-600 {
  color: #4b5563;
}
.text-gray-700 {
  color: #374151;
}
.bg-gray-50 {
  background: #f9fafb;
}
.bg-gray-100 {
  background: #f3f4f6;
}
.border-gray-100 {
  border-color: #f3f4f6;
}
.overflow-hidden {
  overflow: hidden;
}
.flex {
  display: flex;
}
.items-center {
  align-items: center;
}
.gap-4 {
  gap: 1rem;
}
.flex-1 {
  flex: 1 1 0%;
}
.flex-shrink-0 {
  flex-shrink: 0;
}
.mt-2 {
  margin-top: 0.5rem;
}
.mt-4 {
  margin-top: 1rem;
}
.pt-4 {
  padding-top: 1rem;
}
</style>
