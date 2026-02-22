<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronDown } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();
const open = ref<string | null>(null);

const toggle = (title: string) => {
    open.value = open.value === title ? null : title;
};
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <!-- Jika ada children -->
                <template v-if="item.children && item.children.length">
                    <SidebarMenuButton as="button" class="flex justify-between items-center w-full"
                        @click="toggle(item.title)">
                        <div class="flex items-center gap-2">
                            <component :is="item.icon" class="w-4 h-4" v-if="item.icon" />
                            <span>{{ item.title }}</span>
                        </div>
                        <ChevronDown class="w-4 h-4 transition-transform"
                            :class="{ 'rotate-180': open === item.title }" />
                    </SidebarMenuButton>

                    <transition name="fade">
                        <div v-if="open === item.title" class="ml-6 mt-1 flex flex-col space-y-1">
                            <Link v-for="child in item.children" :key="child.title" :href="child.href"
                                class="text-sm text-muted-foreground hover:text-foreground transition" :class="{
                                    'text-foreground font-medium': urlIsActive(child.href, page.url),
                                }">
                                {{ child.title }}
                            </Link>
                        </div>
                    </transition>
                </template>

                <!-- Jika tidak ada children -->
                <template v-else>
                    <SidebarMenuButton as-child :is-active="urlIsActive(item.href, page.url)" :tooltip="item.title">
                        <Link :href="item.href" class="flex items-center gap-2">
                            <component :is="item.icon" class="w-4 h-4" v-if="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </template>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: all 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}
</style>
