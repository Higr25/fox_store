## Požadavky

- Nainstalovaný Docker & Docker Compose nebo Docker Desktop.

## Spuštění

1. V rootu projektu spustit ``` docker-compose up -d ```

Příklad URL: http://localhost:8000/api/v1/products

OpenAPI Schema: http://localhost:8000/api/v1/openapi/schema

## Testy
PHPStan ``` vendor/bin/phpstan analyse ```

Api Codeception
1. V souboru .env zapnout ``` ENV=test ```
2. Restartovat kontejnery ``` docker-compose down / docker-compose up -d ```
3. ``` vendor/bin/codecept run Api ```

Případnou chybu ``` '/usr/bin/env: ‘php\r’: No such file or directory' ``` při spouštění testů lze opravit přes ``` dos2unix vendor/bin/codecept ``` nebo ``` dos2unix vendor/bin/phpstan ```


## K dokončení
Zde jsou zahrnuty všechny části, které jsem nepřidal kvůli časové náročnosti.

Unit testy, Middleware testy, Bulk/Batch endpointy, CI/CD Pipeliny
