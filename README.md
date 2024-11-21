# Library API Documentation 
---

## Table of Contents

1. [Library API Documentation](#library-api-documentation)
2. [Software/Application Used](#software--application-used) 
3. [Endpoints & Description](#endpoints--description)  
4. [API Components](#api-components)  
5. [Contact Information](#contact-information)
---


## Software / Application Used

1. Slim Framework
2. SQLyog
3. Thunder Client
4. Composer
---
  ## Endpoints & Description
  1. http://127.0.0.1/library/public/user/input - Create and save new user.
  2. https://127.0.0.1/Library/public/user/token - Authenticate user and get a token.
  3. https://127.0.0.1/Library/public/user/view - View user details.
  4. https://127.0.0.1/Library/public/user/delete - Delete user account.
  5. http://127.0.0.1/Library/public/book/add - Add a new book to the catalog.
  6. http://127.0.0.1/Library/public/book/add - Add another new book to the catalog.
  7. http://127.0.0.1/Library/public/book/list - List all books in the library.
  8. http://127.0.0.1/Library/public/book/view - View a specific book's details.
  9. http://127.0.0.1/Library/public/book/borrow - Borrow a book from the library.
  10. http://127.0.0.1/Library/public/book/return - Return a borrowed book.
  11. http://127.0.0.1/Library/public/author/list - List all authors in the library.
  12. http://127.0.0.1/Library/public/author/view - View a specific author's details.
---

## API Components

### 1. Create New User
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
  - **Method:** `GET`  
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
  
  ### 3. View User Info
  - **Method:** `GET`  
  - **Endpoint:** `https://127.0.0.1/Library/public/user/view`  
  
  - **Request:**
    ```json
    {
      "token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzIxNzMwODgsImV4cCI6MTczMjE3MzIwOCwiZGF0YSI6eyJ1c2VyaWQiOiI5IiwibmFtZSI6IkxheWwifX0.ENpzk2x3D2U2MvwXpPtUXehwQclVwquDBx4lSeZWNLQ"
    }
    ```
  - **Response:**
    - **Success:** 
      ```json
      {
        "status": "success",
        "user": [
          {
            "userid": "9",
            "username": "Layl",
            "password": "a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3",
            "name": "Layl",
            "gender": "Male",
            "age": "18"
          }
        ]
      }
      ```
    - **Fail:** 
      ```json
      {
        "status": "fail",
        "data": {
          "title": "Invalid or Expired Token"
        }
      }
      ```


  ### 4. Delete User Info
  - **Method:** `DELETE`  
  - **Endpoint:** `https://127.0.0.1/Library/public/user/delete`  
  
  - **Request:**
    ```json
    {
      "token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzIxNzMwODgsImV4cCI6MTczMjE3MzIwOCwiZGF0YSI6eyJ1c2VyaWQiOiI5IiwibmFtZSI6IkxheWwifX0.ENpzk2x3D2U2MvwXpPtUXehwQclVwquDBx4lSeZWNLQ"
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
          "title": "Invalid or Expired Token"
        }
      }
      ```

### 5. Change User Password
  - **Method:** `POST`  
  - **Endpoint:** `https://127.0.0.1/Library/public/user/password`  
  
  - **Request:**
    ```json
    {
      "password":"newPass",
      "token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzIxNzMwODgsImV4cCI6MTczMjE3MzIwOCwiZGF0YSI6eyJ1c2VyaWQiOiI5IiwibmFtZSI6IkxheWwifX0.ENpzk2x3D2U2MvwXpPtUXehwQclVwquDBx4lSeZWNLQ"
    }
    ```
  - **Response:**
    - **Success:** 
      ```json
      {
        "status": "success"
      }
      ```
    - **Fail:** 
      ```json
      {
        "status": "fail",
        "data": {
          "title": "Invalid or Expired Token"
        }
      }
      ```


  ### 6. Add New Book
  - **Method:** `POST`  
  - **Endpoint:** `http://127.0.0.1/Library/public/book/add`  
  
  - **Request:**
    ```json
    {
      "title":"IT",
      "genre" :"Horror",
      "pages":"19",
      
      
      "token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3Mjk1NjM2NjcsImV4cCI6MTcyOTU2Mzc4NywiZGF0YSI6eyJ1c2VyaWQiOiI2IiwibmFtZSI6IkZlcmIifX0.g_ppNRVIVlZJWPbBNwgZwKMRuYALuPUgWPw8ovaEb6k"
    }
    ```
  - **Response:**
    - **Success:** 
      ```json
      {
        "status": "success"
      }
      ```
    - **Fail:** 
      ```json
      {
        "status": "fail",
        "data": {
          "title": "IBook token already used or not found"
        }
      }
      ```


### 7. View Book List
  - **Method:** `GET`  
  - **Endpoint:** `http://127.0.0.1/Library/public/book/list`  
  
  - **Request:**
    ```json
    {
      "token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzIxNzM4NjksImV4cCI6MTczMjE3Mzk4OSwiZGF0YSI6eyJ1c2VyaWQiOiIxMiIsIm5hbWUiOiJMYXlsYSJ9fQ.QLMwMxsLUKUG4iLfj_mr_T7GcrvcO6C8qXyqOlb60vU"
    }
    ```
  - **Response:**
    - **Success:** 
      ```json
      {
        "status": "success",
        "books": [
          {
            "bookid": "1",
            "title": "Rapanzel",
            "author": "Arthur",
            "book_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzIxNzM4ODIsImV4cCI6MTczMjE3NDQ4MiwiZGF0YSI6eyJib29raWQiOiIxIn19.4Gs8qz8amLQ4WjIjKuPQRf8_2ZwGigZsSDQ7fCSPbWA"
          }
          {
            "bookid": "5",
            "title": "IT",
            "author": "Layla",
            "book_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzIxNzM4ODIsImV4cCI6MTczMjE3NDQ4MiwiZGF0YSI6eyJib29raWQiOiI1In19.iSUc7k1Q52GHAXv_oaZEi3Z59cPe_SdVDS6yDlapxZg"
          }
        ]
      }
      ```
    - **Fail:** 
      ```json
      {
        "status": "fail",
        "data": {
          "title": "Invalid or Expired Token"
        }
      }
      ```

### 8. View Book
  - **Method:** `GET`  
  - **Endpoint:** `http://127.0.0.1/Library/public/book/view`  
  
  - **Request:**
    ```json
    {
      "book_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzIxNzQxMzAsImV4cCI6MTczMjE3NDczMCwiZGF0YSI6eyJib29raWQiOiI1In19.6xULPKpekd28UZSYmS9i8eWUO8x5KgPEfzi5OqDLk-E"
    }
    ```
  - **Response:**
    - **Success:** 
      ```json
      {
        "status": "success",
        "books": [
          {
            "bookid": "5",
            "title": "IT",
            "genre": "Horror",
            "pages": "19",
            "author": "Layla",
            "status": "available"
          }
        ],
        "book_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzIxNzQxNDIsImV4cCI6MTczMjE3NDc0MiwiZGF0YSI6eyJib29raWQiOiI1In19.x6p1rCBCVHHOucMPENv0T862zvqQf4YfbXkH0bnHg8w"
      }
      ```
    - **Fail:** 
      ```json
      {
        "status": "fail",
        "data": {
          "title": "Book token already used or not found"
        }
      }
      ```

  ### 9. Borrow Book
  - **Method:** `POST`  
  - **Endpoint:** `http://127.0.0.1/Library/public/book/borrow`  
  
  - **Request:**
    ```json
    {
      "book_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzIxNzQxNDIsImV4cCI6MTczMjE3NDc0MiwiZGF0YSI6eyJib29raWQiOiI1In19.x6p1rCBCVHHOucMPENv0T862zvqQf4YfbXkH0bnHg8w"
    }
    ```
  - **Response:**
    - **Success:** 
      ```json
      {
        "status": "success",
        "return_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzIxNzQxOTcsImV4cCI6MTczMjE3NDc5NywiZGF0YSI6eyJib29raWQiOiI1In19.L7PNeK8APWsq92Z9OS6s3qz-w0JsRADYZA_KIy-gYRI"
      }
      ```
    - **Fail:** 
      ```json
      {
        "status": "fail",
        "data": {
          "title": "Book token already used or not found"
        }
      }
      ```


### 10. Return Book
  - **Method:** `POST`  
  - **Endpoint:** `http://127.0.0.1/Library/public/book/return`  
  
  - **Request:**
    ```json
    {
      "return_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzIxNzQxOTcsImV4cCI6MTczMjE3NDc5NywiZGF0YSI6eyJib29raWQiOiI1In19.L7PNeK8APWsq92Z9OS6s3qz-w0JsRADYZA_KIy-gYRI"
    }
    ```
  - **Response:**
    - **Success:** 
      ```json
      {
        "status": "success"
      }
      ```
    - **Fail:** 
      ```json
      {
        "status": "fail",
        "data": {
          "title": "Token already used or not found"
        }
      }
      ```

### 11. View Author List
  - **Method:** `GET`  
  - **Endpoint:** `http://127.0.0.1/Library/public/author/list`  
  
  - **Request:**
    ```json
    {
      "token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzIxNzQ0NjAsImV4cCI6MTczMjE3NDU4MCwiZGF0YSI6eyJ1c2VyaWQiOiIxMiIsIm5hbWUiOiJMYXlsYSJ9fQ.BQdkM1OqYYH2ZnTCdbRk2xaI3X3np4K5ICW2_XasxUw"
    }
    ```
  - **Response:**
    - **Success:** 
      ```json
      {
        "status": "success",
        "authors": [
          {
            "authorid": "1",
            "author": "Arthur",
            "author_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzIxNzQ0NzUsImV4cCI6MTczMjE3NTA3NSwiZGF0YSI6eyJhdXRob3IiOiJBcnRodXIifX0.7QUKRroMoYlUPgKiOh7EQrzGcOhiBBb0NyMfFjw8iwU"
          },
          {
            "authorid": "4",
            "author": "Layla",
            "author_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzIxNzQ0NzUsImV4cCI6MTczMjE3NTA3NSwiZGF0YSI6eyJhdXRob3IiOiJMYXlsYSJ9fQ.1E02Um7o-0oZmFvFJRY6lPqr4LW2OosQSiy1cms8-ZA"
          }
        ]
      }
      ```
    - **Fail:** 
      ```json
      {
        "status": "fail",
        "data": {
          "title": "Token already used"
        }
      }
      ```

### 12. View Author
  - **Method:** `GET`  
  - **Endpoint:** `http://127.0.0.1/Library/public/author/view`  
  
  - **Request:**
    ```json
    {
      "author_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeS5jb20iLCJpYXQiOjE3MzIxNzQ0NzUsImV4cCI6MTczMjE3NTA3NSwiZGF0YSI6eyJhdXRob3IiOiJMYXlsYSJ9fQ.1E02Um7o-0oZmFvFJRY6lPqr4LW2OosQSiy1cms8-ZA"
    }
    ```
  - **Response:**
    - **Success:** 
      ```json
      {
        "status": "success",
        "author": [
          "Layla",
          [
            {
              "bookid": "5",
              "title": "IT",
              "genre": "Horror",
              "pages": "19",
              "author": "Layla",
              "status": "available"
            }
          ]
        ]
      }
      ```
    - **Fail:** 
      ```json
      {
        "status": "fail",
        "data": {
          "title": "Token already used"
        }
      }
      ```
---

## Contact Information

#### User: Layl-QT
#### Facebook: Lyle Monis
#### Gmail: monislyle@gmail.com
#### Contact Number: 09777540158
#### Gcash: 09858485821 (Badly needed)

![My Face](https://mystickermania.com/cdn/stickers/anime/noragami-yato-cat-smile-512x512.png)

---
