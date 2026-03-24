import { usePage } from '@inertiajs/vue3';
import { computed, readonly } from 'vue';
import { toUrl } from '@/lib/utils';

const page = usePage();
const currentUrlReactive = computed(
    () =>
        new URL(
            page.url,
            typeof window !== 'undefined'
                ? window.location.origin
                : 'http://localhost',
        ).pathname,
);

export function useCurrentUrl() {
    function isCurrentUrl(urlToCheck, currentUrl) {
        const urlToCompare = currentUrl ?? currentUrlReactive.value;
        const urlString = toUrl(urlToCheck);

        if (!urlString.startsWith('http')) {
            return urlString === urlToCompare;
        }

        try {
            const absoluteUrl = new URL(urlString);

            return absoluteUrl.pathname === urlToCompare;
        } catch {
            return false;
        }
    }

    function whenCurrentUrl(urlToCheck, ifTrue, ifFalse = null) {
        return isCurrentUrl(urlToCheck) ? ifTrue : ifFalse;
    }

    return {
        currentUrl: readonly(currentUrlReactive),
        isCurrentUrl,
        whenCurrentUrl,
    };
}
