/**
 * StockFlow — animations du thème « Neo ».
 * Compteurs animés, apparition en cascade, cartes 3D, effet d'onde et confettis.
 * Tout est désactivé si l'utilisateur préfère réduire les animations ou active le contraste élevé.
 */
(function () {
    const calme = () =>
        window.matchMedia('(prefers-reduced-motion: reduce)').matches ||
        document.body.classList.contains('contraste-eleve');

    /** Graphiques Chart.js : même police et couleurs douces que le thème. */
    if (window.Chart) {
        Chart.defaults.font.family = "'Outfit', 'Segoe UI', sans-serif";
        Chart.defaults.color = '#64748b';
        Chart.defaults.plugins.legend.labels.usePointStyle = true;
        Chart.defaults.elements.bar.borderRadius = 10;
        Chart.defaults.elements.line.tension = 0.4;

        /** Barres et anneaux aux couleurs du thème (les courbes gardent vert = entrées, rouge = sorties). */
        const palette = ['#6366f1', '#ec4899', '#06b6d4', '#f97316', '#8b5cf6', '#84cc16', '#facc15', '#14b8a6'];
        Chart.register({
            id: 'sfCouleurs',
            beforeUpdate(graphique) {
                const type = graphique.config.type;
                graphique.data.datasets.forEach((serie) => {
                    const couleurs = graphique.data.labels.map((_, i) => palette[i % palette.length]);
                    if ((serie.type || type) === 'bar') {
                        serie.backgroundColor = couleurs;
                        serie.borderRadius = 12;
                        serie.borderSkipped = false;
                    }
                    if (type === 'doughnut' || type === 'pie') {
                        serie.backgroundColor = couleurs;
                        serie.borderColor = '#ffffff';
                        serie.borderWidth = 4;
                        serie.hoverOffset = 14;
                    }
                });
            },
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        /** Apparition en cascade des cartes. */
        document.querySelectorAll('.stat-card, .card-custom').forEach((carte, index) => {
            carte.style.animationDelay = Math.min(index * 70, 700) + 'ms';
        });

        /** Les chiffres des cartes défilent de 0 jusqu'à leur valeur. */
        document.querySelectorAll('.stat-value').forEach((element) => {
            const texte = element.textContent.trim();
            const correspondance = texte.match(/^(\d[\d\s.,]*)(.*)$/);
            if (!correspondance || calme()) {
                return;
            }
            const cible = parseInt(correspondance[1].replace(/\D/g, ''), 10);
            const suffixe = correspondance[2];
            const duree = 1100;
            const debut = performance.now();
            const etape = (maintenant) => {
                const progression = Math.min((maintenant - debut) / duree, 1);
                const adouci = 1 - Math.pow(1 - progression, 3);
                element.textContent = Math.round(cible * adouci).toLocaleString('fr-FR') + suffixe;
                if (progression < 1) {
                    requestAnimationFrame(etape);
                }
            };
            element.textContent = '0' + suffixe;
            requestAnimationFrame(etape);
        });

        /** Cartes de chiffres qui s'inclinent en 3D sous la souris. */
        document.querySelectorAll('.stat-card').forEach((carte) => {
            carte.addEventListener('mousemove', (evenement) => {
                if (calme()) {
                    return;
                }
                const zone = carte.getBoundingClientRect();
                const x = (evenement.clientX - zone.left) / zone.width;
                const y = (evenement.clientY - zone.top) / zone.height;
                carte.style.setProperty('--mx', x * 100 + '%');
                carte.style.setProperty('--my', y * 100 + '%');
                carte.style.transform = `perspective(700px) rotateX(${(0.5 - y) * 10}deg) rotateY(${(x - 0.5) * 12}deg) translateY(-4px)`;
            });
            carte.addEventListener('mouseleave', () => {
                carte.style.transform = '';
            });
        });

        /** Effet d'onde au clic sur les boutons. */
        document.addEventListener('click', (evenement) => {
            const bouton = evenement.target.closest('.btn');
            if (!bouton || calme()) {
                return;
            }
            const zone = bouton.getBoundingClientRect();
            const taille = Math.max(zone.width, zone.height);
            const onde = document.createElement('span');
            onde.className = 'sf-ripple';
            onde.style.width = onde.style.height = taille + 'px';
            onde.style.left = evenement.clientX - zone.left - taille / 2 + 'px';
            onde.style.top = evenement.clientY - zone.top - taille / 2 + 'px';
            bouton.appendChild(onde);
            setTimeout(() => onde.remove(), 650);
        });

        /** Confettis quand une action vient de réussir. */
        if (document.querySelector('.alert-success') && !calme()) {
            lancerConfettis();
        }

        afficherSalut();
        installerProgression();
        installerLueur();
        installerPaletteCommandes();
    });

    /** « Bonsoir Admin 🌆 · mercredi 15 octobre » au-dessus du titre de la page. */
    function afficherSalut() {
        const lienUtilisateur = document.querySelector('.sf-user a');
        const titre = document.querySelector('.page-title');
        if (!lienUtilisateur || !titre) {
            return;
        }
        const heure = new Date().getHours();
        const [mot, emoji] =
            heure >= 5 && heure < 12 ? ['Bonjour', '☀️'] :
            heure >= 12 && heure < 18 ? ['Bon après-midi', '😎'] :
            heure >= 18 && heure < 23 ? ['Bonsoir', '🌆'] : ['Bonne nuit', '🌙'];
        const date = new Date().toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });
        const salut = document.createElement('div');
        salut.className = 'sf-greet';
        salut.innerHTML = `${emoji} ${mot} <b></b> · ${date}`;
        salut.querySelector('b').textContent = lienUtilisateur.textContent.trim();
        titre.parentElement.insertBefore(salut, titre);
    }

    /** Fine barre en haut de l'écran qui suit le défilement. */
    function installerProgression() {
        const barre = document.createElement('div');
        barre.id = 'sf-progress';
        document.body.appendChild(barre);
        const majour = () => {
            const max = document.documentElement.scrollHeight - window.innerHeight;
            barre.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
        };
        window.addEventListener('scroll', majour, { passive: true });
        majour();
    }

    /** Halo de couleur qui suit la souris. */
    function installerLueur() {
        if (calme() || !window.matchMedia('(pointer: fine)').matches) {
            return;
        }
        const halo = document.createElement('div');
        halo.id = 'sf-glow';
        document.body.appendChild(halo);
        let x = window.innerWidth / 2;
        let y = window.innerHeight / 2;
        let enAttente = false;
        window.addEventListener('mousemove', (evenement) => {
            x = evenement.clientX;
            y = evenement.clientY;
            if (!enAttente) {
                enAttente = true;
                requestAnimationFrame(() => {
                    halo.style.left = x + 'px';
                    halo.style.top = y + 'px';
                    enAttente = false;
                });
            }
        });
    }

    /** Palette de commandes : Ctrl + K pour aller sur n'importe quelle page au clavier. */
    function installerPaletteCommandes() {
        const bouton = document.querySelector('[data-sf-cmdk]');
        if (!bouton) {
            return;
        }

        const elements = [];
        document.querySelectorAll('.sf-nav .nav-link[href], .sf-nav .dropdown-item[href]').forEach((lien) => {
            const href = lien.getAttribute('href');
            if (!href || href === '#') {
                return;
            }
            const icone = lien.querySelector('i');
            elements.push({
                libelle: lien.textContent.trim(),
                icone: icone ? icone.className.replace(/\bme-\d\b/g, '').trim() : 'bi bi-arrow-right',
                action: () => { window.location.href = href; },
                indice: 'Page',
            });
        });
        const profil = document.querySelector('.sf-user a');
        if (profil) {
            elements.push({ libelle: 'Mon profil', icone: 'bi bi-person-circle', action: () => { window.location.href = profil.href; }, indice: 'Page' });
        }
        elements.push(
            { libelle: 'Agrandir le texte', icone: 'bi bi-zoom-in', action: () => window.changerTaille && changerTaille(1), indice: 'Action' },
            { libelle: 'Réduire le texte', icone: 'bi bi-zoom-out', action: () => window.changerTaille && changerTaille(-1), indice: 'Action' },
            { libelle: 'Contraste élevé', icone: 'bi bi-circle-half', action: () => window.toggleContraste && toggleContraste(), indice: 'Action' },
            { libelle: 'Lancer des confettis', icone: 'bi bi-stars', action: () => lancerConfettis(), indice: 'Bonus' },
        );
        const deconnexion = document.querySelector('form[action$="/logout"]');
        if (deconnexion) {
            elements.push({ libelle: 'Se déconnecter', icone: 'bi bi-box-arrow-right', action: () => deconnexion.submit(), indice: 'Action' });
        }

        const fenetre = document.createElement('div');
        fenetre.className = 'sf-cmdk';
        fenetre.innerHTML = `
            <div class="sf-cmdk-box" role="dialog" aria-modal="true" aria-label="Palette de commandes">
                <div class="sf-cmdk-search"><i class="bi bi-search"></i><input type="text" placeholder="Aller à… (stock, commandes, alertes…)" aria-label="Rechercher une page ou une action"></div>
                <ul class="sf-cmdk-list" role="listbox"></ul>
                <div class="sf-cmdk-aide"><span>↑ ↓ naviguer</span><span>Entrée ouvrir</span><span>Échap fermer</span></div>
            </div>`;
        document.body.appendChild(fenetre);
        const champ = fenetre.querySelector('input');
        const liste = fenetre.querySelector('.sf-cmdk-list');
        let resultats = [];
        let selection = 0;

        const sansAccents = (texte) => texte.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
        const afficher = () => {
            const recherche = sansAccents(champ.value.trim());
            resultats = elements.filter((element) => sansAccents(element.libelle).includes(recherche));
            selection = Math.min(selection, Math.max(resultats.length - 1, 0));
            liste.innerHTML = '';
            if (!resultats.length) {
                liste.innerHTML = '<li class="sf-cmdk-vide">Rien trouvé 🤷</li>';
                return;
            }
            resultats.forEach((element, index) => {
                const ligne = document.createElement('li');
                ligne.className = 'sf-cmdk-item' + (index === selection ? ' actif' : '');
                ligne.setAttribute('role', 'option');
                ligne.innerHTML = `<i class="${element.icone}"></i><span></span><small>${element.indice}</small>`;
                ligne.querySelector('span').textContent = element.libelle;
                ligne.addEventListener('mouseenter', () => { selection = index; afficher(); });
                ligne.addEventListener('click', () => executer(index));
                liste.appendChild(ligne);
            });
        };
        const ouvrir = () => {
            fenetre.classList.add('ouvert');
            champ.value = '';
            selection = 0;
            afficher();
            setTimeout(() => champ.focus(), 20);
        };
        const fermer = () => fenetre.classList.remove('ouvert');
        const executer = (index) => {
            const element = resultats[index];
            if (element) {
                fermer();
                element.action();
            }
        };

        bouton.addEventListener('click', ouvrir);
        fenetre.addEventListener('click', (evenement) => { if (evenement.target === fenetre) { fermer(); } });
        champ.addEventListener('input', () => { selection = 0; afficher(); });
        document.addEventListener('keydown', (evenement) => {
            if ((evenement.ctrlKey || evenement.metaKey) && evenement.key.toLowerCase() === 'k') {
                evenement.preventDefault();
                fenetre.classList.contains('ouvert') ? fermer() : ouvrir();
                return;
            }
            if (!fenetre.classList.contains('ouvert')) {
                return;
            }
            if (evenement.key === 'Escape') {
                fermer();
            } else if (evenement.key === 'ArrowDown') {
                evenement.preventDefault();
                selection = (selection + 1) % Math.max(resultats.length, 1);
                afficher();
            } else if (evenement.key === 'ArrowUp') {
                evenement.preventDefault();
                selection = (selection - 1 + resultats.length) % Math.max(resultats.length, 1);
                afficher();
            } else if (evenement.key === 'Enter') {
                evenement.preventDefault();
                executer(selection);
            }
        });
    }

    function lancerConfettis() {
        const toile = document.createElement('canvas');
        toile.id = 'sf-confetti';
        document.body.appendChild(toile);
        const contexte = toile.getContext('2d');
        const redimensionner = () => {
            toile.width = window.innerWidth;
            toile.height = window.innerHeight;
        };
        redimensionner();
        window.addEventListener('resize', redimensionner);

        const couleurs = ['#6366f1', '#8b5cf6', '#ec4899', '#f97316', '#84cc16', '#06b6d4', '#facc15'];
        const confettis = Array.from({ length: 140 }, () => ({
            x: toile.width / 2 + (Math.random() - 0.5) * 200,
            y: toile.height * 0.25,
            vx: (Math.random() - 0.5) * 14,
            vy: Math.random() * -12 - 4,
            taille: Math.random() * 8 + 5,
            angle: Math.random() * Math.PI,
            rotation: (Math.random() - 0.5) * 0.3,
            couleur: couleurs[Math.floor(Math.random() * couleurs.length)],
            rond: Math.random() < 0.35,
        }));

        const debut = performance.now();
        const dessiner = (maintenant) => {
            contexte.clearRect(0, 0, toile.width, toile.height);
            const age = maintenant - debut;
            confettis.forEach((c) => {
                c.vy += 0.35;
                c.vx *= 0.99;
                c.x += c.vx;
                c.y += c.vy;
                c.angle += c.rotation;
                contexte.save();
                contexte.globalAlpha = Math.max(0, 1 - age / 2600);
                contexte.translate(c.x, c.y);
                contexte.rotate(c.angle);
                contexte.fillStyle = c.couleur;
                if (c.rond) {
                    contexte.beginPath();
                    contexte.arc(0, 0, c.taille / 2, 0, Math.PI * 2);
                    contexte.fill();
                } else {
                    contexte.fillRect(-c.taille / 2, -c.taille / 4, c.taille, c.taille / 2);
                }
                contexte.restore();
            });
            if (age < 2600) {
                requestAnimationFrame(dessiner);
            } else {
                toile.remove();
            }
        };
        requestAnimationFrame(dessiner);
    }
})();
