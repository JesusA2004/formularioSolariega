<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import DatePickerField from '@/components/public/DatePickerField.vue';
import FileDropzone from '@/components/public/FileDropzone.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
import { Textarea } from '@/components/ui/textarea';
import { store } from '@/routes/reportar';
import type { FormOptions } from '@/types/buzon';

const props = defineProps<{
    options: FormOptions;
}>();

const heroImageMissing = ref(false);

const form = useForm({
    full_name: '',
    contact_info: '',
    department: '',
    request_type: '',
    incident_date: null as string | null,
    involved_people: '',
    description: '',
    attachments: [] as File[],
    accepted_terms: false,
});

const canSubmit = computed(
    () =>
        form.full_name.trim() !== '' &&
        form.contact_info.trim() !== '' &&
        form.department !== '' &&
        form.request_type !== '' &&
        form.description.trim().length >= 20 &&
        form.accepted_terms,
);

function submit() {
    form.post(store.url(), { forceFormData: true });
}
</script>

<template>
    <Head>
        <meta
            name="description"
            content="Canal interno de atención de Solariega Cenit para compartir comentarios, sugerencias y situaciones que requieran seguimiento de forma confidencial y organizada."
        />
        <meta
            name="keywords"
            content="Buzón Solariega, Solariega Cenit, comentarios, sugerencias, seguimiento, atención interna, colaboradores"
        />
        <meta name="robots" content="index, follow" />
        <link rel="canonical" href="https://buzon.solariegacenit.com/" />

        <meta property="og:title" content="Buzón Solariega" />
        <meta
            property="og:description"
            content="Canal interno de atención de Solariega Cenit para compartir comentarios y sugerencias de forma confidencial."
        />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="https://buzon.solariegacenit.com/" />
        <meta property="og:site_name" content="Buzón Solariega" />
    </Head>

    <div class="min-h-svh bg-background lg:grid lg:grid-cols-[58%_42%]">
        <!-- Left column: form -->
        <div
            class="flex flex-col justify-center px-4 py-10 sm:px-8 lg:px-14 lg:py-16"
        >
            <div
                class="mx-auto w-full max-w-xl animate-in duration-700 fade-in slide-in-from-bottom-4 lg:max-w-2xl"
            >
                <div
                    class="mb-6 rounded-2xl border border-gold/30 bg-gold/5 p-4 transition-shadow duration-300 hover:shadow-md hover:shadow-gold/10"
                >
                    <p class="text-sm font-medium">
                        Confidencialidad y seguimiento responsable
                    </p>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Tu información será tratada con confidencialidad por el
                        área de Recursos Humanos y, en caso necesario, con la
                        persona correspondiente para dar seguimiento adecuado.
                        El objetivo de este espacio es escuchar, atender y
                        mejorar, cuidando siempre el respeto hacia cada
                        colaborador.
                    </p>
                </div>

                <form class="space-y-5" @submit.prevent="submit">
                    <div
                        class="grid animate-in gap-5 duration-500 fade-in slide-in-from-bottom-2 sm:grid-cols-2"
                    >
                        <div class="space-y-2">
                            <Label for="full_name">Nombre completo</Label>
                            <Input
                                id="full_name"
                                v-model="form.full_name"
                                placeholder="Escribe tu nombre completo"
                                class="transition-colors hover:border-gold/60"
                            />
                            <InputError :message="form.errors.full_name" />
                        </div>

                        <div class="space-y-2">
                            <Label for="contact_info"
                                >Correo o teléfono de contacto</Label
                            >
                            <Input
                                id="contact_info"
                                v-model="form.contact_info"
                                placeholder="Ejemplo: correo@empresa.com o 777 123 4567"
                                class="transition-colors hover:border-gold/60"
                            />
                            <p class="text-xs text-muted-foreground">
                                Este dato será utilizado únicamente si
                                Recursos Humanos necesita comunicarse contigo
                                para dar seguimiento.
                            </p>
                            <InputError :message="form.errors.contact_info" />
                        </div>
                    </div>

                    <div
                        class="grid animate-in gap-5 delay-100 duration-500 fade-in slide-in-from-bottom-2 sm:grid-cols-2"
                    >
                        <div class="space-y-2">
                            <Label for="department">Área o departamento</Label>
                            <Input
                                id="department"
                                v-model="form.department"
                                placeholder="Ejemplo: Cocina, Recursos Humanos, Sistemas..."
                                class="transition-colors hover:border-gold/60"
                            />
                            <InputError :message="form.errors.department" />
                        </div>

                        <div class="space-y-2">
                            <Label>Tipo de mensaje</Label>
                            <Select v-model="form.request_type">
                                <SelectTrigger
                                    class="w-full transition-colors hover:border-gold/60"
                                >
                                    <SelectValue
                                        placeholder="Selecciona una opción"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="opt in options.requestTypes"
                                        :key="opt.value"
                                        :value="opt.value"
                                        >{{ opt.label }}</SelectItem
                                    >
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.request_type" />
                        </div>
                    </div>

                    <div
                        class="grid animate-in gap-5 delay-150 duration-500 fade-in slide-in-from-bottom-2 sm:grid-cols-2"
                    >
                        <div class="space-y-2">
                            <Label>Fecha aproximada</Label>
                            <DatePickerField
                                v-model="form.incident_date"
                                placeholder="Opcional"
                            />
                            <InputError :message="form.errors.incident_date" />
                        </div>

                        <div class="space-y-2">
                            <Label for="involved_people"
                                >Personas relacionadas</Label
                            >
                            <Input
                                id="involved_people"
                                v-model="form.involved_people"
                                placeholder="Opcional"
                                class="transition-colors hover:border-gold/60"
                            />
                            <InputError
                                :message="form.errors.involved_people"
                            />
                        </div>
                    </div>

                    <div
                        class="animate-in space-y-2 delay-200 duration-500 fade-in slide-in-from-bottom-2"
                    >
                        <Label for="description">Cuéntanos tu mensaje</Label>
                        <p class="text-xs text-muted-foreground">
                            Comparte con la mayor claridad posible lo que deseas
                            comunicar. Si lo consideras necesario, puedes
                            mencionar la fecha aproximada y las personas
                            relacionadas.
                        </p>
                        <Textarea
                            id="description"
                            v-model="form.description"
                            rows="8"
                            placeholder="Escribe aquí tu mensaje..."
                            class="transition-colors hover:border-gold/60"
                        />
                        <p class="text-xs text-muted-foreground">
                            Mínimo 20 caracteres.
                            {{ form.description.length }}/5000
                        </p>
                        <InputError :message="form.errors.description" />
                    </div>

                    <div
                        class="animate-in space-y-2 delay-300 duration-500 fade-in slide-in-from-bottom-2"
                    >
                        <Label>Adjuntar evidencia (opcional)</Label>
                        <p class="text-xs text-muted-foreground">
                            Si cuentas con capturas, fotos u otro documento,
                            compártelo aquí. No es obligatorio, pero ayuda a dar
                            mejor seguimiento a tu mensaje.
                        </p>
                        <FileDropzone v-model="form.attachments" />
                        <InputError :message="form.errors.attachments" />
                    </div>

                    <div
                        class="flex animate-in items-start gap-3 rounded-lg border bg-muted/40 p-4 transition-colors delay-300 duration-500 fade-in slide-in-from-bottom-2 hover:border-gold/40 hover:bg-gold/5"
                    >
                        <Checkbox
                            id="accepted_terms"
                            v-model="form.accepted_terms"
                            class="mt-0.5"
                        />
                        <Label
                            for="accepted_terms"
                            class="text-sm leading-relaxed font-normal"
                        >
                            Confirmo que la información proporcionada es
                            correcta según mi conocimiento y entiendo que será
                            tratada de forma confidencial por el área de
                            Recursos Humanos para su debida atención y
                            seguimiento.
                        </Label>
                    </div>
                    <InputError :message="form.errors.accepted_terms" />

                    <Button
                        type="submit"
                        class="w-full"
                        :disabled="!canSubmit || form.processing"
                    >
                        <Spinner v-if="form.processing" />
                        Enviar mensaje
                    </Button>
                </form>
            </div>
        </div>

        <!-- Right column: institutional visual block -->
        <div
            class="relative flex flex-col justify-between gap-10 overflow-hidden bg-[#171717] px-8 py-12 text-marfil sm:px-12 lg:min-h-svh lg:px-14 lg:py-16"
        >
            <div class="absolute inset-0">
                <div
                    class="size-full bg-gradient-to-br from-[#1E1E1E] via-[#232323] to-[#171717]"
                />
                <img
                    v-if="!heroImageMissing"
                    src="/images/buzon-hero.png"
                    alt=""
                    class="absolute inset-0 size-full object-cover"
                    @error="heroImageMissing = true"
                />
            </div>
            <div
                class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-black/10"
            />
            <div
                class="pointer-events-none absolute -top-16 -left-16 size-72 rounded-full bg-gold/15 blur-3xl"
            />
            <div
                class="pointer-events-none absolute inset-x-10 bottom-24 h-px bg-gradient-to-r from-transparent via-gold/40 to-transparent"
            />

            <div
                class="relative z-10 flex animate-in items-center justify-center duration-700 fade-in slide-in-from-top-4"
            >
                <img
                    src="/images/logoLetras.png"
                    alt="Solariega Cenit"
                    class="h-20 w-auto object-contain transition-transform duration-300 hover:scale-105 sm:h-28 lg:h-32 xl:h-40"
                />
            </div>

            <div
                class="relative z-10 max-w-md animate-in space-y-5 duration-700 fade-in slide-in-from-bottom-4"
            >
                <div class="space-y-2">
                    <h1 class="text-3xl font-semibold sm:text-4xl">
                        Buzón Solariega
                    </h1>
                    <p class="text-lg font-medium text-marfil/90">
                        Queremos escucharte
                    </p>
                    <p class="text-sm text-marfil/70">
                        Comparte tus comentarios, sugerencias o cualquier
                        situación que requiera atención. Cada mensaje será
                        tratado con seriedad, confidencialidad y respeto.
                    </p>
                </div>
            </div>

            <p class="relative z-10 text-xs text-marfil/50">
                © {{ new Date().getFullYear() }} Solariega Cenit
            </p>
        </div>
    </div>
</template>
