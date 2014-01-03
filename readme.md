# [French] Informations utiles pour InSearch

## Instructions pour l'installation

1. Il est vivement conseillé d'installer le projet en utilisant Git (installer-le si vous ne l'avez pas encore, sous Linux et Mac, il est installé par défaut, sous Windows il faut installer msysgit et choisir l'option "Ajouter à votre variable d'environement..." lorsque l'installateur la propose).

2. Créer un dossier de travail "insearch" taper les commandes suivantes :
	cd /chemin/vers/le/dossier/insearch
	git clone https://github.com/kylekatarnls/insearch.git .
	git config --global push.default matching
	git config --global user.name "Votre prénom"
	git config --global user.email "votre-pseudo-github@github.com"

	Exemple de configuration :
	git config --global user.name "Bastien"
	git config --global user.email "kylekatarnls@github.com"

3. Installer un serveur Web (exemple : Wamp / Mamp) disposant d'Apache et de PHP 5.4 ou + (sous linux, l'installation des paquets apache et php suffit)

4. (Facultatif) Installer Memcached et activer l'extension php_memcached

5. Installer PostgreSQL et activer l'extension php_pdo_pgsql
	- Créer un rôle de connexion user : "insearch", pass : "r6y_7|Hj{-SQdf"
	- Créer une base de donnée nommée "insearch" dont le rôle "insearch" est propriétaire

6. (Windows uniquement) Ajouter php.exe à la variable d'environement PATH

7. Ouvrir la console, aller dans le dossier du projet (le dossier contenant le fichier "artisan") et taper les commandes suivantes suivies d'Entrée :
	- php artisan migrate
	- php artisan seed
	migrate : créer les tables utiles ou les met à jour le cas échéant
	seed : peuple les tables avec des données de base

8. Créer un virtual-host comme ceci :
	<VirtualHost *:*>
	ServerAdmin webmaster@insearch
	DocumentRoot "C:\wamp\www\insearch\public"
	ServerName insearch
	</VirtualHost>

	Remplacer C:\wamp\www\insearch\public par le chemin correspondant sur votre machine
	Chemin conseillé sous Mac/Linux : /var/www/insearch/public
	/!\ Il faut pointer sur le dossier public contenu dans le projet.

9. Modifier C:\Windows\System32\drivers\etc (sous Windows) ou /etc/hosts (sous Linux et Mac) pour ajouter l'URL insearch :
	Remplacer la ligne :
		127.0.0.1       localhost
	Par
		127.0.0.1       localhost insearch

10. Installer composer en vous mettant dans le dossier du projet puis en exécutant :
	curl -sS https://getcomposer.org/installer | php

11. Installer les dépendances du projet avec composer en exécutant :
	php composer.phar update

12. (Linux/Mac Uniquement) changer le CHMOD du dossier storage :
	chmod -R 0777 app/storage

12. Démarrer/redémarrer le serveur

13. Tester en chargeant l'URL dans votre navigateur :
[http://insearch/](http://insearch/)


## Récupérer les mises à jour du projet
Ouvrir un terminal, se placer dans le dossier du projet et exécuter :
	git pull
	php artisan migrate


## Envoyer vos modifications
Ouvrir un terminal, se placer dans le dossier du projet et exécuter :
	git add *
	git commit -m "Décrivez vos modifications"
	git push

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
