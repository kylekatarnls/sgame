# [French] Informations utiles pour InSearch

## Instructions pour l'installation

1. Il est vivement conseillé d'installer le projet en utilisant Git (installez le si vous ne l'avez pas encore, sous Linux et Mac, il est installé par défaut, sous Windows il faut installer msysgit et choisir l'option "Ajouter à votre variable d'environement..." lorsque l'installateur la propose).

2. Installer un serveur Web (exemple : Wamp / Mamp) disposant d'Apache et de PHP 5.4 ou + (sous linux, l'installation des paquets apache et php suffit)

3. Créer un dossier de travail "insearch" (dans htdocs de wamp) taper les commandes suivantes :
	cd /chemin/vers/le/dossier/insearch
	git clone https://github.com/kylekatarnls/insearch.git .
	git config --global push.default matching
	git config --global user.name "Votre prénom"
	git config --global user.email "votre-pseudo-github@github.com"

	Exemple de configuration :
	git config --global user.name "Bastien"
	git config --global user.email "kylekatarnls@github.com"

4. Installer composer en vous mettant dans le dossier du projet puis en exécutant :
	curl -sS https://getcomposer.org/installer | php

5. Installer les dépendances du projet avec composer en exécutant :
    (installer/activer openssl si manquant et décommenter la ligne ";extension=openssl.dll" de php.ini)
    (
        sudo apt-get install postgresql postgresql-client apache2-mpm-prefork php5-mcrypt php5-pgsql
        sudo a2enmod mod_php5
        sudo service apache2 restart
    )
	php composer.phar update

5.b. Aucune fonctionnalité que nous utilisons ne semble utiliser Mcrypt, l'extension n'est donc pas absolument nécessaire.
Donc si l'installation de Mcrypt échoue, il est possible de commenter cette partie de code dans le fichier
vendor/laravel/framework/src/Illuminate/Foundation/start.php :
<pre>if ( ! extension_loaded('mcrypt'))
{
	echo 'Mcrypt PHP extension required.'.PHP_EOL;

	exit(1);
}</pre>

6. (Linux/Mac Uniquement) changer le CHMOD du dossier storage :
	chmod -R 0777 app/storage

7. (Facultatif) Installer Memcached et activer l'extension php_memcached

8. Installer PostgreSQL et activer l'extension php_pdo_pgsql dans php.ini 
    !! Attention !!
        Ne pas installer StackBuilder
	- Créer un rôle de connexion user : "insearch", pass : "r6y_7|Hj{-SQdf"
	- Créer une base de donnée nommée "insearch" dont le rôle "insearch" est propriétaire

9. (Windows uniquement) Ajouter php.exe à la variable d'environement PATH (wamp est dans "c:\" )

10. Ouvrir la console, aller dans le dossier du projet (le dossier contenant le fichier "artisan") et taper les commandes suivantes suivies d'Entrée :
    (seulement quand postgre est installé bien sûr)
	- php artisan migrate
	- php artisan db:seed
	migrate : créer les tables utiles ou les met à jour le cas échéant
	seed : peuple les tables avec des données de base

11. Créer un virtual-host comme ceci :
	<VirtualHost *:*>
	ServerAdmin webmaster@insearch
	DocumentRoot "C:\wamp\www\insearch\public"
	ServerName insearch
	</VirtualHost>

	Remplacer C:\wamp\www\insearch\public par le chemin correspondant sur votre machine
	Chemin conseillé sous Mac/Linux : /var/www/insearch/public
	/!\ Il faut pointer sur le dossier public contenu dans le projet.

12. Modifier C:\Windows\System32\drivers\etc (sous Windows) ou /etc/hosts (sous Linux et Mac) pour ajouter l'URL insearch :
	Remplacer la ligne :
		127.0.0.1       localhost
	Par
		127.0.0.1       localhost insearch

13. Démarrer/redémarrer le serveur

14. Tester en chargeant l'URL dans votre navigateur :
[http://insearch/](http://insearch/)
    (pour voir la config modifier le fichier app/routes.php, et commenter-décommenter la ligne phpinfo();exit;)


## Récupérer les mises à jour du projet
Ouvrir un terminal, se placer dans le dossier du projet et exécuter :
- git pull
- php artisan migrate


## Avant d'envoyer vos modifications
Avant d'envoyer vos modifications, vérifiez-les avec git status et git diff
- git status : Cette commande vous montre les fichiers modifiés (en vert, ce qui sont prêt à être commités et en rouge ceux qui doivent d'abord être ajoutés)
- git diff : Cette commande vous montre les modifications pas encore ajoutées (fichiers rouges de git status)

Dans la mesure du possible, n'ajoutez que les modifications que vous avez faites

Lancer ensuite les tests unitaires PHP avec phpunit. Sur l'espace Cloud 9, entrer ceci dans le Terminal :
- cd ~/711694/insearch
- php ../phpunit.phar

Un bandeau vert OK doit s'afficher si aucun test n'a été cassé par votre code

Lancer ensuite les tests unitaires JS avec Jasmine. Sur l'espace Cloud 9, ouvrez par routes.php et cliquez sur Run (en cas d'erreur : Configure... > Runtime : Apache+PHP)
Puis entrer l'URL https://insearch-c9-kylekatarn.c9.io/specs/1 dans votre navigateur
Un bandeau vert OK doit s'afficher si aucun test n'a été cassé par votre code


## Envoyer vos modifications
Ouvrir un terminal, se placer dans le dossier du projet et exécuter :
- git add . ("." ajoute tous les fichiers modifiés, sinon vous pouvez préciser des dossiers et des fichiers)
- git commit -m "Décrivez vos modifications"
- git push

N'hésitez pas à être très précis dans vos descriptions et si possible ne poussez (git push) que des modifications opérationnelles, sinon précisez dans la description ce qu'il reste à faire pour les rendre opérationnelles.

Vous pouvez faire plusieurs commit au fur et à mesure que vous codez pour séparer plusieurs modifications puis pousser (git push) plusieurs modifications d'un coup, ça ne pose pas de problème.


## Fonctionnalités additionnelles

Ci-dessous la liste des fonctionnalités qui peuvent compter pour les 10 points bonus du barème :
- À côté de chaque résultat, un compteur (gellule grise) indique le nombre clics effectué sur ce lien (combien de fois les utilisateurs ont cliqué sur ce résultat).
- Le Crawler convertit les contenus ISO en UTF-8 pour un affichage uniforme des résultats.
- Le Crawler détecte les contenus duppliqué pour éviter les doublons.
- Internationalisation (français, anglais, traduction en d'autres langues aisée)
- Détection de la langue la plus appropriée en fonction des préférences de l'utilisateur et des traductions disponibles
- Oeil gris ou bleu devant les lien pour indiquer lesquels ont déjà été visités
- Affichage prioritaire des pages dans la langue de l'utilisateur (à score égal, les pages françaises sont affichées avant les pages en d'autres langues si l'utilisateur est français)
- Recherche de phrases (mots groupés en les mettant entre guillements)
- Suggestion en fonction des recherches fructueuses déjà effectuées
- Compatibilité mobile (responsive design)


## Laravel PHP Framework

[![Latest Stable Version](https://poser.pugx.org/laravel/framework/version.png)](https://packagist.org/packages/laravel/framework) [![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.png)](https://packagist.org/packages/laravel/framework) [![Build Status](https://travis-ci.org/laravel/framework.png)](https://travis-ci.org/laravel/framework)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, and caching.

Laravel aims to make the development process a pleasing one for the developer without sacrificing application functionality. Happy developers make the best code. To this end, we've attempted to combine the very best of what we have seen in other web frameworks, including frameworks implemented in other languages, such as Ruby on Rails, ASP.NET MVC, and Sinatra.

Laravel is accessible, yet powerful, providing powerful tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

## Official Documentation

Documentation for the entire framework can be found on the [Laravel website](http://laravel.com/docs).

### Contributing To Laravel

**All issues and pull requests should be filed on the [laravel/framework](http://github.com/laravel/framework) repository.**

### License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
