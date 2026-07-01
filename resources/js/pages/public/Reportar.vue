<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, ShieldCheck } from '@lucide/vue';
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

const steps = [
    'Tipo de solicitud',
    'Tus datos',
    'Descripción',
    'Urgencia y evidencia',
    'Seguimiento',
];

const stepFields: string[][] = [
    ['request_type', 'is_anonymous'],
    ['full_name', 'department', 'location', 'incident_date'],
    ['description', 'involved_people'],
    ['urgency_level', 'has_evidence', 'attachments'],
    ['wants_follow_up', 'contact_info', 'accepted_terms'],
];

const currentStep = ref(0);

const yesNoOptions = [
    { value: '1', label: 'Sí' },
    { value: '0', label: 'No' },
];

const anonymousOptions = [
    { value: '1', label: 'Sí, deseo permanecer anónimo' },
    { value: '0', label: 'No, deseo proporcionar mis datos para seguimiento' },
];

const form = useForm({
    request_type: '',
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
            return form.request_type !== '' && form.is_anonymous !== '';
        case 1:
            return form.department !== '' && form.location.trim() !== '';
        case 2:
            return form.description.trim().length >= 20;
        case 3:
            return form.urgency_level !== '' && form.has_evidence !== '';
        case 4:
            return form.wants_follow_up !== '' && form.accepted_terms;
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
    <Head title="Buzón de Quejas, Sugerencias y Reportes" />

    <PublicPageShell>
        <template #title>Buzón de Quejas, Sugerencias y Reportes</template>

        <p class="mx-auto mb-8 max-w-xl text-center text-sm text-emerald-50/90">
            Este formulario tiene como finalidad recibir quejas, sugerencias,
            reportes o comentarios relacionados con el ambiente laboral,
            procesos internos, condiciones de trabajo o cualquier situación que
            requiera atención. La información será revisada de manera
            confidencial por el área correspondiente. Puedes enviar tu reporte
            de forma anónima o proporcionar tus datos si deseas recibir
            seguimiento.
        </p>

        <Card class="mx-auto w-full max-w-2xl shadow-xl">
            <CardHeader class="space-y-4 pb-2">
                <StepProgress :steps="steps" :current-step="currentStep" />
            </CardHeader>

            <CardContent class="pt-4">
                <form
                    class="space-y-6"
                    @submit.prevent="
                        currentStep === steps.length - 1 ? submit() : goNext()
                    "
                >
                    <!-- Step 0 -->
                    <div
                        v-if="currentStep === 0"
                        class="animate-in space-y-6 duration-300 fade-in slide-in-from-right-2"
                    >
                        <div class="space-y-2">
                            <Label>Tipo de solicitud</Label>
                            <OptionCardGroup
                                v-model="form.request_type"
                                :options="options.requestTypes"
                                :columns="2"
                            />
                            <InputError :message="form.errors.request_type" />
                        </div>

                        <div class="space-y-2">
                            <Label>¿Deseas que tu solicitud sea anónima?</Label>
                            <OptionCardGroup
                                v-model="form.is_anonymous"
                                :options="anonymousOptions"
                                :columns="1"
                            />
                            <InputError :message="form.errors.is_anonymous" />
                        </div>
                    </div>

                    <!-- Step 1 -->
                    <div
                        v-else-if="currentStep === 1"
                        class="animate-in space-y-6 duration-300 fade-in slide-in-from-right-2"
                    >
                        <div v-if="!isAnonymous" class="space-y-2">
                            <Label for="full_name">Nombre completo</Label>
                            <Input
                                id="full_name"
                                v-model="form.full_name"
                                placeholder="Escribe tu nombre completo"
                            />
                            <p class="text-xs text-muted-foreground">
                                Llena este campo solo si deseas seguimiento
                                directo.
                            </p>
                            <InputError :message="form.errors.full_name" />
                        </div>

                        <div class="space-y-2">
                            <Label>Área o departamento</Label>
                            <OptionCardGroup
                                v-model="form.department"
                                :options="options.departments"
                                :columns="2"
                            />
                            <InputError :message="form.errors.department" />
                        </div>

                        <div class="space-y-2">
                            <Label for="location"
                                >Sucursal, planta o ubicación</Label
                            >
                            <Input
                                id="location"
                                v-model="form.location"
                                placeholder="Ejemplo: Solariega Cenit, planta principal, oficinas, almacén, etc."
                            />
                            <InputError :message="form.errors.location" />
                        </div>

                        <div class="space-y-2">
                            <Label>Fecha aproximada del hecho</Label>
                            <DatePickerField
                                v-model="form.incident_date"
                                placeholder="Selecciona una fecha (opcional)"
                            />
                            <InputError :message="form.errors.incident_date" />
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div
                        v-else-if="currentStep === 2"
                        class="animate-in space-y-6 duration-300 fade-in slide-in-from-right-2"
                    >
                        <div class="space-y-2">
                            <Label for="description"
                                >Describe tu solicitud</Label
                            >
                            <Textarea
                                id="description"
                                v-model="form.description"
                                rows="6"
                                placeholder="Describe qué ocurrió, cuándo sucedió, dónde fue y quiénes estuvieron involucrados, si aplica."
                            />
                            <p class="text-xs text-muted-foreground">
                                Mínimo 20 caracteres.
                                {{ form.description.length }}/5000
                            </p>
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="space-y-2">
                            <Label for="involved_people"
                                >Personas involucradas</Label
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

                    <!-- Step 3 -->
                    <div
                        v-else-if="currentStep === 3"
                        class="animate-in space-y-6 duration-300 fade-in slide-in-from-right-2"
                    >
                        <div class="space-y-2">
                            <Label>Nivel de urgencia</Label>
                            <UrgencyLevelPicker
                                v-model="form.urgency_level"
                                :options="options.urgencyLevels"
                            />
                            <InputError :message="form.errors.urgency_level" />
                        </div>

                        <div class="space-y-2">
                            <Label>¿Cuentas con evidencia?</Label>
                            <OptionCardGroup
                                v-model="form.has_evidence"
                                :options="yesNoOptions"
                                :columns="2"
                            />
                            <InputError :message="form.errors.has_evidence" />
                        </div>

                        <div class="space-y-2">
                            <Label>Adjuntar evidencia</Label>
                            <FileDropzone v-model="form.attachments" />
                            <InputError :message="form.errors.attachments" />
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div
                        v-else
                        class="animate-in space-y-6 duration-300 fade-in slide-in-from-right-2"
                    >
                        <div class="space-y-2">
                            <Label>¿Deseas recibir seguimiento?</Label>
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
                                >Correo o teléfono de contacto</Label
                            >
                            <Input
                                id="contact_info"
                                v-model="form.contact_info"
                                placeholder="correo@ejemplo.com o número telefónico"
                            />
                            <p class="text-xs text-muted-foreground">
                                Solo llena este campo si deseas que se te
                                contacte para seguimiento.
                            </p>
                            <InputError :message="form.errors.contact_info" />
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
                                Confirmo que la información proporcionada es
                                verdadera según mi conocimiento y autorizo que
                                sea revisada de manera confidencial para su
                                seguimiento interno.
                            </Label>
                        </div>
                        <InputError :message="form.errors.accepted_terms" />

                        <div
                            class="flex items-center gap-2 rounded-lg bg-emerald-50 p-3 text-xs text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200"
                        >
                            <ShieldCheck class="size-4 shrink-0" />
                            Tu información se maneja de forma confidencial por
                            el área correspondiente.
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
                            Enviar solicitud
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </PublicPageShell>
</template>
