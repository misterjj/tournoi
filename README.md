# Ankama Ultimate Tournament

Ce site est un site evènementiel afin d'accueillir un tournoi de super smash bros ultimate

## Outils utilisés
* [Symfony 4](https://symfony.com/4)
* [Bootstrap 4](https://getbootstrap.com/)
* [Api Toornament](https://developer.toornament.com/v2/overview/get-started) pour la gestions des matches (avec [Guzzle](http://docs.guzzlephp.org/en/stable/))
* [System responsive Tournament Bracket by Jakub Hájek](https://codepen.io/jimmyhayek/pen/yJkdEB) pour l'affichage des arbres de combats

## Features intérésantes

### L'autowiring et l'injection de dépendances

Grâce à Symfony 4 et l'[autowiring](https://symfony.com/doc/current/service_container/autowiring.html) l'injection de dépendance est beaucoup plus simple.
Comme exemple on peut voir dans [/src/Service/ToornamentService.php](https://github.com/misterjj/tournoi/blob/master/src/Service/ToornamentService.php):

```php
function __construct(
    \GuzzleHttp\Client $guzzle_client,
    FilesystemCache $cache,
    String $apikey,
    String $clientId,
    String $clientSecret,
    String $tournamentId,
    String $tournamentStageId
)
{
    /* fonction body */
}
```

Ici mon service qui communique avec Toornament à besoin de Guzzle, de cache fichier et de paramètre de connections à l'api.

Dans [/config/services.yaml](https://github.com/misterjj/tournoi/blob/master/config/services.yaml) nous avons la configurations des services :
```yaml
GuzzleHttp\Client:
    class: GuzzleHttp\Client
    public: false

Symfony\Component\Cache\Simple\FilesystemCache:
    class: Symfony\Component\Cache\Simple\FilesystemCache
    public: false

App\Service\ToornamentService:
    class: App\Service\ToornamentService
    arguments:
        $apikey: '%env(TOORNAMENT_APIKEY)%'
        $clientId:  '%env(TOORNAMENT_CLIENT_ID)%'
        $clientSecret:  '%env(TOORNAMENT_CLIENT_SECRET)%'
        $tournamentId:  '%env(TOORNAMENT_TOURRNAMENT_ID)%'
        $tournamentStageId:  '%env(TOORNAMENT_TOURRNAMENT_STAGE_ID)%'
```
La déclaration des deux service pour Guzzle et le cache, puis la déclaration du service ``ToornamentService`` avec les paramètres.

###Guzzle Asynchrone

Afin de d'optimiser le temps d'appel vers l'api de toornament j'ai utilisé le système de [requête asynchrone de guzzle](http://mcamuzat.github.io/blog/2015/09/21/guzzle-asynchrone-avec-les-promises/) avec les promises.

Example  [/src/Service/ToornamentService.php](https://github.com/misterjj/tournoi/blob/master/src/Service/ToornamentService.php) dans la fonction ``getGameList(String $matchId)`` :

```php
$promises = [];
for ($i = 1; $i <= $numberOfGame; $i++) {
    $promises['game' . $i] = $this->toornamentApiGet(
        '/organizer/v2/tournaments/' . $this->tournamentId . '/matches/' . $matchId . '/games/' . $i,
        "",
        [],
        true
    );
}
$results = \GuzzleHttp\Promise\unwrap($promises);
```

##Points d'améliorations
* Enlever le hack dans la méthode ``getGameList(String $matchId)`` du ``ToornamentService`` si Toornament corige le bug.
* Utiliser [Webpack Encore](https://symfony.com/doc/current/frontend.html) afin d'avoir une gestion des statics plus propores.