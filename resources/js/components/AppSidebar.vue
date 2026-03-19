<script setup lang="ts">

import NavFooter from '@/components/NavFooter.vue'
import NavMain from '@/components/NavMain.vue'
import NavUser from '@/components/NavUser.vue'

import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem
} from '@/components/ui/sidebar'

import { Link } from '@inertiajs/vue3'

import {
    LayoutGrid,
    Users,
    Send,
    Folder,
    BookOpen,
    Menu,
    Logs,
    TimerIcon
} from 'lucide-vue-next'

import { ref } from 'vue'

import AppLogo from './AppLogo.vue'

import { dashboard } from '@/routes'
import campaigns from '@/routes/blasting/campaigns'
import recipients from '@/routes/blasting/recipients'
import templates from '@/routes/templates'
import log from '@/routes/log'

import type { NavItem } from '@/types'
import { DatePicker } from 'reka-ui/namespaced'


/* =========================
Sidebar Toggle
========================= */

const openSidebar = ref(false)

const toggleSidebar = () => {
    openSidebar.value = !openSidebar.value
}


/* =========================
Navigation Items
========================= */

const mainNavItems: NavItem[] = [

    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid
    },

    {
        title: 'Blasting Campaign',
        icon: Send,
        href: templates.index()
    },

    {
        title: 'Recipients',
        icon: Users,
        href: recipients.index()
    },
    
    {
        title: 'Schedule List',
        icon: TimerIcon,
        href: campaigns.index()
    },

    {
        title: 'Logs',
        icon: Logs,
        href: log.index()
    }

]


const footerNavItems: NavItem[] = [

    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder
    },

    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen
    }

]

</script>



<template>

    <div class="flex min-h-screen">


        <!-- SIDEBAR -->

        <Sidebar :class="[
            'transition-all duration-300',

            openSidebar
                ? 'translate-x-0'
                : '-translate-x-full md:translate-x-0'
        ]" class="fixed md:relative z-40" collapsible="icon" variant="inset">


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

        <!-- CONTENT -->

        <div class="flex-1">


            <!-- MOBILE HEADER -->

            <div class="md:hidden flex items-center gap-3 p-4 border-b bg-white">

                <button @click="toggleSidebar">

                    <Menu class="w-6 h-6" />

                </button>

                <AppLogo />

            </div>



            <!-- PAGE CONTENT -->

            <div class="p-6">
                <slot />

            </div>
        </div>
    </div>


</template>