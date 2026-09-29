# Frames & cores

Frames & cores est un site e-commerce de matériel informatique réalisé en PHP dans un cadre pédagogique.
Il permet de consulter des articles, d'afficher leur détail, de gérer un panier et de se connecter à un compte utilisateur.

## Fonctionnalités

- Affichage des articles disponibles depuis une base de données
- Consultation du détail d'un article
- Ajout d'articles au panier
- Connexion utilisateur
- Formulaire de contact

## Installation

1. Placer le projet dans le dossier `www` de WampServer.
2. Démarrer Apache et MySQL depuis WampServer.
3. Créer une base de données puis importer le fichier `database/computer_database.sql`.
4. Vérifier les identifiants de connexion à la base dans `includes/php/bd.php`.
5. Ouvrir le projet à l'adresse suivante : `http://localhost/TyldenHounsa/`.

## Organisation du projet

- `index.php` : page d'accueil et liste des articles
- `article.php` : détail d'un article
- `panier.php` : gestion du panier
- `connexion.php` : connexion utilisateur
- `contact.html` : formulaire de contact
- `database/` : script de création et de remplissage de la base
- `assets/` : feuilles de style, scripts JavaScript et images
- `includes/php/` : connexion à la base et traitement des formulaires

## Crédits et Droits d'auteur

Ce projet a été réalisé dans un cadre strictement pédagogique et n'a aucun but commercial.

Les images de produits utilisées pour illustrer le site proviennent du site de [La Fnac](https://fnac.com).

Elles restent la propriété exclusive de leurs auteurs respectifs.
