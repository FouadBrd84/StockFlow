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
    });

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
