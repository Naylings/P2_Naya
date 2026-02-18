<script setup lang="ts">
import { computed, defineAsyncComponent, watch, ref, provide } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { getDocDefinition } from '@/views/admin/doc/doc-registry';
import { useDocConfig } from '@/views/admin/doc/useDocConfig';
import { LETTER_DATA_KEY } from '@/views/admin/doc/doc-types';

const route  = useRoute();
const router = useRouter();

const docType = computed(() => route.params.type as string);
const docDef  = computed(() => getDocDefinition(docType.value));

watch(docDef, (def) => {
  if (!def) router.replace({ name: 'notfound' });
}, { immediate: true });

const FormComponent = computed(() =>
  docDef.value ? defineAsyncComponent(docDef.value.formComponent) : null
);
const PreviewComponent = computed(() =>
  docDef.value ? defineAsyncComponent(docDef.value.previewComponent) : null
);

const { config } = useDocConfig();

// ====== Shared reactive data — provide ke SEMUA children (Form & Preview) ======
const letterData = ref<Record<string, any>>({});
provide(LETTER_DATA_KEY, letterData);
</script>

<template>
  <div>
    <Toast />

    <div class="mb-6">
      <div class="font-semibold text-xl">{{ docDef?.label ?? 'Surat' }}</div>
      <p class="text-gray-500 text-sm mt-1">Form dan preview surat</p>
    </div>

    <div v-if="docDef" class="grid grid-cols-1 xl:grid-cols-2 gap-6">

      <!-- Kiri: Form -->
      <Suspense>
        <component
          :is="FormComponent"
          :config="config"
          :doc-def="docDef"
        />
        <template #fallback>
          <div class="flex flex-col gap-3">
            <Skeleton height="3rem" />
            <Skeleton height="3rem" />
            <Skeleton height="6rem" />
          </div>
        </template>
      </Suspense>

      <!-- Kanan: Preview -->
      <Suspense>
        <component
          :is="PreviewComponent"
          :config="config"
        />
        <template #fallback>
          <Skeleton height="600px" />
        </template>
      </Suspense>

    </div>
  </div>
</template>