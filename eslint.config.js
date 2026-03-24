import prettier from 'eslint-config-prettier';
import importPlugin from 'eslint-plugin-import';
import vue from 'eslint-plugin-vue';

export default [
    ...vue.configs['flat/essential'],
    {
        ignores: ['vendor', 'node_modules', 'public', 'bootstrap/ssr', 'vite.config.js', 'resources/js/components/ui/*'],
    },
    {
        plugins: {
            import: importPlugin,
        },
        rules: {
            'vue/multi-word-component-names': 'off',
            'import/order': [
                'error',
                {
                    groups: ['builtin', 'external', 'internal', 'parent', 'sibling', 'index'],
                    alphabetize: {
                        order: 'asc',
                        caseInsensitive: true,
                    },
                },
            ],
        },
    },
    prettier,
];
