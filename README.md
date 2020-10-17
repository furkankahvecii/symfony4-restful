# Symfony4 RESTFUL API Dökümantasyonu
ABC Company tedarikçi bir firmadır ve 3 müşterisi vardır. Bu müşterilerin kendilerine ait kullanıcı adları ve şifreleri vardır. Müşteriler Restful servislerini kullanarak sipariş
oluşturabilir ve görebilirler. Bu beklentiler doğrultusunda aşağıda listelenen servisleri hazırlamanı bekliyoruz.

İstenen servisler:
- Sisteme login olma ve JWT Token alma
- Yeni sipariş oluşturma (orderCode, productid, quantity, address, shippingDate)
- Siparişi güncelleme (shippingDate henüz gelmediyse)
- Sipariş detayını görme
- Tüm siparişlerini listeleme


## Önemli Notlar
- API geliştirdiğimiz için ```php symfony/skeleton my_project_name --version= 4.0``` ile yapı kuruldu. Temel olarak bir 'FOSRestBundle' paketi kullanılarak RESTFUL API geliştirildi. Resourcelarımız mysql veritabanında tutuldu. JWT için ```"lexik/jwt-authentication-bundle"``` kullanıldı.
- Programı indirdikten sonra, .env dosyasındaki DATABASE_URL keyine karşılık gelen değeri sizde bulunan mysql'e göre ayarlamanız gerekmektedir. Örnek olarak mysql://root:@127.0.0.1:3306/restful. (mysql://username:password@hostname:mysqlport/dbname)
- symfony server:start ile web serverı çalıştırabiliriz. (http://127.0.0.1:8000/)
- Kullanılan <a href="https://dbdiagram.io/d/5f8a9f313a78976d7b780041" target="_blank"> DB Diagram </a>
-  <a href="https://www.postman.com/collections/cb8c99df853e1d9f051a" target="_blank"> Postman Collection </a>

## Authentication
- /register ve /login pathleri haric diger pathler header kısmında JWT gönderilerek yapılmalıdır. Yollanmazsa eğer "JWT Token not found" hatası alınır.
- JWT TTL(Time To Live) 3600 saniyedir, alındıktan 3600 saniye sonra JWT yollanırsa "Expired JWT" response alınır. (Code = 401)
- Geçersiz JWT yollanırsa "Invalid JWT Token" response alınır.
- Gönderilen JWT geçerli ise, kullanıma açık olan servislere erişilebilir. Her servisteki işlemler JWT'deki usera göre yapılır.


## Sisteme Kayıt Olma (Register)
**Gönderilen:**  Kayıt kimlik bilgileri
**Elde edilen:** Sistemde oturum açma fırsatı

**Request:**
```json
POST /register HTTP/1.1
Accept: application/json
Content-Type: application/json
Content-Length: xy

{
    "_username": "test_user",
    "_password": "test_password" 
}
```
**Successful Response:**
```json
HTTP/1.1 201 Created
Server: My RESTful API
Content-Type: application/json
Content-Length: xy

{
   "message" : "User test_user successfully created"
}
```
**Failed Response:**
```json
HTTP/1.1 500 Server Error
Server: My RESTful API
Content-Type: application/json
Content-Length: xy

{
    "code": 500,
    "message": "Integrity constraint violation: 1062 Duplicate entry 'test_user' for key 'UNIQ_1483A5E9F85E0677'"
}
``` 


## JWT Token Alma
**Gönderilen:**  Giriş kimlik bilgileri
**Elde edilen:** `JWT-Token`

**Request:**
```json
POST /login_check HTTP/1.1
Accept: application/json
Content-Type: application/json
Content-Length: xy

{
    "username": "test_user",
    "password": "test_password" 
}
```
**Successful Response:**
```json
HTTP/1.1 200 OK
Server: My RESTful API
Content-Type: application/json
Content-Length: xy

{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MDI5MjM2ODksImV4cCI6MTYwMjkyNzI4OSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdF91c2VybmFtZTIifQ.Ax_5TjrU_aID5lonG8ENaxpL-9-NYdgOu_5Ly9CC-3Bi4LU7vzuqq_OdQ8FvMtdTw3qoLMJp2RJ9L86B4qHTicZMdOiOJ8aMY_tlQQRY1p2Bx3weSK1p4VHdRLl20aEOZLFyBJCfPia4EqZidzzlmD8mrr_-atSQ1eD2VWQmqbT5ux2p5Rqg768aut1w3Se2xIuU_ijmtgXtngN_OyPpUxTYXWLXc4690i0BQYhPgHFk8EIm2qa3ZboumPnle6uywIX43PL6-ORWknmuoPrah7QV0oKCTCeFsxBQlbJwlzpPaWBFSCRI9aWAJahZodK2pq7BPFFspmy9wprJXasxEg"
}
```
**Failed Response:**
```json
HTTP/1.1 401 Unauthorized
Server: My RESTful API
Content-Type: application/json
Content-Length: xy

{
    "code": 401,
    "message": "Invalid credentials."
}
``` 


## Yeni Sipariş Oluşturma
**Gönderilen:**  (orderCode, productid, quantity, address, shippingDate) ve JWT
**Elde edilen:** Siparişin oluşturulması

userid = JWT'ye göre belirlenecek

**Request:**
```json
POST /addorder HTTP/1.1
Accept: application/json
Content-Type: application/json
Content-Length: xy

{
    "orderCode": "orderCode_example",
    "productId": 1,
    "quantity": 10,
    "address": "address_example",
    "shippingDate": "2020-10-17 12:42:45"
}
```
**Successful Response:**
```json
HTTP/1.1 201 Created
Server: My RESTful API
Content-Type: application/json
Content-Length: xy

{
    "id": 1,
    "userId": 1,
    "orderCode": "orderCode_example",
    "productId": "1",
    "quantity": "10",
    "address": "address_example",
    "shippingDate": "2020-10-17T12:42:45+03:00"
}
```
**Failed Response:**
```json
HTTP/1.1 500 Server Error
Server: My RESTful API
Content-Type: application/json
Content-Length: xy

{
    "code": 500,
    "message": "Integrity constraint violation: 1062 Duplicate entry 'orderCode_example' for key 'UNIQ_E52FFDEE3AE40A8F'"
}
``` 

## Siparişi güncelleme
**Gönderilen:**  (orderCode, productid, quantity, address, shippingDate) ve JWT
**Elde edilen:** Siparişin güncellemesi

orderCode göre güncelleme yapılır. ShippingDate gelmediyse, gönderilen parametreler güncellenir.

**Request:**
```json
PUT /updateorder HTTP/1.1
Accept: application/json
Content-Type: application/json
Content-Length: xy

{
    "orderCode": "orderCode_example",
    "productId": 3,
    "quantity": 10,
    "address": "address_example",
    "shippingDate": "2020-10-17 12:42:45"
}
```
**Successful Response:**
```json
HTTP/1.1 200 OK
Server: My RESTful API
Content-Type: application/json
Content-Length: xy

ShippingDate Geldiyse

{
    "id": 1,
    "userId": 1,
    "orderCode": "orderCode_example",
    "productId": "3",
    "quantity": "10",
    "address": "address_example",
    "shippingDate": "2020-10-17T12:42:45+03:00"
}


ShippingDate Gelmediyse
{
    "now_datetime": "2020-10-17 11:11:11",
    "shipping_date": "2020-10-14 12:42:45",
    "result": "shippingdate gelmedi, update yapılamadi."
}

```
**Failed Response:**
```json
HTTP/1.1 500 Server Error
Server: My RESTful API
Content-Type: application/json
Content-Length: xy

{
    "code": 500,
    "message": "Notice: Undefined index: (yanlis gönderilen parametreler keyleri veya gönderilmeyen parametreler)"
}
``` 

## Sipariş detayını görme
**Gönderilen:**  (orderCode) ve JWT
**Elde edilen:** Siparişin detayları

orderCode ait sipariş response edilir.

**Request:**
```json
GET /detailorder?orderCode=orderCodeExample HTTP/1.1

```
**Successful Response:**
```json
HTTP/1.1 200 OK
Server: My RESTful API
Content-Type: application/json
Content-Length: xy

{
    "order": [
        {
            "id": 1,
            "userid": 2,
            "orderCode": "orderCodeExample2",
            "productId": 1,
            "quantity": 10,
            "adress": "adressExample",
            "shippingDate": {
                "date": "2020-10-18 12:42:45.000000",
                "timezone_type": 3,
                "timezone": "Europe/Istanbul"
            }
        }
    ]
}
```
**Failed Response:**
```json
HTTP/1.1 404 Cannot be found
Server: My RESTful API
Content-Type: application/json
Content-Length: xy

{
    "code": 404,
    "message": "No order found"
}
``` 


## Tüm siparişlerini listeleme
**Gönderilen:**  JWT
**Elde edilen:** Tüm siparişler

**Request:**
```json
GET /orders HTTP/1.1

```
**Successful Response:**
```json
HTTP/1.1 200 OK
Server: My RESTful API
Content-Type: application/json
Content-Length: xy

{
    "orders": [
        {
            "id": 1,
            "userid": 2,
            "orderCode": "orderCodeExample2",
            "productId": 1,
            "quantity": 10,
            "adress": "adressExample",
            "shippingDate": {
                "date": "2020-10-18 12:42:45.000000",
                "timezone_type": 3,
                "timezone": "Europe/Istanbul"
            }
        },
        {
            "id": 3,
            "userid": 2,
            "orderCode": "orderCodeExample23",
            "productId": 1,
            "quantity": 10,
            "adress": "address_example",
            "shippingDate": {
                "date": "2020-10-17 12:42:45.000000",
                "timezone_type": 3,
                "timezone": "Europe/Istanbul"
            }
        },
        {
            "id": 5,
            "userid": 2,
            "orderCode": "orderCodeExample234",
            "productId": 1,
            "quantity": 10,
            "adress": "address_example",
            "shippingDate": {
                "date": "2020-10-17 12:42:45.000000",
                "timezone_type": 3,
                "timezone": "Europe/Istanbul"
            }
        }
    ]
}
```
**Failed Response:**
```json
HTTP/1.1 404 Cannot be found
Server: My RESTful API
Content-Type: application/json
Content-Length: xy

{
    "code": 404,
    "message": "No orders found"
}
``` 
