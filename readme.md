# Execution

Lancer le projet Symfony avec Symfony server : 

```bash
symfony server:start --no-tls  
```
A priori il ne doit pas être en tls (https) ? A confirmer.

Lancer le serveur mercure ou installer (il est dans le docker proposé par Symfony

```bash
docker compose up
```

avec -d si besoin.

Mettre à jour le .env.local avec le bon port (voir dans l'app docker pour l'info, trouver comment le fixer.

