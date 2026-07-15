# SGA EPF

Systeme de gestion des admissions EPF Africa construit avec Laravel, Blade, Livewire et MySQL.

## Installation locale

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

La connexion MySQL et l'envoi des emails doivent etre configures dans `.env` avant de lancer les migrations et les invitations.

## Configuration MySQL et emails

Copiez `.env.example` vers `.env`, puis renseignez `DB_DATABASE`, `DB_USERNAME` et `DB_PASSWORD`. Pour Gmail, utilisez un mot de passe d'application Google dans `MAIL_PASSWORD`, jamais le mot de passe principal du compte.

Apres toute modification du `.env`, synchronisez la base et rechargez la configuration :

```bash
php artisan migrate --seed
php artisan config:clear
```

Les emails de soumission, demande de complement, transmission au jury, admission et refus sont envoyes automatiquement. Chaque tentative est conservee dans la table `emails_envoyes` avec son statut.

## Initialiser le back-office

Il n'existe aucune inscription publique pour le personnel. Apres `php artisan migrate --seed`, creez le premier super administrateur avec la commande interactive suivante :

```bash
php artisan sga:creer-super-admin
```

La commande demande le prenom, le nom, l'email professionnel et un mot de passe fort sans afficher ce dernier dans le terminal.

Le super administrateur peut ensuite ouvrir `/admission/utilisateurs` pour :

- creer un compte interne ;
- attribuer le role service admission, jury ou super administrateur ;
- envoyer un lien temporaire de definition du mot de passe ;
- desactiver un acces tout en conservant son historique ;
- renvoyer une invitation.

## Acces

- `/` : portail candidat ;
- `/programmes` : programmes ouverts ;
- `/suivi-candidature` : suivi avec email et code ;
- `/admission` : entree du back-office ;
- `/admission/connexion` : connexion du personnel.

Chaque route interne verifie que le compte est actif, que l'email est confirme et que l'utilisateur possede un role autorise.

## Verification

```bash
php artisan test
npm run build
```

Le parcours valide est : soumission candidat, prise en charge par le service admission, verification des documents, transmission au jury, puis admission, refus ou demande de complement.
