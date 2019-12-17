# MyWishList

Projet WEB S3 IUT Nancy-Charlemagne

## Installation

Utilisez [composer](https://getcomposer.org/) pour installer les dépendances.
```bash
git clone https://github.com/juliennoel9/mywishlist.git
cd mywishlist
composer install
```

Dans le répertoire "src/config", copiez le fichier **database.ini.sample** et renommez le **database.ini** puis modifiez le pour votre configuration.
Lancez ensuite le script **initDatabase.php** pour créer la base de données (option '-f' pour forcer l'exécution).
```bash
php initDatabase.php
```

## Lancement

Démarrez votre base de données, puis lancez un serveur de developpement à la racine du projet.
```bash
php -S localhost:7777
```
Depuis un navigateur, rendez vous sur http://localhost:7777

## Contributeurs
**DEMANGE Louis** - [Nekzuris](https://github.com/juliennoel9/mywishlist/commits?author=Nekzuris)

**NOËL Julien** - [juliennoel9](https://github.com/juliennoel9/mywishlist/commits?author=juliennoel9)

**NOSAL Loïck** - [LoickNosal](https://github.com/juliennoel9/mywishlist/commits?author=LoickNosal)

**GSELL Paul** - [poluxtobee](https://github.com/juliennoel9/mywishlist/commits?author=poluxtobee)
