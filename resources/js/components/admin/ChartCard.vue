<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

defineProps<{
    title: string;
}>();
</script>

<template>
    <Card class="transition-shadow hover:shadow-md">
        <CardHeader>
            <CardTitle class="text-sm font-medium text-muted-foreground">{{
                title
            }}</CardTitle>
        </CardHeader>
        <CardContent class="h-64">
            <slot />
        </CardContent>
    </Card>
</template>
