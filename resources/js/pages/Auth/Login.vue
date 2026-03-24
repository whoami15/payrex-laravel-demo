<script setup>
import { Form, Head } from '@inertiajs/vue3';
import { ArrowRight, Lock, Mail } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';

defineProps({
    status: {
        type: String,
        default: undefined,
    },
});
</script>

<template>
    <AuthBase
        title="PayRex Laravel Demo"
        description="Explore every feature of the PayRex Laravel package."
    >
        <Head title="Log in" />

        <div
            v-if="status"
            class="mb-4 rounded-lg bg-emerald-50 p-3 text-center text-sm font-medium text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300"
        >
            {{ status }}
        </div>

        <Form
            method="post"
            action="/login"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-5">
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <div class="relative">
                        <Mail
                            class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground/40"
                        />
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            placeholder="email@example.com"
                            default-value="test@payrexlaravel.com"
                            class="pl-10"
                        />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <div class="relative">
                        <Lock
                            class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground/40"
                        />
                        <Input
                            id="password"
                            type="password"
                            name="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            placeholder="Password"
                            default-value="raw15UW^1V3TL8?Bv8qa"
                            class="pl-10"
                        />
                    </div>
                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" name="remember" :tabindex="3" />
                        <span>Remember me</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="w-full"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <Spinner v-if="processing" />
                    <template v-else>
                        Log in
                        <ArrowRight class="size-4" />
                    </template>
                </Button>
            </div>
            <div
                class="rounded-lg border border-dashed p-2 text-center text-xs text-muted-foreground"
            >
                <p class="text-center text-xs text-muted-foreground">
                    Made with
                    <span class="text-red-500">&hearts;</span>
                    by
                    <a
                        href="https://github.com/whoami15"
                        target="_blank"
                        class="font-medium text-foreground underline-offset-4 hover:underline"
                        >Daryl Legion</a
                    >
                </p>
            </div>
        </Form>
    </AuthBase>
</template>
