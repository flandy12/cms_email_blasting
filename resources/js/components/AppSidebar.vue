<script setup lang="ts">
import { computed, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

import NavMain from '@/components/NavMain.vue'
import NavUser from '@/components/NavUser.vue'
import AppLogo from './AppLogo.vue'

import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar'

import {
    LayoutGrid,
    Users,
    Send,
    Folder,
    BookOpen,
    Menu,
    Logs,
    TimerIcon,
    Users2Icon,
} from 'lucide-vue-next'

import { dashboard } from '@/routes'
import campaigns from '@/routes/blasting/campaigns'
import recipients from '@/routes/blasting/recipients'
import templates from '@/routes/templates'
import log from '@/routes/log'
import users from '@/routes/users'
import roles from '@/routes/roles'
import permissions from '@/routes/permissions'

import type { NavItem } from '@/types'

/* =========================
Sidebar Toggle
========================= */

const openSidebar = ref(false)

const toggleSidebar = () => {
    openSidebar.value = !openSidebar.value
}

/* =========================
Permission
========================= */



const page = usePage();

console.log(page.props.auth);


const userPermissions = computed(() => page.props.auth?.permissions ?? [])

const hasPermission = (permission?: string) => {
    if (!permission) return true

    return userPermissions.value.includes(permission)
}

/* =========================
Navigation
========================= */

const allNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
        permission: 'dashboard.view',
    },
    {
        title: 'Template Campaign',
        href: templates.index(),
        icon: Send,
        permission: 'template-campaign.view',
    },
    {
        title: 'Recipients',
        href: recipients.index(),
        icon: Users,
        permission: 'recipients.view',
    },
    {
        title: 'Schedule List',
        href: campaigns.index(),
        icon: TimerIcon,
        permission: 'schedule.view',
    },
    {
        title: 'Users',
        href: users.index(),
        icon: Users2Icon,
        permission: 'users.view',
    },
    {
        title: 'Roles',
        href: roles.index(),
        icon: Users2Icon,
        permission: 'roles.view',
    },
    {
        title: 'Permission',
        href: permissions.index(),
        icon: Users2Icon,
        permission: 'permissions.view',
    },
    {
        title: 'Logs',
        href: log.index(),
        icon: Logs,
        permission: 'logs.view',
    },
]

const mainNavItems = computed(() =>
    allNavItems.filter(item => hasPermission(item.permission))
)
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