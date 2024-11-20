# Library API

## User Management

### 1. Create User
- **Method:** `POST`  
- **Endpoint:** `/user/input`  
- **Request:**
```json
{
  "name":"Layl",
  "age":"18",
  "gender":"Male",
  
  "username":"Layl",
  "password":"123"
}
```
-- **Response:** 
  ```json
{
  "status": "success",
  "data": null
}
  ```
