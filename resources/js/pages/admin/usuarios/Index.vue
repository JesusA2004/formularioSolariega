<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Power, Trash2, UserRound } from '@lucide/vue';
import { ref } from 'vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Switch } from '@/components/ui/switch';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { dashboard } from '@/routes';
import usuarios from '@/routes/usuarios';
import type { OptionItem } from '@/types/buzon';

type AdminUser = {
    id: number;
    name: string;
    email: string;
    role: string;
    role_label: string;
    is_active: boolean;
    created_at: string;
};

defineProps<{
    users: AdminUser[];
    roles: OptionItem[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Usuarios', href: usuarios.index() },
        ],
    },
});

const createOpen = ref(false);
const editOpen = ref(false);
const deleteTarget = ref<AdminUser | null>(null);

const createForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'supervisor',
    is_active: true,
});

function submitCreate() {
    createForm.post(usuarios.store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            createOpen.value = false;
            createForm.reset();
        },
    });
}

const editForm = useForm({
    id: 0,
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'supervisor',
    is_active: true,
});

function openEdit(user: AdminUser) {
    editForm.id = user.id;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.password = '';
    editForm.password_confirmation = '';
    editForm.role = user.role;
    editForm.is_active = user.is_active;
    editOpen.value = true;
}

function submitEdit() {
    editForm.put(usuarios.update.url(editForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            editOpen.value = false;
        },
    });
}

function toggleActive(user: AdminUser) {
    router.patch(
        usuarios.toggle.url(user.id),
        {},
        { preserveScroll: true, preserveState: true },
    );
}

function confirmDelete() {
    if (!deleteTarget.value) {
        return;
    }

    router.delete(usuarios.destroy.url(deleteTarget.value.id), {
        preserveScroll: true,
        onFinish: () => {
            deleteTarget.value = null;
        },
    });
}
</script>

<template>
    <Head title="Usuarios" />

    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-4 md:p-6">
        <div
            class="flex flex-col justify-between gap-2 sm:flex-row sm:items-center"
        >
            <div>
                <h1 class="text-2xl font-semibold">Usuarios</h1>
                <p class="text-sm text-muted-foreground">
                    Administra las cuentas con acceso al panel administrativo.
                </p>
            </div>
            <Button @click="createOpen = true">
                <Plus class="size-4" />
                Nuevo usuario
            </Button>
        </div>

        <Card>
            <CardContent class="p-0">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nombre</TableHead>
                            <TableHead>Correo</TableHead>
                            <TableHead>Rol</TableHead>
                            <TableHead>Estado</TableHead>
                            <TableHead class="text-right">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="users.length === 0">
                            <TableCell colspan="5" class="py-12 text-center">
                                <div
                                    class="flex flex-col items-center gap-2 text-muted-foreground"
                                >
                                    <UserRound class="size-8" />
                                    <p class="text-sm">
                                        Aún no hay usuarios registrados.
                                    </p>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="user in users" :key="user.id">
                            <TableCell class="font-medium">{{
                                user.name
                            }}</TableCell>
                            <TableCell class="text-muted-foreground">{{
                                user.email
                            }}</TableCell>
                            <TableCell>
                                <Badge variant="secondary">{{
                                    user.role_label
                                }}</Badge>
                            </TableCell>
                            <TableCell>
                                <Badge
                                    variant="outline"
                                    :class="
                                        user.is_active
                                            ? 'border-primary/30 bg-primary/10 text-primary'
                                            : 'border-border bg-muted text-muted-foreground'
                                    "
                                >
                                    {{ user.is_active ? 'Activo' : 'Inactivo' }}
                                </Badge>
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-1.5">
                                    <Button
                                        size="icon-sm"
                                        variant="outline"
                                        title="Editar"
                                        @click="openEdit(user)"
                                    >
                                        <Pencil class="size-3.5" />
                                    </Button>
                                    <Button
                                        size="icon-sm"
                                        variant="outline"
                                        title="Activar / desactivar"
                                        @click="toggleActive(user)"
                                    >
                                        <Power class="size-3.5" />
                                    </Button>
                                    <Button
                                        size="icon-sm"
                                        variant="outline"
                                        title="Eliminar"
                                        @click="deleteTarget = user"
                                    >
                                        <Trash2
                                            class="size-3.5 text-destructive"
                                        />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>

    <!-- Crear usuario -->
    <Dialog v-model:open="createOpen">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Nuevo usuario</DialogTitle>
                <DialogDescription
                    >Crea una cuenta de acceso al panel
                    administrativo.</DialogDescription
                >
            </DialogHeader>
            <form class="space-y-4" @submit.prevent="submitCreate">
                <div class="space-y-1.5">
                    <Label for="create_name">Nombre</Label>
                    <Input id="create_name" v-model="createForm.name" />
                    <p
                        v-if="createForm.errors.name"
                        class="text-xs text-destructive"
                    >
                        {{ createForm.errors.name }}
                    </p>
                </div>
                <div class="space-y-1.5">
                    <Label for="create_email">Correo electrónico</Label>
                    <Input
                        id="create_email"
                        v-model="createForm.email"
                        type="email"
                    />
                    <p
                        v-if="createForm.errors.email"
                        class="text-xs text-destructive"
                    >
                        {{ createForm.errors.email }}
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <Label for="create_password">Contraseña</Label>
                        <Input
                            id="create_password"
                            v-model="createForm.password"
                            type="password"
                        />
                        <p
                            v-if="createForm.errors.password"
                            class="text-xs text-destructive"
                        >
                            {{ createForm.errors.password }}
                        </p>
                    </div>
                    <div class="space-y-1.5">
                        <Label for="create_password_confirmation"
                            >Confirmar</Label
                        >
                        <Input
                            id="create_password_confirmation"
                            v-model="createForm.password_confirmation"
                            type="password"
                        />
                    </div>
                </div>
                <div class="space-y-1.5">
                    <Label>Rol</Label>
                    <Select v-model="createForm.role">
                        <SelectTrigger class="w-full"
                            ><SelectValue
                        /></SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="opt in roles"
                                :key="opt.value"
                                :value="opt.value"
                                >{{ opt.label }}</SelectItem
                            >
                        </SelectContent>
                    </Select>
                </div>
                <div class="flex items-center gap-2">
                    <Switch v-model="createForm.is_active" />
                    <Label>Cuenta activa</Label>
                </div>
                <DialogFooter>
                    <Button type="submit" :disabled="createForm.processing">
                        <Spinner v-if="createForm.processing" />
                        Crear usuario
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <!-- Editar usuario -->
    <Dialog v-model:open="editOpen">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Editar usuario</DialogTitle>
                <DialogDescription
                    >Actualiza los datos de la cuenta.</DialogDescription
                >
            </DialogHeader>
            <form class="space-y-4" @submit.prevent="submitEdit">
                <div class="space-y-1.5">
                    <Label for="edit_name">Nombre</Label>
                    <Input id="edit_name" v-model="editForm.name" />
                    <p
                        v-if="editForm.errors.name"
                        class="text-xs text-destructive"
                    >
                        {{ editForm.errors.name }}
                    </p>
                </div>
                <div class="space-y-1.5">
                    <Label for="edit_email">Correo electrónico</Label>
                    <Input
                        id="edit_email"
                        v-model="editForm.email"
                        type="email"
                    />
                    <p
                        v-if="editForm.errors.email"
                        class="text-xs text-destructive"
                    >
                        {{ editForm.errors.email }}
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <Label for="edit_password">Nueva contraseña</Label>
                        <Input
                            id="edit_password"
                            v-model="editForm.password"
                            type="password"
                            placeholder="Dejar en blanco para no cambiar"
                        />
                    </div>
                    <div class="space-y-1.5">
                        <Label for="edit_password_confirmation"
                            >Confirmar</Label
                        >
                        <Input
                            id="edit_password_confirmation"
                            v-model="editForm.password_confirmation"
                            type="password"
                        />
                    </div>
                </div>
                <div class="space-y-1.5">
                    <Label>Rol</Label>
                    <Select v-model="editForm.role">
                        <SelectTrigger class="w-full"
                            ><SelectValue
                        /></SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="opt in roles"
                                :key="opt.value"
                                :value="opt.value"
                                >{{ opt.label }}</SelectItem
                            >
                        </SelectContent>
                    </Select>
                </div>
                <div class="flex items-center gap-2">
                    <Switch v-model="editForm.is_active" />
                    <Label>Cuenta activa</Label>
                </div>
                <DialogFooter>
                    <Button type="submit" :disabled="editForm.processing">
                        <Spinner v-if="editForm.processing" />
                        Guardar cambios
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <!-- Confirmar eliminación -->
    <AlertDialog
        :open="!!deleteTarget"
        @update:open="(value) => !value && (deleteTarget = null)"
    >
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle
                    >¿Eliminar a {{ deleteTarget?.name }}?</AlertDialogTitle
                >
                <AlertDialogDescription>
                    Esta acción no se puede deshacer. El usuario perderá acceso
                    al panel administrativo de forma permanente.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Cancelar</AlertDialogCancel>
                <AlertDialogAction
                    class="bg-destructive text-white hover:bg-destructive/90"
                    @click="confirmDelete"
                >
                    Eliminar
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
