<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    HeartHandshake,
    ShieldCheck,
    Sparkles,
} from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import DatePickerField from '@/components/public/DatePickerField.vue';
import FileDropzone from '@/components/public/FileDropzone.vue';
import OptionCardGroup from '@/components/public/OptionCardGroup.vue';
import PublicPageShell from '@/components/public/PublicPageShell.vue';
import StepProgress from '@/components/public/StepProgress.vue';
import UrgencyLevelPicker from '@/components/public/UrgencyLevelPicker.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { store } from '@/routes/reportar';
import type { FormOptions } from '@/types/buzon';

defineProps<{
    options: FormOptions;
}>();

const steps = ['Tu mensaje', 'Detalles', 'Seguimiento'];

const stepFields: string[][] = [
    [
        'request_type',
        'sender_type',
        'is_anonymous',
        'full_name',
        'wants_follow_up',
        'contact_info',
    ],
    ['department', 'location', 'incident_date', 'description', 'involved_people'],
    ['urgency_level', 'has_evidence', 'attachments', 'accepted_terms'],
];

const currentStep = ref(0);

const yesNoOptions = [
    { value: '1', label: 'Sí' },
    { value: '0', label: 'No' },
];

const anonymousOptions = [
    { value: '1', label: 'Sí, deseo mantener mi identidad en reserva' },
    { value: '0', label: 'No, deseo dejar mis datos para seguimiento' },
];

const form = useForm({
    request_type: '',
    sender_type: '',
    is_anonymous: '',
    full_name: '',
    department: '',
    location: '',
    incident_date: null as string | null,
    description: '',
    involved_people: '',
    urgency_level: '',
    has_evidence: '',
    wants_follow_up: '',
    contact_info: '',
    accepted_terms: false,
    attachments: [] as File[],
});

const isAnonymous = computed(() => form.is_anonymous === '1');
const wantsFollowUp = computed(() => form.wants_follow_up === '1');

watch(isAnonymous, (anonymous) => {
    if (anonymous) {
        form.full_name = '';
        form.contact_info = '';
    }
});

watch(wantsFollowUp, (wants) => {
    if (!wants) {
        form.contact_info = '';
    }
});

const stepIsValid = computed(() => {
    switch (currentStep.value) {
        case 0:
            return (
                form.request_type !== '' &&
                form.sender_type !== '' &&
                form.is_anonymous !== '' &&
                form.wants_follow_up !== ''
            );
        case 1:
            return (
                form.department !== '' &&
                form.location.trim() !== '' &&
                form.description.trim().length >= 20
            );
        case 2:
            return (
                form.urgency_level !== '' &&
                form.has_evidence !== '' &&
                form.accepted_terms
            );
        default:
            return true;
    }
});

function fieldStep(field: string): number {
    return stepFields.findIndex((fields) => fields.includes(field));
}

function goNext() {
    if (currentStep.value < steps.length - 1) {
        currentStep.value += 1;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function goBack() {
    if (currentStep.value > 0) {
        currentStep.value -= 1;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function submit() {
    form.post(store.url(), {
        forceFormData: true,
        onError: (errors) => {
            const firstField = Object.keys(errors)[0];
            const step = firstField ? fieldStep(firstField.split('.')[0]) : -1;

            if (step >= 0) {
                currentStep.value = step;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
    });
}
</script>

<template>
    <Head>
        <meta
            name="description"
            content="Canal de atención de Solariega Cenit para recibir comentarios, sugerencias y mensajes de seguimiento de forma segura y organizada."
        />
        <meta
            name="keywords"
            content="Buzón Solariega, Solariega Cenit, atención, comentarios, sugerencias, seguimiento, experiencia, mejora continua"
        />
        <meta name="robots" content="index, follow" />
        <link rel="canonical" href="https://buzon.solariegacenit.com/reportar" />

        <meta property="og:title" content="Buzón Solariega" />
        <meta
            property="og:description"
            content="Canal de atención de Solariega Cenit para compartir comentarios, sugerencias y mensajes de seguimiento."
        />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="https://buzon.solariegacenit.com/reportar" />
        <meta property="og:site_name" content="Buzón Solariega" />

        <meta name="twitter:card" content="summary" />
        <meta name="twitter:title" content="Buzón Solariega" />
        <meta
            name="twitter:description"
            content="Canal de atención de Solariega Cenit para compartir comentarios, sugerencias y mensajes de seguimiento."
        />
    </Head>

    <PublicPageShell content-class="max-w-6xl">
        <template #title>Buzón Solariega</template>
        <template #subtitle
            >Un espacio para compartir comentarios, sugerencias o situaciones
            que requieran atención.</template
        >

        <p
            class="mx-auto mb-6 max-w-xl text-center text-sm text-muted-foreground"
        >
            Queremos escucharte y dar seguimiento a cada mensaje de forma
            responsable, respetuosa y confidencial.
        </p>

        <div class="mb-6 flex justify-center">
            <span
                class="inline-flex items-center gap-1.5 rounded-full border border-gold/40 bg-gold/10 px-4 py-1.5 text-xs font-medium tracking-wide text-gold-foreground uppercase"
            >
                Canal de atención
            </span>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.6fr)]">
            <!-- Left column: premium visual block -->
            <div class="lg:sticky lg:top-8 lg:self-start">
                <div
                    class="relative overflow-hidden rounded-2xl border border-[#1b3226] bg-gradient-to-br from-[#10251B] via-[#152a1f] to-[#0c1712] p-7 text-marfil shadow-2xl shadow-black/20"
                >
                    <div
                        class="pointer-events-none absolute -top-10 -right-10 size-40 rounded-full bg-gold/15 blur-2xl"
                    />
                    <div
                        class="pointer-events-none absolute inset-x-6 bottom-6 h-px bg-gradient-to-r from-transparent via-gold/40 to-transparent"
                    />

                    <div
                        class="mb-5 flex size-11 items-center justify-center rounded-xl bg-gold/15 text-gold ring-1 ring-gold/40"
                    >
                        <HeartHandshake class="size-5" />
                    </div>

                    <h2 class="text-lg font-semibold">
                        Tu opinión nos ayuda a mejorar
                    </h2>
                    <p class="mt-2 text-sm text-marfil/80">
                        Cada mensaje es revisado con seriedad para fortalecer
                        la experiencia de colaboradores, clientes y
                        visitantes.
                    </p>

                    <ul class="mt-6 space-y-3 text-sm">
                        <li class="flex items-center gap-2.5">
                            <ShieldCheck class="size-4 shrink-0 text-gold" />
                            Atención confidencial
                        </li>
                        <li class="flex items-center gap-2.5">
                            <Sparkles class="size-4 shrink-0 text-gold" />
                            Seguimiento ordenado
                        </li>
                        <li class="flex items-center gap-2.5">
                            <HeartHandshake class="size-4 shrink-0 text-gold" />
                            Mejora continua
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right column: form -->
            <Card
                class="w-full animate-in rounded-2xl border-none bg-card/90 shadow-2xl shadow-black/10 backdrop-blur duration-700 fade-in slide-in-from-bottom-4"
            >
                <CardHeader class="space-y-4 pb-2">
                    <StepProgress :steps="steps" :current-step="currentStep" />
                </CardHeader>

                <CardContent class="pt-4">
                    <form
                        class="space-y-6"
                        @submit.prevent="
                            currentStep === steps.length - 1
                                ? submit()
                                : goNext()
                        "
                    >
                        <!-- Section 0: Tu mensaje -->
                        <div
                            v-if="currentStep === 0"
                            class="animate-in space-y-6 duration-300 fade-in slide-in-from-right-2"
                        >
                            <div class="space-y-2">
                                <Label>Tipo de mensaje</Label>
                                <OptionCardGroup
                                    v-model="form.request_type"
                                    :options="options.requestTypes"
                                    :columns="2"
                                />
                                <InputError
                                    :message="form.errors.request_type"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label>¿Quién envía el mensaje?</Label>
                                <OptionCardGroup
                                    v-model="form.sender_type"
                                    :options="options.senderTypes"
                                    :columns="3"
                                />
                                <InputError
                                    :message="form.errors.sender_type"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label
                                    >¿Quieres mantener tus datos en
                                    reserva?</Label
                                >
                                <OptionCardGroup
                                    v-model="form.is_anonymous"
                                    :options="anonymousOptions"
                                    :columns="1"
                                />
                                <InputError
                                    :message="form.errors.is_anonymous"
                                />
                            </div>

                            <div v-if="!isAnonymous" class="space-y-2">
                                <Label for="full_name"
                                    >Nombre, si aplica</Label
                                >
                                <Input
                                    id="full_name"
                                    v-model="form.full_name"
                                    placeholder="Escribe tu nombre completo"
                                />
                                <InputError
                                    :message="form.errors.full_name"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label>¿Quieres recibir seguimiento?</Label>
                                <OptionCardGroup
                                    v-model="form.wants_follow_up"
                                    :options="yesNoOptions"
                                    :columns="2"
                                />
                                <InputError
                                    :message="form.errors.wants_follow_up"
                                />
                            </div>

                            <div
                                v-if="wantsFollowUp && !isAnonymous"
                                class="space-y-2"
                            >
                                <Label for="contact_info"
                                    >Contacto, si aplica</Label
                                >
                                <Input
                                    id="contact_info"
                                    v-model="form.contact_info"
                                    placeholder="correo@ejemplo.com o número telefónico"
                                />
                                <InputError
                                    :message="form.errors.contact_info"
                                />
                            </div>
                        </div>

                        <!-- Section 1: Detalles -->
                        <div
                            v-else-if="currentStep === 1"
                            class="animate-in space-y-6 duration-300 fade-in slide-in-from-right-2"
                        >
                            <div class="space-y-2">
                                <Label>Área o categoría</Label>
                                <OptionCardGroup
                                    v-model="form.department"
                                    :options="options.departments"
                                    :columns="2"
                                />
                                <InputError :message="form.errors.department" />
                            </div>

                            <div class="space-y-2">
                                <Label for="location"
                                    >Ubicación, sucursal o área</Label
                                >
                                <Input
                                    id="location"
                                    v-model="form.location"
                                    placeholder="Ejemplo: Solariega Cenit, planta principal, oficinas, almacén, etc."
                                />
                                <InputError :message="form.errors.location" />
                            </div>

                            <div class="space-y-2">
                                <Label>Fecha aproximada</Label>
                                <DatePickerField
                                    v-model="form.incident_date"
                                    placeholder="Selecciona una fecha (opcional)"
                                />
                                <InputError
                                    :message="form.errors.incident_date"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="description"
                                    >Cuéntanos tu mensaje</Label
                                >
                                <p class="text-xs text-muted-foreground">
                                    Describe la situación con el mayor
                                    detalle posible: qué pasó, cuándo
                                    ocurrió, dónde sucedió y quiénes
                                    estuvieron involucrados, si aplica.
                                </p>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="5"
                                    placeholder="Escribe aquí los detalles de tu mensaje..."
                                />
                                <p class="text-xs text-muted-foreground">
                                    Mínimo 20 caracteres.
                                    {{ form.description.length }}/5000
                                </p>
                                <InputError
                                    :message="form.errors.description"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="involved_people"
                                    >Personas relacionadas, si aplica</Label
                                >
                                <Textarea
                                    id="involved_people"
                                    v-model="form.involved_people"
                                    rows="3"
                                    placeholder="Puedes dejarlo en blanco si no deseas mencionar nombres."
                                />
                                <InputError
                                    :message="form.errors.involved_people"
                                />
                            </div>
                        </div>

                        <!-- Section 2: Seguimiento -->
                        <div
                            v-else
                            class="animate-in space-y-6 duration-300 fade-in slide-in-from-right-2"
                        >
                            <div class="space-y-2">
                                <Label
                                    >¿Qué tan urgente consideras esta
                                    situación?</Label
                                >
                                <UrgencyLevelPicker
                                    v-model="form.urgency_level"
                                    :options="options.urgencyLevels"
                                />
                                <InputError
                                    :message="form.errors.urgency_level"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label>¿Cuentas con evidencia?</Label>
                                <OptionCardGroup
                                    v-model="form.has_evidence"
                                    :options="yesNoOptions"
                                    :columns="2"
                                />
                                <InputError
                                    :message="form.errors.has_evidence"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label>Adjuntar archivos</Label>
                                <FileDropzone v-model="form.attachments" />
                                <InputError
                                    :message="form.errors.attachments"
                                />
                            </div>

                            <div
                                class="flex items-start gap-3 rounded-lg border bg-muted/40 p-4"
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
                                    Confirmo que la información proporcionada
                                    es verdadera según mi conocimiento y
                                    autorizo que sea revisada de manera
                                    confidencial para su seguimiento interno.
                                </Label>
                            </div>
                            <InputError
                                :message="form.errors.accepted_terms"
                            />

                            <div
                                class="flex items-center gap-2 rounded-lg bg-primary/5 p-3 text-xs text-primary"
                            >
                                <ShieldCheck class="size-4 shrink-0" />
                                La información será revisada únicamente por
                                el área correspondiente para brindar
                                seguimiento adecuado.
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between gap-3 border-t pt-5"
                        >
                            <Button
                                v-if="currentStep > 0"
                                type="button"
                                variant="outline"
                                @click="goBack"
                            >
                                <ArrowLeft class="size-4" />
                                Atrás
                            </Button>
                            <div v-else />

                            <Button
                                v-if="currentStep < steps.length - 1"
                                type="submit"
                                :disabled="!stepIsValid"
                            >
                                Siguiente
                                <ArrowRight class="size-4" />
                            </Button>
                            <Button
                                v-else
                                type="submit"
                                :disabled="!stepIsValid || form.processing"
                            >
                                <Spinner v-if="form.processing" />
                                Enviar mensaje
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </PublicPageShell>
</template>
