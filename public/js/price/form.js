function formatRupiah(value) {
    if (!value) return '';
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
}

function attachRupiahFormatter(selector) {
    const input = document.querySelector(selector);
    if (!input) return;

    const update = () => {
        const digits = input.value.replace(/[^0-9]/g, '');
        input.value = formatRupiah(digits);
    };

    input.addEventListener('input', update);
    input.addEventListener('paste', () => setTimeout(update));

    const form = input.closest('form');
    if (form) {
        form.addEventListener('submit', () => {
            input.value = input.value.replace(/[^0-9]/g, '');
        });
    }
}