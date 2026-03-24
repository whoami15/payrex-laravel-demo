<script setup>
import { usePage } from '@inertiajs/vue3';
import { BookOpen, Code, ExternalLink } from 'lucide-vue-next';
import { markRaw } from 'vue';

const props = defineProps({
    docs: { type: String, default: undefined },
    controller: { type: String, default: undefined },
});

const page = usePage();
const componentPath = `resources/js/pages/${page.component}.vue`;

const isDeployed = typeof __PROJECT_ROOT__ === 'undefined';

function url(path) {
    if (!isDeployed) {
        const [file, line] = path.split('#L');
        return `vscode://file/${__PROJECT_ROOT__}/${file}${line ? `:${line}` : ''}`;
    }

    return `https://github.com/whoami15/payrex-laravel-demo/blob/main/${path}`;
}

const links = [
    props.docs
        ? {
              label: 'Docs',
              href: `https://payrexlaravel.com/${props.docs}`,
              external: true,
              icon: markRaw(BookOpen),
          }
        : null,
    {
        label: 'Vue Page',
        href: url(componentPath),
        external: isDeployed,
        icon: markRaw(Code),
    },
    props.controller
        ? {
              label: 'Controller',
              href: url(props.controller),
              external: isDeployed,
              icon: markRaw(Code),
          }
        : null,
].filter(Boolean);
</script>

<template>
    <div class="mt-3 flex flex-wrap items-center gap-2">
        <a
            v-for="link in links"
            :key="link.label"
            :href="link.href"
            :target="link.external ? '_blank' : undefined"
            class="inline-flex items-center gap-1.5 rounded-lg border bg-muted px-3 py-1.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-mauve-50 hover:text-foreground"
        >
            <component :is="link.icon" class="size-3" />
            {{ link.label }}
            <ExternalLink v-if="link.external" class="size-3 opacity-70" />
        </a>
    </div>
</template>
