# MyWishList

Projet WEB S3 IUT Nancy-Charlemagne 2019-2020

## Installation

Utilisez [composer](https://getcomposer.org/) pour installer les dépendances.
```bash
git clone https://github.com/juliennoel9/mywishlist.git
cd mywishlist
composer install
```

Dans le répertoire "src/config", copiez le fichier **database.ini.sample** et renommez le **database.ini** puis modifiez le pour votre configuration.
Lancez ensuite le script **initDatabase.php** pour créer la base de données (option `-f` pour forcer l'exécution).
```bash
php initDatabase.php
```

## Lancement

Démarrez votre base de données, puis lancez un serveur de developpement à la racine du projet.
```bash
php -S localhost:<numPort>
```
Depuis un navigateur, rendez vous sur http://localhost:numPort

## Fonctionnalités

### Participant

- [x] *Afficher une liste de souhaits* (Julien, Louis)
- [x] *Afficher un item d'une liste* (Julien, Louis)
- [x] *Réserver un item* (Julien)
- [x] *Ajouter un message avec sa réservation* (Julien)
- [x] *Ajouter un message sur une liste* (Julien)

### Créateur
- [x] *Créer une liste* (Julien, Louis)
- [x] *Modifier les informations générales d'une de ses listes* (Julien)
- [x] *Ajouter des items* (Julien)
- [x] *Modifier un item* (Julien)
- [x] *Supprimer un item* (Julien)
- [x] *Rajouter une image à un item* (Julien)
- [x] *Modifier une image à un item* (Julien)
- [x] *Supprimer une image d'un item* (Julien)
- [x] *Partager une liste* (Julien)
- [x] *Consulter les réservations d'une de ses listes avant échéance* (Julien)
- [x] *Consulter les réservations et messages d'une de ses listes après échéance* (Julien)

### Extensions
- [x] *Créer un compte* (Julien)
- [x] *S'authentifier* (Julien)
- [x] *Modifier son compte* (Julien)
- [x] *Rendre une liste publique* (Julien, Louis)
- [x] *Afficher les listes de souhaits publiques* (Julien, Louis)
- [ ] *Créer une cagnotte sur un item*
- [ ] *Participer à une cagnotte*
- [x] *Uploader une image* (Julien)
- [x] *Créer un compte participant* (Julien)
- [x] *Afficher la liste des créateurs* (Julien)
- [x] *Supprimer son compte* (Julien)
- [x] *Joindre les listes à son compte* (Julien)
- [x] *Page d'erreur 404* (Louis)

## Liens utiles
**Google Sheet** - Fonctionnalités, routes et méthodes - [Google Sheet](https://docs.google.com/spreadsheets/d/1NkXoVzma5kQGag3LFqx1n8IFDDFTjCObA5FDB259kMA/edit?usp=sharing)

**Trello** - Fonctionnalités et répartitions des tâches - [Trello](https://trello.com/b/V2bNWbbd/mywishlist)

## Contributeurs
**NOËL Julien** - [juliennoel9](https://github.com/juliennoel9/mywishlist/commits?author=juliennoel9)

**DEMANGE Louis** - [Nekzuris](https://github.com/juliennoel9/mywishlist/commits?author=Nekzuris)

**NOSAL Loïck** - [LoickNosal](https://github.com/juliennoel9/mywishlist/commits?author=LoickNosal)

**GSELL Paul** - [poluxtobee](https://github.com/juliennoel9/mywishlist/commits?author=poluxtobee)
