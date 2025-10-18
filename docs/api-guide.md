# Dhiraagu Bulk SMS API Documentation

## Overview

Dhiraagu Bulk SMS messaging allows you to send SMS through POST and GET endpoints.

## V1 API

### Base URL

```
https://messaging.dhiraagu.com.mv/v1/api
```

---

## `POST /sms` - Send messages to single/multiple destinations

### Request Body

The request body should be a JSON object with the following fields:

#### `destination` (Array of Strings)

- **Description**: An array of strings representing the destination of the content. Each destination should have a length of at least 10 characters.
- **Example**:

```json
"destination": [
  "string"
]
```

#### `content` (String)

- **Description**: The SMS content that is being sent.
- **Example**:

```json
"content": "string"
```

#### `source` (String, Optional)

- **Description**: This is the sender ID.
- **Example**:

```json
"source": "string"
```

#### `authorizationKey` (String)

- **Description**: Base64 encoded username and password. Should be formatted as `username:password` and then encoded to base64 for it to work (e.g., `dXNlcm5hbWU6cGFzc3dvcmQ=`).
- **Example**:

```json
"authorizationKey": "string"
```

### Example Request

```json
{
  "destination": ["960XXXXXXX", "960XXXXXXX"],
  "content": "This is an example sms",
  "source": "Test",
  "authorizationKey": "dXNlcm5hbWU6cGFzc3dvcmQ="
}
```

### Example cURL

```bash
curl --location 'https://messaging.dhiraagu.com.mv/v1/api/sms' \
--header 'Content-Type: application/json' \
--data '{
  "destination": ["960XXXXXXX"],
  "content": "Test SMS",
  "source": "Test",
  "authorizationKey": "your-key"
}'
```

### Response

#### Success (HTTP 200)

Message was successfully sent.

```json
{
  "transactionId": "319075e0-25a3-4a4b-a330-30c1dbb865fd",
  "transactionStatus": "true",
  "transactionDescription": "Message was sent for delivery",
  "referenceNumber": "060806032411233232311216"
}
```

#### Error (HTTP 4xx, 5xx)

In case of an error, the server will respond with an error message.

```json
{
  "transactionId": "e3f94753-8a4c-4349-9d76-680ae9da2880",
  "transactionStatus": "false",
  "transactionDescription": "Failed to send message",
  "referenceNumber": ""
}
```

#### Error (HTTP 401)

In case of incorrect credentials, the server will respond with an error message.

```json
{
  "transactionId": "e3f94753-8a4c-4349-9d76-321ae9da2880",
  "transactionStatus": "false",
  "transactionDescription": "Incorrect credentials",
  "referenceNumber": ""
}
```

---

## `GET /sms` - Send messages to single destination

The `destination` and `content` are the same as from the `POST` endpoint, and `key` is the `authorizationKey`.

### Example Request

```
GET /sms?key=your_key&content=Test SMS&destination=960XXXXXXX&source=Test
```

### Example cURL

```bash
curl --location 'https://messaging.dhiraagu.com.mv/v1/api/sms?key=your_key&content=Test%20SMS&destination=960XXXXXXX&source=Test'
```

### Response

#### Success (HTTP 200)

Message was successfully sent.

```json
{
  "transactionId": "e3f94753-8a4c-4349-9d76-680ae9da2880",
  "transactionStatus": "true",
  "transactionDescription": "Message was sent for delivery",
  "referenceNumber": "060806032411233232311216"
}
```

#### Error (HTTP 4xx, 5xx)

In case of an error, the server will respond with an error message.

```json
{
  "transactionId": "e3f94753-8a4c-4349-9d76-680ae9da2880",
  "transactionStatus": "false",
  "transactionDescription": "Failed to send message",
  "referenceNumber": ""
}
```

#### Error (HTTP 401)

In case of incorrect credentials, the server will respond with an error message.

```json
{
  "transactionId": "e3f94753-8a4c-4349-9d76-321ae9da2880",
  "transactionStatus": "false",
  "transactionDescription": "Incorrect credentials",
  "referenceNumber": ""
}
```
