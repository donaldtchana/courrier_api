# productsAPI

### Note: The API only accepts JSON data

## API endpoints

- <http://localhost/products>
- <http://localhost/products/{id}>
- <http://localhost/products/{name}>

## Get all products  

### Request a list of products

```http
GET http://localhost/products
```

### response

```json

[
  {
    "productId": 1,
    "nom": "iPhone 13",
    "prix": 1000,
    "quantite": 100,
    "description": "The latest and greatest iPhone"
  },
  {
    "productId": 2,
    "nom": "MacBook Pro",
    "prix": 1500,
    "quantite": 50,
    "description": "The most powerful MacBook"
  },
  {
    "productId": 3,
    "nom": "AirPods Pro",
    "prix": 250,
    "quantite": 200,
    "description": "The best wireless earbuds"
  },
  {
    "productId": 4,
    "nom": "TV",
    "prix": 350,
    "quantite": 100,
    "description": "42\" wide smart screen"
  }
]

```

## Get product by id

### Request a particular product

```http
GET http://localhost/products/1
```

### Response

```json
{
  "productId": 1,
  "nom": "iPhone 13",
  "prix": 1000,
  "quantite": 100,
  "description": "The latest and greatest iPhone"
}
```

## Get product by name

### Request

```http
GET http://localhost/products/TV
```

### Response

```json
{
  "productId": 4,
  "nom": "TV",
  "prix": 350,
  "quantite": 100,
  "description": "42\" wide smart screen"
}
```

## Create a  product

### Request

```http
POST http://localhost/products

{
  "nom": "iPhone 14",
  "prix": 1500,
  "quntite": 50,
  "description": "The latest iPhone on the market"
}
```

### Response 

```json
{
  "message": "Product created",
  "id": "5"
}
```

## Update a product

### Request

```http
PATCH http://localhost/products/5

{
  "prix": 1750,
  "quntite": 80
}
```

### Response

```json
{
  "message": "Product 5 updated",
  "rows": 1
}
```

## Delete a product

### Request

```http
DELETE http://localhost/products/5
```

### Response

```json
{
    "message": "Product 5 deleted",
    "rows": 1
}
```