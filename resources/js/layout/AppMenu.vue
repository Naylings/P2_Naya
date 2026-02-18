<script setup>
import { ref } from 'vue';
import AppMenuItem from './AppMenuItem.vue';

const model = ref([
    {
        label: 'Main',
        items: [
            {
                label: 'Dashboard',
                icon: 'pi pi-fw pi-home',
                to: '/'
            },
            {
                label: 'Data User',
                icon: 'pi pi-fw pi-id-card',
                to: '/users'
            },
            {
                label: 'Rukun',
                icon: 'pi pi-fw pi-sitemap',
                to: '/rukun'
            },
            {
                label: 'Keluarga',
                icon: 'pi pi-fw pi-heart',
                to: '/family'
            },
            {
                label: 'Warga',
                icon: 'pi pi-fw pi-users',
                to: '/warga'
            },
            {
                label: 'Config',
                icon: 'pi pi-fw pi-cog',
                to: '/config'
            },
            {
                label: 'Form Pdf',
                icon: 'pi pi-fw pi-file',
                to: '/pdf'
            },
            
            {
                label: 'Surat',
                icon: 'pi pi-fw pi-align-left',
                path: '/doc',
                items: [
                    {
                        label: 'SKTM',
                        icon: 'pi pi-fw pi-align-left',
                        to: '/doc/sktm'
                    },
                    {
                        label: 'Surat Pindah Keluar',
                        icon: 'pi pi-fw pi-align-left',
                        to: '/doc/skpk'
                        
                    },
                    {
                        label: 'Surat Ahli Waris',
                        icon: 'pi pi-fw pi-align-left',
                        to: '/doc/skaw'
                        
                    },
                    {
                        label: 'Surat Domisili',
                        icon: 'pi pi-fw pi-align-left',
                        to: '/doc/skd'
                        
                    },
                ]
            },
        ]
    
    }
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
