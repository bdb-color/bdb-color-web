// main.js - fungsionalitas sederhana
document.addEventListener('DOMContentLoaded', () => {
    console.log("JS aktif! Selamat coding 🎉");

    // Contoh interaksi: konfirmasi hapus
    const links = document.querySelectorAll('a[href*="delete"], a[href*="remove"]');
    links.forEach(link => {
        link.addEventListener('click', (e) => {
            if (!confirm('Yakin mau hapus item ini?')) {
                e.preventDefault();
            }
        });
    });
});
