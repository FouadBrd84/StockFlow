{{-- Applique le thème choisi (Neo, Prestige, Champions) avant l'affichage, pour éviter un flash au chargement --}}
<script>
    (function () {
        var theme = 'neo';
        try { theme = localStorage.getItem('sf-theme') || 'neo'; } catch (e) {}
        if (['neo', 'prestige', 'champions'].indexOf(theme) === -1) { theme = 'neo'; }
        document.documentElement.setAttribute('data-theme', theme);
        document.documentElement.setAttribute('data-bs-theme', theme === 'neo' ? 'light' : 'dark');
    })();
</script>
