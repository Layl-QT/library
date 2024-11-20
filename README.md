# Library API

## User Management

### 1. Create User
- **Method:** `POST`  
- **Endpoint:** `http://127.0.0.1/library/public/user/input`  

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
- **Response:**
  - **Success:** 
    ```json
      {
        "status": "success",
        "data": null
      }
    ```
  - **Fail:** 
    ```json
      {
        "status": "fail",
        "data": {
        "title": "Username already exists"
        }
      }
    ```
