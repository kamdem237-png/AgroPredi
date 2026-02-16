export function getAppProps() {
    if (typeof window === 'undefined') return {};
    return window.__APP_PROPS__ || {};
}

export function getCsrfToken() {
    const props = getAppProps();
    return props.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}
