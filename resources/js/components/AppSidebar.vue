<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import {  dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    LayoutGrid,
    Users,
    Send,
    FileText,
    Mail,
    ClipboardList,
    Folder,
    BookOpen,
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import recipients from '@/routes/blasting/recipients';
import log from '@/routes/log';
import templates from '@/routes/templates';
import blasting from '@/routes/blasting';
import campaigns from '@/routes/blasting/campaigns';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },

    // 🔹 BLASTING CAMPAIGN (CORE FEATURE)
    {
        title: 'Blasting Campaign',
        icon: Send,
        children: [
            {
                title: 'Campaign List',
                href: campaigns.index(),
            },
            {
                title: 'Templates',
                href: templates.index(),
            },
            {
                title: 'Logs',
                href: log.index(),
            },
        ],
    },

    // 🔹 MASTER RECIPIENT
    {
        title: 'Recipients',
        icon: Users,
        href: recipients.index(),
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
