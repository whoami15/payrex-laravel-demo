<script setup>
import PayrexLogo from '@/components/PayrexLogo.vue';

defineProps({
    title: { type: String, default: undefined },
    description: { type: String, default: undefined },
});
</script>

<template>
    <div
        class="login-bg flex min-h-svh flex-col items-center justify-center p-6 md:p-10"
    >
        <div class="w-full max-w-sm">
            <div class="animate-fade-in-up flex flex-col items-center gap-10">
                <div class="w-full space-y-2 text-center">
                    <h1 class="text-2xl font-semibold tracking-tight">
                        {{ title }}
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        {{ description }}
                    </p>
                </div>
                <div class="w-full">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.login-bg {
    background-color: var(--background);
    position: relative;
    overflow: hidden;
}

.login-bg::before {
    content: '';
    position: absolute;
    top: -20%;
    right: -10%;
    width: 600px;
    height: 600px;
    background: radial-gradient(
        ellipse at center,
        rgba(106, 99, 239, 0.06) 0%,
        transparent 70%
    );
    pointer-events: none;
}

.login-bg::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -15%;
    width: 500px;
    height: 500px;
    background: radial-gradient(
        ellipse at center,
        rgba(0, 150, 255, 0.04) 0%,
        transparent 70%
    );
    pointer-events: none;
}

.dark .login-bg::before {
    background: radial-gradient(
        ellipse at center,
        rgba(106, 99, 239, 0.12) 0%,
        transparent 70%
    );
}

.dark .login-bg::after {
    background: radial-gradient(
        ellipse at center,
        rgba(0, 150, 255, 0.08) 0%,
        transparent 70%
    );
}
</style>
