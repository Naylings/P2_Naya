<script setup lang="ts">
import { ref } from "vue";
import { PdfModel } from "./PdfForm.vue";

const props = defineProps<{
  model: PdfModel;
}>();

function clearPdf() {
    props.model.city=  null;
    props.model.date=  null;
    props.model.subjectAddress=  null;
    props.model.paragraphOne=  null;
    props.model.paragraphTwo=  null;
    props.model.paragraphThree=  null;
    props.model.applicant=  null;
}

</script>

<template>
  <Card>
    <template #title>
      <div class="font-semibold text-xl mb-4">Form</div>
    </template>
    <template #content>
      <div class="grid grid-cols-6 gap-4">
        <!-- Other form fields -->

        <div class="flex flex-col col-span-3 grow basis-0 gap-2">
        <label for="city">Kota</label>
          <InputText
          id="city"
            type="text"
            v-model="props.model.city"
          />
        </div>
        <div class="flex flex-col col-span-3 grow basis-0 gap-2">
          <label for="date">Tanggal</label>
        <DatePicker
          :showIcon="true"
          id="date"
          :showButtonBar="true"
          
          v-model="props.model.date"
        />
        </div>

        <div class="flex flex-col  grow basis-0 col-span-6 gap-2">
            <label for="subject">Subjek & Alamat</label>
        <Textarea id="subject" v-model="props.model.subjectAddress" :autoResize="true" rows="3" cols="30" />
        </div>
        <div class="flex flex-col  grow basis-0 col-span-6 gap-2">
            <label for="pOne">Paragraph 1 (Pembukaan)</label>
        <Textarea id="pOne" v-model="props.model.paragraphOne" :autoResize="true" rows="3" cols="30" />
        </div>
        <div class="flex flex-col  grow basis-0 col-span-6 gap-2">
            <label for="pTwo">Paragraph 2 (Isi)</label>
        <Textarea id="pTwo" v-model="props.model.paragraphTwo" :autoResize="true" rows="3" cols="30" />
        </div>
        <div class="flex flex-col  grow basis-0 col-span-6 gap-2">
            <label for="pThree">Paragraph 3 (Penutup)</label>
        <Textarea id="pThree" v-model="props.model.paragraphThree" :autoResize="true" rows="3" cols="30" />
        </div>

        <div class="flex flex-col  col-span-6 grow basis-0 gap-2">

        <label for="applicant">Nama Penulis</label>
          <InputText
          id="applicant"
            type="text"
            v-model="props.model.applicant"
          />
        </div>
        <Button class="col-span-2" icon="pi pi-upload" label="Simpan Ke Database"></Button>
        <Button class="col-span-2" icon="pi pi-print" severity="secondary" label="Print"></Button>
        <Button class="col-span-2" @click="clearPdf" icon="pi pi-trash" severity="danger" label="Clear"></Button>

        
      </div>
    </template>
  </Card>
</template>
