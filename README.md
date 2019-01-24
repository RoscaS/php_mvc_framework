# Framework MVC (PHP)

## ATTENTION !
* Sur la branche *master* (ainsi que *app*) se trouve l'application *Tasks* faite à l'aide du framework.
* Sur la branche *framework* se trouve le framework "bare bones" prêt à accueillir une application.

* Un dump d'une BDD valide pour l'application se trouve à la racine.
  * Utilisateur: **sol**
  * mdp: **poule**
  
## Question de la consigne
> Est-ce une bonne idée de mettre dans git un fichier pouvant contenir des mots de passe comme config-production.php ?

Non. Dailleurs pour que le projet fonctoinne, il est nécessaire de créér un 
fichier `env.php` dans `/config` avec le mdp de la base de donnée:

```php
<?php

$env_data = [
  'password' => 'mdp',
];
```

Ce dernier est dans le gitignore du projet et ne se retrouve pas sur le répo git.


## Git flow

master <- staging <- WIP-branches

## Conventions
* **Nommage**
  * Fonctions et variables: camelCase
  * Classes: CapWords
* **Langue**
  * Le framework ainsi que tous les noms de fichiers sont écrits en anglais
  * Les messages d'erreur sont écrits en anglais 
  * Les commentaires et la documentation sont temporairement en français

## Diagrammes

### Controllers
![Image](https://i.imgur.com/MJ96FCu.png)

### Core
![Image](https://i.imgur.com/QmUXNRi.png)

### Model
![Image](https://i.imgur.com/8tAuhH6.png)



## Navigation & Interactions

### home
* `/`: 
  * redirect sur `/register/login` si pas connecté et pas de possibilter d'identifier via session/cookie
  * redirect sur `/tasks` si connecté
* `/home` 

### register
  * redirect sur `/`
* `/register` 
  * redirect sur `/register/login`
* `/register/login` _loginAction_
* `/register/logout` _logoutAction_ **protégé**
  * redirect sur `/register/login` si pas connecté et pas possible d'identifier via session/cookie
  
### tasks
* `/tasks` `index` **protégé**
  * redirect sur `/register/login` si pas connecté et pas possible d'identifier via session/cookie
* `/tasks/add` _addAction_ **protégé**
  * redirect sur `/tasks` après l'action
* `/tasks/update` _updateAction_ **protégé**
  * redirect sur `/tasks` après l'action
* `/tasks/delete` _deleteAction_ **protégé**
  * redirect sur `/tasks` après l'action
  
