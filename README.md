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
### 2. Authenticate User
- **Method:** `Get`  
- **Endpoint:** `https://127.0.0.1/Library/public/user/token`  

- **Request:**
  ```json
  {
  "username":"Layl",
  "password":"123"
  }
  ```
- **Response:**
  - **Success:** 
    ```json
    {
      "status": "success",
      "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzIxMjMwMjgsImV4cCI6MTczMjEyMzE0OCwiZGF0YSI6eyJ1c2VyaWQiOiI5IiwibmFtZSI6IkxheWwifX0.Mw8lVtZRa5LHADWkKHX3r2mJzu10KGfAAIDnV_8MjOA",
      "data": null
    }
    ```
  - **Fail:** 
    ```json
    {
      "status": "fail",
      "data": {
        "title": "Authentication Failed"
      }
    }
    ```