# Blog 8.7.5 compatible ZwiiCMS / ZwiiCampus

Module **Blog** autonome, préparé pour **remplacer proprement** le dossier `module/blog` de **ZwiiCMS** ou **ZwiiCampus**.

Cette archive corrige et stabilise la branche 8.7 afin que le **tri visiteur fonctionne réellement côté serveur** — y compris avec la pagination — au lieu de se limiter à un simple réordonnancement visuel de la page courante.

## Correctifs intégrés

- correction d'une **erreur de syntaxe PHP** dans `blog.php` (`elsif` → `elseif`)
- mise à jour de version vers **8.7.5**
- conservation du mécanisme de tri dans l'URL (`/sort/...`)
- adaptation de la vue publique pour que les boutons de tri déclenchent un **vrai tri serveur**
- compatibilité conservée avec l'arborescence module standard de **ZwiiCMS** et **ZwiiCampus**

## Installation

1. Sauvegarder le dossier existant `module/blog`
2. Remplacer ce dossier par celui contenu dans cette archive
3. Vider les caches éventuels du site
4. Recharger la page du blog

## Structure de l'archive

L'archive contient directement un dossier racine nommé **`blog`** prêt à être copié dans `module/`.

## Ce que règle cette version

- tri par **date**, **titre** et **catégorie** côté visiteur
- conservation du tri lors de l'ouverture d'un article
- conservation du tri avec la pagination
- catégories et tags modernisés de la 8.7

## Vérifications réalisées

- lint PHP sur `blog.php`
- lint PHP sur les vues et fichiers `.js.php`
- contrôle de compatibilité structurelle avec le `module/blog` de **ZwiiCampus master**

