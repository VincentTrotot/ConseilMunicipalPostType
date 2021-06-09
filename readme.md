# ConseilMunicipalPostType

ConseilMunicipalPostType est un package permettant d'ajouter un Custom Post Type `vt_cm` à un theme WordPress.  
Ce Custom Post Type ajoute six metadata : un ordre du jour (nom et lien du fichier PDF), un compte rendu (nom et lien du fichier PDF) et un compte rendu audio (nom et lien du fichier mp3).

La classe `VincentTrotot\ConseilMunicipalPostType` paramètre le Custom Post Type tandis que la classe `VincentTrotot\ConseilMunicipal\ConseilMunicipal` est une espèce de wrapper du Post (la classe hérite de la classe `Timber\TimberPost`).

## Installation

```bash
composer require vtrotot/conseil-municipal-post-type
```

## Utilisation

Votre theme doit instancier la classe `ConseilMunicipalPostType`

```php
new VincentTrotot\ConseilMunicipal\ConseilMunicipalPostType();
```

Vous pouvez ensuite récupérer un Post de type ConseilMunicipal:

```php
$post = new VincentTrotot\ConseilMunicipal\ConseilMunicipal();
```

Ou récupérer plusieurs posts avec :

```php
$args = [
    'post_type' => 'vt_cm',
    ...
];
$posts = new Timber\TimberRequest($args, VincentTrotot\ConseilMunicipal\ConseilMunicipal::class);
```
