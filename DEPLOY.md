# Déploiement gratuit — SmartTransport API

## Solution recommandée : [Render.com](https://render.com)

| Composant | Service Render | Coût |
|-----------|----------------|------|
| API Laravel | Web Service (Docker) | Gratuit (750 h/mois) |
| Base de données | PostgreSQL | Gratuit (1 Go, 90 jours) |
| HTTPS | Inclus | Gratuit |

> **Pourquoi PostgreSQL et pas MySQL ?**  
> Render ne propose pas de MySQL gratuit. PostgreSQL est compatible avec vos migrations Laravel sans modification de code.

> **Limitation du plan gratuit :** le service se met en veille après ~15 min d'inactivité. Le premier appel après veille peut prendre 30–60 s.

---

## Déploiement en 5 étapes

### 1. Pousser le code sur GitHub

```bash
git add .
git commit -m "Ajout configuration déploiement Render"
git push origin main
```

### 2. Créer un compte Render

1. Allez sur [render.com](https://render.com) et connectez-vous avec GitHub.
2. Autorisez l'accès à votre dépôt `smart_transport_api`.

### 3. Déployer via Blueprint

1. Dashboard Render → **New** → **Blueprint**.
2. Sélectionnez le dépôt `smart_transport_api`.
3. Render détecte `render.yaml` et propose :
   - `smart-transport-api` (Web Service)
   - `smart-transport-db` (PostgreSQL)
4. Cliquez sur **Apply**.

Le premier déploiement prend environ 5–10 minutes (build Docker + migrations + seed).

### 4. Vérifier le déploiement

Une fois le statut **Live**, testez :

```bash
# Health check
curl https://VOTRE-APP.onrender.com/up

# Login admin (compte seedé)
curl -X POST https://VOTRE-APP.onrender.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@smarttransport.com","password":"admin123"}'
```

### 5. Désactiver le seed après le premier déploiement

Dans le dashboard Render → **smart-transport-api** → **Environment** :

```
SEED_DATABASE=false
```

Cela évite de recréer les données de test à chaque redéploiement.

---

## Comptes de test (seedés automatiquement)

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@smarttransport.com | admin123 |
| Agent terminal | terminal@smarttransport.com | terminal123 |
| Agent bagagiste | bagagiste@smarttransport.com | bagagiste123 |

---

## URL de l'API

Toutes les routes sont préfixées par `/api` :

```
POST   /api/register
POST   /api/login
GET    /api/voyages          (auth requise)
GET    /api/admin/stats      (auth + rôle admin)
...
```

Base URL de production : `https://VOTRE-APP.onrender.com/api`

---

## Fichiers de configuration

| Fichier | Rôle |
|---------|------|
| `Dockerfile.production` | Image Docker optimisée pour la production |
| `docker/entrypoint.sh` | Migrations, cache, démarrage du serveur |
| `render.yaml` | Infrastructure as Code (Blueprint Render) |
| `.env.render.example` | Référence des variables d'environnement |

---

## Alternatives gratuites

| Plateforme | Avantages | Inconvénients |
|------------|-----------|---------------|
| **[Fly.io](https://fly.io)** | Docker natif, pas de veille | Crédits limités, config plus complexe |
| **[Koyeb](https://koyeb.com)** | Docker, déploiement rapide | 1 service gratuit, DB externe requise |
| **[Railway](https://railway.app)** | Très simple, MySQL possible | ~5 $ de crédit/mois seulement |
| **Oracle Cloud Free Tier** | VPS ARM gratuit à vie, MySQL possible | Configuration manuelle (Nginx, PHP, SSL) |

Pour conserver **MySQL** gratuitement, combinez Render/Koyeb (app) + [Aiven MySQL free tier](https://aiven.io/free-mysql-database) (base externe).

---

## Dépannage

**Build Docker échoue**  
→ Vérifiez que `composer.lock` est commité.

**Erreur de connexion DB**  
→ Vérifiez que `DB_CONNECTION=pgsql` et `DB_SSLMODE=require` sont définis.

**502 / service en veille**  
→ Attendez 30–60 s après le premier appel, ou passez au plan Starter (7 $/mois, sans veille).

**Migrations échouent**  
→ Consultez les logs Render : Dashboard → smart-transport-api → **Logs**.
