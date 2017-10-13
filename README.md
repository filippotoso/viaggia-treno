# Viaggia Treno (Trenitalia) Unofficial API Client

A simple client for viaggiatreno.it unofficial API.

## Requirements

- PHP 5.6+
- guzzlehttp/guzzle 6.2+
- nesbot/carbon ^1.22+

## Installing

Use Composer to install it:

```
composer require filippo-toso/viaggia-treno
```

## Using It

```
use FilippoToso\ViaggiaTrenoAPI\Client as ViaggiaTreno;

// Create the client
$client = new ViaggiaTreno();

// Get the starting station and other details
$results = $client->cercaNumeroTrenoTrenoAutocomplete('8526');
var_dump($results);

// Get the current train status
$results = $client->andamentoTreno('S08409', '8526');
var_dump($results);

// Get the trian stops details
$results = $client->tratteCanvas('S08409', '8526');
var_dump($results);

// Get the station departures at specified time
$results = $client->partenze('S08409', '2017-10-13 21:43:00');
var_dump($results);

```
