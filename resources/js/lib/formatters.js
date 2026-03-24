/**
 * Format an amount from centavos to Philippine Peso display string.
 *
 * @param {number} amount - Amount in centavos
 * @returns {string} Formatted amount (e.g., "₱1,500.00")
 */
export function formatAmount(amount) {
    return `₱${(amount / 100).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
}

/**
 * Format a Unix timestamp to a localized date/time string.
 *
 * @param {number|null} ts - Unix timestamp in seconds
 * @returns {string} Formatted date or "—" if null
 */
export function formatTimestamp(ts) {
    if (!ts) return '—';
    return new Date(ts * 1000).toLocaleString();
}

/**
 * Format a peso amount (as a string or number) to a display string.
 * Unlike formatAmount, this takes pesos directly (not centavos).
 *
 * @param {string|number} value - Amount in pesos (e.g., "1000.00" or 1000)
 * @returns {string} Formatted amount (e.g., "₱1,000.00") or empty string if invalid
 */
export function formatPesos(value) {
    const val = parseFloat(value);
    if (isNaN(val)) return '';
    return `₱${val.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
}

/**
 * Convert a snake_case string to Title Case.
 *
 * @param {string|null} value - Snake case string (e.g., "requested_by_customer")
 * @returns {string} Title case string (e.g., "Requested By Customer") or "—" if null
 */
export function formatSnakeCase(value) {
    if (!value) return '—';
    return value.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
}

/**
 * Convert a JavaScript object to a PHP array string representation.
 *
 * @param {object} params - The object to convert
 * @returns {string} PHP array syntax (e.g., "['key' => 'value']")
 */
export function toPhpArray(params) {
    let result = JSON.stringify(params, null, 4)
        .replace(/"([^"]+)":/g, "'$1' =>")
        .replace(/"/g, "'")
        .replace(/null/g, 'null')
        .replace(/\{/g, '[')
        .replace(/\}/g, ']');

    // Add trailing commas — run multiple times for nested brackets
    let prev;
    do {
        prev = result;
        result = result.replace(/([^,\s])\n(\s*])/g, '$1,\n$2');
    } while (result !== prev);

    return result;
}
