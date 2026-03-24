<!--
Displays a syntax-highlighted code block with copy button.

Usage with prop:
    <CodeBlock code="$user->createAsPayrexCustomer();" />
    <CodeBlock lang="bash" code="php artisan migrate" />

Usage with textarea slot (code containing HTML tags, mixed quotes, etc.):
    <CodeBlock>
        <textarea>
            import { Form } from '@inertiajs/vue3'
        </textarea>
    </CodeBlock>
-->

<script setup>
import { Check, Copy } from 'lucide-vue-next';
import { computed, ref, shallowRef, useSlots, watch } from 'vue';
import { getHighlighter } from '@/lib/shiki';

const props = defineProps({
    code: { type: String, default: undefined },
    lang: { type: String, default: undefined },
    title: { type: String, default: undefined },
});

const slots = useSlots();
const slotRef = ref(null);
const slotCode = computed(() => slotRef.value?.textContent ?? '');
const copied = ref(false);
const highlighted = shallowRef('');

function dedent(raw) {
    const lines = raw.replace(/^\n/, '').replace(/\s+$/, '').split('\n');
    const indent = Math.min(
        ...lines
            .filter((l) => l.trim())
            .map((l) => l.match(/^(\s*)/)?.[1].length ?? 0),
    );

    return lines.map((l) => l.slice(indent)).join('\n');
}

function detectLang(code) {
    if (props.lang) return props.lang;
    const trimmed = code.trim();
    if (
        trimmed.startsWith('<?php') ||
        trimmed.startsWith('use ') ||
        trimmed.startsWith('$') ||
        trimmed.includes('->') ||
        trimmed.includes('::') ||
        trimmed.includes('function ')
    )
        return 'php';
    if (
        trimmed.startsWith('#') ||
        trimmed.startsWith('php artisan') ||
        trimmed.startsWith('composer ')
    )
        return 'bash';
    if (
        trimmed.startsWith('const ') ||
        trimmed.startsWith('let ') ||
        trimmed.startsWith('import ') ||
        trimmed.includes('=> {')
    )
        return 'javascript';
    return 'plaintext';
}

async function highlight(raw) {
    const code = dedent(raw);
    const lang = detectLang(code);

    try {
        const highlighter = await getHighlighter();
        highlighted.value = highlighter.codeToHtml(code, {
            lang,
            theme: 'material-theme-palenight',
        });
    } catch {
        highlighted.value = '';
    }
}

const rawCode = computed(() => props.code ?? slotCode.value);

watch(
    rawCode,
    (val) => {
        if (val) highlight(val);
    },
    { immediate: true },
);

function copy() {
    navigator.clipboard.writeText(dedent(rawCode.value));
    copied.value = true;
    setTimeout(() => (copied.value = false), 2000);
}
</script>

<template>
    <div
        class="group contain-[inline-size] overflow-hidden rounded-xl border border-[#292D3E] bg-[#292D3E]"
    >
        <p
            v-if="title"
            class="border-b border-[#343A50] bg-[#252937] px-4 pt-3 pb-2 font-sans text-[0.7rem] font-semibold tracking-wider text-neutral-400 uppercase"
        >
            {{ title }}
        </p>
        <div class="relative">
            <button
                type="button"
                class="absolute top-2.5 right-2.5 z-10 rounded-md p-1.5 text-neutral-400 opacity-0 transition-all duration-200 group-hover:opacity-100 hover:bg-white/10 hover:text-white"
                @click="copy"
            >
                <Check v-if="copied" class="size-3.5 text-emerald-400" />
                <Copy v-else class="size-3.5" />
            </button>
            <div
                v-if="highlighted"
                class="shiki-wrapper overflow-auto px-4 py-3 text-xs leading-relaxed"
                v-html="highlighted"
            />
            <pre
                v-else
                class="overflow-auto bg-[#292D3E] px-4 py-3 text-xs leading-relaxed text-neutral-300"
                >{{ dedent(rawCode) }}</pre
            >
        </div>
        <span v-if="slots.default" ref="slotRef" hidden><slot /></span>
    </div>
</template>

<style>
.shiki-wrapper pre {
    margin: 0;
    padding: 0;
    background: transparent !important;
}

.shiki-wrapper code {
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
}

.shiki-wrapper .shiki {
    background-color: transparent !important;
}
</style>
