</main>

<footer class="text-center text-muted py-4 border-top">
    <small>🐾 Salsicha Lanches &middot; Cozinha</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Relógio no canto direito da navbar
    function atualizarRelogio() {
        const agora = new Date();
        const hh = String(agora.getHours()).padStart(2, '0');
        const mm = String(agora.getMinutes()).padStart(2, '0');
        const ss = String(agora.getSeconds()).padStart(2, '0');
        const el = document.getElementById('relogio');
        if (el) el.textContent = `${hh}:${mm}:${ss}`;
    }
    atualizarRelogio();
    setInterval(atualizarRelogio, 1000);
</script>

</body>
</html>
