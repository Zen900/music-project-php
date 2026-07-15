# Music Project

Een muziekapplicatie gebouwd met PHP Symfony en PostgreSQL. In de applicatie kunnen albums en liedjes worden bekeken en geüpload. Ook kunnen de liedjes beluisterd worden. 
## Functionaliteiten

* Albums en liedjes bekijken
* Albums uploaden
* Liedjes uploaden
* Audio player met wachtrij

## Gebruikte technieken

* PHP 8
* Symfony
* PostgreSQL
* Doctrine ORM
* Twig
* JavaScript

## Installatie

Clone de repository:

git clone <repository-url>
cd music-project-php

Installeer de dependencies: composer install

Maak een `.env.local` bestand aan en vul de databasegegevens in:

DATABASE_URL="postgresql://gebruikersnaam:wachtwoord@127.0.0.1:5432/musicPHP?serverVersion=17&charset=utf8"
JAMENDO_CLIENT_ID=your_client_id
JAMENDO_ARTIST_ID=7872

Maak de database aan en voer de migraties uit:

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

## Applicatie starten

Start de Symfony-server: symfony server:start

De applicatie is vervolgens bereikbaar via:

http://127.0.0.1:8000

## Data importeren via de API

Om de data vab albums en liedjes vanuit de Jamendo API te importeren en in de database toe te voegen:

php bin/console app:import-jamendo

## Opmerking

De map `public/songs` is niet opgenomen in deze repository omdat de audiobestanden te groot zijn voor GitHub. Voor het testen kunnen eigen audiobestanden worden geüpload via de applicatie.
