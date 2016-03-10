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


Generazione files necessari in cartella ROOT WEB:
```!/bin/bash
app/console mrapps:onesignal:generatefiles
```


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
$os = $container->get('mrapps.onesignal');
```


Invio notifica:
```php
$data = array(
    'message' => 'Corpo della notifica',
    'title' => 'Titolo della notifica',     //Opzionale; se non viene passato, verrà impostato di default il nome dell'app
    'url' => 'URL da visitare al click sulla notifica', //Opzionale
);

/*
    Valori possibili:

    segments: valorizzare $sendTo (3° parametro) con l'array dei gruppi (segments) a cui inviare la notifica. Se non
              viene specificato niente, la notifica verrà inviata al segmento All (tutti gli utenti).
              
    players: valorizzare $sendTo (3° parametro) con l'array dei playerID a cui inviare la notifica. Se non viene specificato
             niente, non verrà inviata nessuna notifica.
*/
$type = 'segments';

$sendTo = array('All');

$os->sendNotification($data, $type, $sendTo);
```


Invio notifica a tutti i dispositivi di uno specifico utente:
```php
$data = array();    //Vedi esempio "Invio notifica"

$os->sendNotificationToUser($data, $user);
```


Invio notifica a più utenti:
```php
$data = array();    //Vedi esempio "Invio notifica"

$users = array();   //Array di utenti (classe che implementa Mrapps/OnesignalBundle/Model/UserInterface)

$os->sendNotificationToMultipleUsers($data, $users);
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
