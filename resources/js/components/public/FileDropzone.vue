<script setup lang="ts">
import { FileText, Image as ImageIcon, Trash2, UploadCloud } from '@lucide/vue';
import { computed, ref } from 'vue';
import { cn } from '@/lib/utils';

const props = withDefaults(
    defineProps<{
        modelValue: File[];
        maxFiles?: number;
        maxSizeMb?: number;
        accept?: string;
    }>(),
    {
        maxFiles: 5,
        maxSizeMb: 20,
        accept: '.jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.mp4,.mov',
    },
);

const emit = defineEmits<{
    'update:modelValue': [files: File[]];
}>();

const isDragging = ref(false);
const error = ref<string | null>(null);
const inputRef = ref<HTMLInputElement | null>(null);

const previews = computed(() =>
    props.modelValue.map((file) => ({
        file,
        url: file.type.startsWith('image/') ? URL.createObjectURL(file) : null,
    })),
);

function humanSize(bytes: number): string {
    if (bytes < 1024) {
        return `${bytes} B`;
    }

    if (bytes < 1024 * 1024) {
        return `${(bytes / 1024).toFixed(1)} KB`;
    }

    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

function addFiles(fileList: FileList | File[]) {
    error.value = null;
    const incoming = Array.from(fileList);
    const next = [...props.modelValue];

    for (const file of incoming) {
        if (next.length >= props.maxFiles) {
            error.value = `Puedes adjuntar un máximo de ${props.maxFiles} archivos.`;
            break;
        }

        if (file.size > props.maxSizeMb * 1024 * 1024) {
            error.value = `"${file.name}" supera el límite de ${props.maxSizeMb} MB.`;
            continue;
        }

        next.push(file);
    }

    emit('update:modelValue', next);
}

function onDrop(event: DragEvent) {
    isDragging.value = false;

    if (event.dataTransfer?.files?.length) {
        addFiles(event.dataTransfer.files);
    }
}

function onInputChange(event: Event) {
    const target = event.target as HTMLInputElement;

    if (target.files?.length) {
        addFiles(target.files);
    }

    target.value = '';
}

function removeFile(index: number) {
    const next = [...props.modelValue];
    next.splice(index, 1);
    emit('update:modelValue', next);
}

function openFileDialog() {
    inputRef.value?.click();
}
</script>

<template>
    <div class="space-y-3">
        <div
            :class="
                cn(
                    'group relative flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed px-6 py-10 text-center transition-colors',
                    isDragging
                        ? 'border-primary bg-primary/5'
                        : 'border-input hover:border-primary/60 hover:bg-accent/40',
                )
            "
            @click="openFileDialog"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="onDrop"
        >
            <input
                ref="inputRef"
                type="file"
                multiple
                :accept="accept"
                class="hidden"
                @change="onInputChange"
            />
            <div
                class="flex size-12 items-center justify-center rounded-full bg-primary/10 text-primary transition-transform group-hover:scale-110"
            >
                <UploadCloud class="size-6" />
            </div>
            <p class="text-sm font-medium">
                Arrastra tus archivos aquí o
                <span class="text-primary underline underline-offset-2"
                    >selecciona desde tu dispositivo</span
                >
            </p>
            <p class="text-xs text-muted-foreground">
                Imágenes, PDF, Word, Excel o video — máximo {{ maxSizeMb }} MB
                por archivo, hasta {{ maxFiles }} archivos
            </p>
        </div>

        <p v-if="error" class="text-sm text-destructive">{{ error }}</p>

        <transition-group
            v-if="previews.length"
            tag="ul"
            class="grid gap-2 sm:grid-cols-2"
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <li
                v-for="(item, index) in previews"
                :key="`${item.file.name}-${item.file.lastModified}-${index}`"
                class="flex items-center gap-3 rounded-lg border bg-card p-2 shadow-xs"
            >
                <div
                    class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-md bg-muted"
                >
                    <img
                        v-if="item.url"
                        :src="item.url"
                        :alt="item.file.name"
                        class="size-full object-cover"
                    />
                    <ImageIcon
                        v-else-if="item.file.type.startsWith('image/')"
                        class="size-5 text-muted-foreground"
                    />
                    <FileText v-else class="size-5 text-muted-foreground" />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium">
                        {{ item.file.name }}
                    </p>
                    <p class="text-xs text-muted-foreground">
                        {{ humanSize(item.file.size) }}
                    </p>
                </div>
                <button
                    type="button"
                    class="flex size-8 shrink-0 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-destructive/10 hover:text-destructive"
                    aria-label="Eliminar archivo"
                    @click="removeFile(index)"
                >
                    <Trash2 class="size-4" />
                </button>
            </li>
        </transition-group>
    </div>
</template>
