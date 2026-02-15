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
import { blastingCampaigns, blastingCampaignsCreate, dashboard, email, template, templateCreate, log, recipientsCreate, recipients } from '@/routes';
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
                href: blastingCampaigns(),
            },
            {
                title: 'Templates',
                href: template(),
            },
            {
                title: 'Logs',
                href: log(),
            },
        ],
    },

    // 🔹 MASTER RECIPIENT
    {
        title: 'Recipients',
        icon: Users,
        children: [
            {
                title: 'Master Recipients',
                href: recipients().url,
            },
            {
                title: 'Create Recipient',
                href: recipientsCreate().url,
            },
        ],
    },

    // 🔹 MASTER EMAIL
    {
        title: 'Email',
        href: email(),
        icon: Mail,
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
