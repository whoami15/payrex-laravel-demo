import { createHighlighter } from 'shiki';

let highlighter = null;
let promise = null;

export function getHighlighter() {
    if (!promise) {
        promise = createHighlighter({
            themes: ['material-theme-palenight'],
            langs: ['php', 'javascript', 'json', 'bash', 'plaintext'],
        }).then((h) => {
            highlighter = h;
            return h;
        });
    }
    return promise;
}
