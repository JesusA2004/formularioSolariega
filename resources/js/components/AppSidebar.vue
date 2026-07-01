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
        title: 'Solicitudes',
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
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()" class="gap-3">
                            <BrandMark class="size-8 shrink-0 text-sm" />
                            <span
                                class="grid flex-1 text-left text-sm leading-tight"
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
