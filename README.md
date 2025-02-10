## Požadavky

- Nainstalovaný Docker & Docker Compose nebo Docker Desktop.

## Spuštění

1. V rootu projektu spustit ``` docker-compose up -d ```

Příklad URL:
- http://localhost:8000/api/v1/products
- http://localhost:8000/api/v1/product/create + JSON Body: ``` {"name": "Hříbeček", "price": 7.1, "stock": 12} ```

OpenAPI Schema: http://localhost:8000/api/v1/openapi/schema

## Testy
PHPStan ``` vendor/bin/phpstan analyse ```

Api Codeception
1. V souboru .env zapnout ``` ENV=test ```
2. Restartovat kontejnery ``` docker-compose down / docker-compose up -d ```
3. ``` vendor/bin/codecept run Api ```

Případnou CRLF chybu ``` '/usr/bin/env: ‘php\r’: No such file or directory' ``` při spouštění testů lze opravit přes ``` dos2unix vendor/bin/codecept ``` nebo ``` dos2unix vendor/bin/phpstan ```


## K dokončení

Unit testy, Middleware testy, Bulk/Batch endpointy, Response Decoratory


## Postman Schema Import
```
{
"info": {
"_postman_id": "6869a197-ab23-4979-9432-d6a3373fc6bd",
"name": "fox",
"schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json",
"_exporter_id": "41781871"
},
"item": [
{
"name": "Products Price History",
"request": {
"method": "GET",
"header": [],
"url": {
"raw": "http://localhost:8000/api/v1/products/price-history",
"protocol": "http",
"host": [
"localhost"
],
"port": "8000",
"path": [
"api",
"v1",
"products",
"price-history"
],
"query": [
{
"key": "name",
"value": "oĹ™ech",
"disabled": true
},
{
"key": "after",
"value": "2025-01-01T12:00:00",
"disabled": true
}
]
}
},
"response": []
},
{
"name": "Products",
"request": {
"method": "GET",
"header": [],
"url": {
"raw": "http://localhost:8000/api/v1/products",
"protocol": "http",
"host": [
"localhost"
],
"port": "8000",
"path": [
"api",
"v1",
"products"
]
}
},
"response": []
},
{
"name": "Product",
"request": {
"method": "POST",
"header": [],
"body": {
"mode": "raw",
"raw": "{\r\n \"name\": \"HĹ™Ă­beÄŤek\",\r\n \"price\": 7.1,\r\n \"stock\": 12\r\n}",
"options": {
"raw": {
"language": "json"
}
}
},
"url": {
"raw": "http://localhost:8000/api/v1/product/create",
"protocol": "http",
"host": [
"localhost"
],
"port": "8000",
"path": [
"api",
"v1",
"product",
"create"
]
}
},
"response": []
},
{
"name": "Product",
"request": {
"method": "DELETE",
"header": [],
"url": {
"raw": "http://localhost:8000/api/v1/products/1/delete",
"protocol": "http",
"host": [
"localhost"
],
"port": "8000",
"path": [
"api",
"v1",
"products",
"1",
"delete"
]
}
},
"response": []
},
{
"name": "Product",
"request": {
"method": "PATCH",
"header": [
{
"key": "Content-Type",
"value": "application/json",
"type": "text",
"disabled": true
}
],
"body": {
"mode": "raw",
"raw": "{\r\n \"price\": 3,\r\n \"stockMod\": 12\r\n}",
"options": {
"raw": {
"language": "json"
}
}
},
"url": {
"raw": "http://localhost:8000/api/v1/products/1/edit",
"protocol": "http",
"host": [
"localhost"
],
"port": "8000",
"path": [
"api",
"v1",
"products",
"1",
"edit"
]
}
},
"response": []
}
]
}
