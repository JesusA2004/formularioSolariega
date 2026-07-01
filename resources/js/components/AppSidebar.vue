<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { FileBarChart, Inbox, LayoutGrid, QrCode, Users } from '@lucide/vue';
import { computed } from 'vue';
import BrandMark from '@/components/BrandMark.vue';
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
import { dashboard } from '@/routes';
import qr from '@/routes/qr';
import reportes from '@/routes/reportes';
import solicitudes from '@/routes/solicitudes';
import usuarios from '@/routes/usuarios';
import type { NavItem } from '@/types';

const page = usePage();
const isAdmin = computed(() => page.props.auth.user?.role === 'admin');

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Mensajes',
        href: solicitudes.index(),
        icon: Inbox,
    },
    {
        title: 'Reportes',
        href: reportes.index(),
        icon: FileBarChart,
    },
    {
        title: 'Código QR',
        href: qr.index(),
        icon: QrCode,
    },
    ...(isAdmin.value
        ? [
              {
                  title: 'Usuarios',
                  href: usuarios.index(),
                  icon: Users,
              },
          ]
        : []),
]);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        size="lg"
                        as-child
                        class="h-auto py-2 group-data-[collapsible=icon]:!size-9 group-data-[collapsible=icon]:!p-1"
                    >
                        <Link :href="dashboard()" class="gap-2.5">
                            <BrandMark
                                class="h-9 w-auto max-w-[140px] shrink-0 group-data-[collapsible=icon]:max-w-7"
                            />
                            <span
                                class="grid flex-1 text-left text-sm leading-tight group-data-[collapsible=icon]:hidden"
                            >
                                <span class="truncate font-semibold"
                                    >Buzón Solariega</span
                                >
                                <span
                                    class="truncate text-xs text-sidebar-foreground/70"
                                    >Solariega Cenit</span
                                >
                            </span>
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
    <slot />
</template>
