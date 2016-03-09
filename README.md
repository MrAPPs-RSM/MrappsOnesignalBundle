# Mrapps OneSignal
Gestione notifiche OneSignal - bundle per Symfony2

## Requisiti

  - PHP 5.4+
  - Symfony2 2.6+

## Installazione

composer.json:
```json
{
	"require": {
		"mrapps/onesignalbundle": "dev-master"
	}
}
```

AppKernel.php:
```php
$bundles = array(
    [...]
    new Mrapps\OnesignalBundle\MrappsOnesignalBundle(),
);
```

routing.yml:
```yaml
mrapps_onesignal:
    resource: "@MrappsOnesignalBundle/Controller/"
    prefix:   /
```

config.yml (configurazione completa):
```yaml
doctrine:
    [...]
    orm:
        [...]
        resolve_target_entities:
            [...]
            Mrapps\OnesignalBundle\Model\UserInterface: [[ CLASSE USER ALL'INTERNO DEL PROGETTO (es. AppBundle\Entity\User) ]]


mrapps_onesignal:
    parameters:
        app_id: [[ APP ID ONE SIGNAL ]]
        app_name: [[ NOME APP ]]
    web_push:
        rest_api_key: [[ API KEY CHIAMATE REST ]]
        gcm_sender_id: [[ ID PER NOTIFICHE PUSH CHROME ]]
        safari_web_id: [[ ID PER NOTIFICHE PUSH SAFARI ]]
```

## Utilizzo

- Copiare i file "OneSignalSDKWorker.js" e "OneSignalSDKUpdaterWorker.js" dalla cartella "public/js" del bundle alla root del progetto WEB. Il file MANIFEST verrà generato direttamente inline all'interno della pagina.

Attivazione notifiche lato client:
```twig
{#

- Aggiungere questa riga nel punto in cui si vuole renderizzare il blocco javascript per attivare le
  notifiche OneSignal.

- INCLUDERE JQUERY PRIMA DI QUESTA RIGA!!!

- Se si vogliono passare i parametri device_name, device_version e platform (opzionali), creare una rotta
  in un proprio controller, leggere i dati in qualche modo (ad esempio parsando lo user agent) e forwardare
  il controller nel bundle MrappsOnesignal.
  
#}
{{ render(controller('MrappsOnesignalBundle:Onesignal:__js', {'device_name':'[[ NOME BROWSER ]]', 'device_version':'[[ VERSIONE BROWSER ]]', 'platform':'[[ NOME SO ]]'})) }}
```

Servizio:
```php
$os = $this->container->get('mrapps.onesignal');
```

Invio notifica:
```php
$data = array(
    'message' => 'Corpo della notifica',
    'title' => 'Titolo della notifica',     //Opzionale; se non viene passato, verrà impostato di default il nome dell'app
    'url' => 'URL da visitare al click sulla notifica', //Opzionale
);
$segments = array('All');   //Opzionale; se non viene passato, la notifica verrà inviata di default a tutti gli utenti.

$os->sendNotification($data, $segments);
```

Disattiva un dispositivo (player):
```php
$playerID = 'ID del dispositivo su OneSignal';
$os->deactivatePlayer($playerID);
```

Disattiva tutti i dispositivi associati ad un utente:
```php
$os->deactivateAllPlayersForUser($user);
```
