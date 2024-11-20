<?php 
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require '../src/vendor/autoload.php';
$app = new \Slim\App;



/////////////////////////////////// U S E R ////////////////////////////

$app->post('/user/input', function (Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());
    $usr = $data->username;
    $pass = $data->password;
    $name = $data->name; 
    $age = $data->age; 
    $gender = $data->gender; 

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "INSERT INTO users (name, age, gender, username, password) VALUES ('".$name."', '".$age."', '".$gender."', '".$usr."', '".hash('SHA256',$pass)."')";
        $conn -> exec($sql);
        $response->getBody()->write(json_encode(array("status" => "success", "data" => null)));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => $e->getMessage()))));
    }
    return $response;
});



$app->post('/user/token', function (Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());
    $usr = $data->username;
    $pass = $data->password; 

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM users WHERE username = '".$usr."' AND password = '".hash('SHA256', $pass)."'";
        $stat = $conn->query($sql);
        $data = $stat->fetchAll(PDO::FETCH_ASSOC);

        if (count($data) == 1) {
            $key = 'server_hack';
            $iat = time();
            $payload = [
                'iss' => 'http://library.org',
                'aud' => 'http://library.com',
                'iat' => $iat,
                'exp' => $iat + 120,
                'data' => [
                    "userid" => $data[0]['userid'],
                    "name" => $data[0]['name']
                ]
            ];
            $jwt = JWT::encode($payload, $key, 'HS256');

           $cacheDir = 'cache/';
            if (!is_dir($cacheDir)) {
                mkdir($cacheDir, 0777, true);
            }

            $tokenFile = $cacheDir . "user_token.token";
            file_put_contents($tokenFile, $jwt . "|unused" . PHP_EOL, FILE_APPEND);
            $response->getBody()->write(json_encode(array("status" => "success", "token" => $jwt, "data" => null)));
        } else {
            $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Authentication Failed"))));
        }
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Database Error"))));
    }
    return $response;
});



$app->post('/user/view', function(Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());
    $jwt = $data->token;
    $key = 'server_hack';
    $cacheDir = 'cache/';
    $tokenFile = $cacheDir . "user_token.token";
    $tokenStatus = null;

        try {
            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
            $userid = $decoded->data->userid;
            $tokens = [];
            if (file_exists($tokenFile)) {
                $tokens = file($tokenFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            }
    
            $isTokenUsed = false;
            foreach ($tokens as $key => $tokenLine) {
                list($storedToken, $status) = explode("|", $tokenLine);
                if ($storedToken === $jwt) {
                    if ($status === "used") {
                        $isTokenUsed = true;
                        break;
                    } else {
                        $tokens[$key] = $storedToken . "|used";
                    }
                }
            }
    
            if ($isTokenUsed) {
                return $response->withJson(array("status" => "fail", "data" => array("title" => "Token already used")), 403);
            }

            file_put_contents($tokenFile, implode(PHP_EOL, $tokens) . PHP_EOL);
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "library";

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT * FROM users WHERE userid = '".$userid."'";
            $stat = $conn->query($sql);
            $users = $stat->fetchAll(PDO::FETCH_ASSOC);

            $response->getBody()->write(json_encode(array("status" => "success", "user" => $users)));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Invalid or Expired Token"))));
        }

    return $response;
});




$app->post('/user/password', function(Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());
    $pass = $data->password; 
    $jwt = $data->token;
    $key = 'server_hack';
    $cacheDir = 'cache/';
    $tokenFile = $cacheDir . "user_token.token";
    $tokenStatus = null;

    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $userid = $decoded->data->userid;
        $tokens = [];
        if (file_exists($tokenFile)) {
            $tokens = file($tokenFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }

        $isTokenUsed = false;
        foreach ($tokens as $key => $tokenLine) {
            list($storedToken, $status) = explode("|", $tokenLine);
            if ($storedToken === $jwt) {
                if ($status === "used") {
                    $isTokenUsed = true;
                    break;
                } else {
                    $tokens[$key] = $storedToken . "|used";
                }
            }
        }

        if ($isTokenUsed) {
            return $response->withJson(array("status" => "fail", "data" => array("title" => "Token already used")), 403);
        }

        // Update the token file with the new statuses
        file_put_contents($tokenFile, implode(PHP_EOL, $tokens) . PHP_EOL);

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "library";

        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "UPDATE users SET password = '".hash('SHA256',$pass)."' WHERE userid = '".$userid."'";
        $stat = $conn->query($sql);
        $users = $stat->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode(array("status" => "success")));
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Invalid or Expired Token"))));
    }

    return $response;
});



$app->post('/user/delete', function(Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());
    $jwt = $data->token;
    $key = 'server_hack';

    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $userid = $decoded->data->userid;

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "library";

        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "DELETE FROM users WHERE userid = '".$userid."'";
        $conn->exec($sql);

        $response->getBody()->write(json_encode(array("status" => "success", "data" => null)));
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Invalid or Expired Token"))));
    }

    return $response;
});











//////////////////////////// B O O K //////////////////////////////////


$app->post('/book/list', function(Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());
    $jwt = $data->token;
    $key = 'server_hack';
    $cacheDir = 'cache/';
    $bookTokenFile = $cacheDir . "book_token.token";
    $tokenFile = $cacheDir . "user_token.token";
    $tokenStatus = null;
    
    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $userid = $decoded->data->userid;

        $tokens = [];
        if (file_exists($tokenFile)) {
            $tokens = file($tokenFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }
        $isTokenUsed = false;
        foreach ($tokens as $key => $tokenLine) {
            list($storedToken, $status) = explode("|", $tokenLine);
            if ($storedToken === $jwt) {
                if ($status === "used") {
                    $isTokenUsed = true;
                    break;
                } else {
                    $tokens[$key] = $storedToken . "|used";
                }
            }
        }

        if ($isTokenUsed) {
            return $response->withJson(array("status" => "fail", "data" => array("title" => "Token already used")), 403);
        }
        file_put_contents($tokenFile, implode(PHP_EOL, $tokens) . PHP_EOL);

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "library";

        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT bookid, title, author FROM books";
        $stat = $conn->query($sql);
        $books = $stat->fetchAll(PDO::FETCH_ASSOC);

        $bookkey = 'book_hack';

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        foreach ($books as &$book) {
            $iat = time();
            $payload = [
                'iss' => 'http://library.org',
                'aud' => 'http://library.com',
                'iat' => $iat,
                'exp' => $iat + 600,
                'data' => [
                    "bookid" => $book['bookid']
                ]
            ];
            $bookjwt = JWT::encode($payload, $bookkey, 'HS256');

            file_put_contents($bookTokenFile, $bookjwt . "|unused" . PHP_EOL, FILE_APPEND);

            $book['book_token'] = $bookjwt;
        }

        $response->getBody()->write(json_encode(array("status" => "success", "books" => $books)));
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Invalid or Expired Token"))));
    }

    return $response;
});






$app->post('/book/view', function(Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());
    $bookjwt = $data->book_token;
    $bookkey = 'book_hack';
    $cacheDir = 'cache/';
    $tokenFile = $cacheDir . "book_token.token";
    $tokenStatus = null;

    if (file_exists($tokenFile)) {
        $lines = file($tokenFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            if (count($parts) === 2) {
                list($storedToken, $status) = $parts;
                if ($storedToken === $bookjwt) {
                    $tokenStatus = $status;
                    break;
                }
            } else {
                error_log("Unexpected cache line format: $line");
            }
        }
    }

    if ($tokenStatus === 'unused') {
        try {
            $decoded = JWT::decode($bookjwt, new Key($bookkey, 'HS256'));
            $bookid = $decoded->data->bookid;
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "library";

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT * FROM books WHERE bookid = '".$bookid."'";
            $stat = $conn->query($sql);
            $books = $stat->fetchAll(PDO::FETCH_ASSOC);

            $updatedLines = [];
            foreach ($lines as $line) {
                if (strpos($line, $bookjwt) === 0) {
                    $updatedLines[] = $bookjwt . "|used";
                } else {
                    $updatedLines[] = $line;
                }
            }
            file_put_contents($tokenFile, implode(PHP_EOL, $updatedLines) . PHP_EOL);

            $newBookKey = 'book_hack';
            $newPayload = [
                'iss' => 'http://library.org',
                'aud' => 'http://library.com',
                'iat' => time(),
                'exp' => time() + 600,
                'data' => [
                    "bookid" => $bookid
                ]
            ];
            $newBookJwt = JWT::encode($newPayload, $newBookKey, 'HS256');
            file_put_contents($tokenFile, $newBookJwt . "|unused" . PHP_EOL, FILE_APPEND);

            $response->getBody()->write(json_encode(array("status" => "success", "books" => $books, "book_token" => $newBookJwt)));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Invalid or Expired Token"))));
        }
    } else {
        $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Token already used or not found"))));
    }

    return $response;
});




$app->post('/book/add', function(Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());
    $jwt = $data->token;
    $title = $data->title;
    $genre = $data->genre;
    $pages = $data->pages;

    $key = 'server_hack';
    $cacheDir = 'cache/';
    $tokenFile = $cacheDir . "user_token.token";
    $tokenStatus = null;

    if (file_exists($tokenFile)) {
        $lines = file($tokenFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            
            if (count($parts) === 2) {
                list($storedToken, $status) = $parts;
                if ($storedToken === $jwt) {
                    $tokenStatus = $status;
                    break;
                }
            } else {
                error_log("Unexpected cache line format: $line");
            }
        }
    }

    if ($tokenStatus === 'unused') {
        try {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "library";

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
            $author = $decoded->data->name;
            $sql = "INSERT INTO books (title, author, genre, pages, status) VALUES ('".$title."', '".$author."', '".$genre."', '".$pages."', 'available')";
            $conn->exec($sql);

            $sqlauthor = "INSERT INTO authors (author) SELECT '".$author."' WHERE NOT EXISTS (SELECT 1 FROM authors WHERE author = '".$author."')";
            $conn->exec($sqlauthor);

            $updatedLines = [];
            foreach ($lines as $line) {
                if (strpos($line, $jwt) === 0) {
                    $updatedLines[] = $jwt . "|used";
                } else {
                    $updatedLines[] = $line;
                }
            }
            file_put_contents($tokenFile, implode(PHP_EOL, $updatedLines) . PHP_EOL);

            $response->getBody()->write(json_encode(array("status" => "success")));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Invalid or Expired Token"))));
        }
    } else {
        $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Book token already used or not found"))));
    }

    return $response;
});


$app->post('/book/borrow', function(Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());
    $bookjwt = $data->book_token;
    $bookkey = 'book_hack';
    $cacheDir = 'cache/';
    $tokenFile = $cacheDir . "book_token.token";
    $returnTokenFile = $cacheDir . "return_token.token";
    $tokenStatus = null;
    
    if (file_exists($tokenFile)) {
        $lines = file($tokenFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            if (count($parts) === 2) {
                list($storedToken, $status) = $parts;
                if ($storedToken === $bookjwt) {
                    $tokenStatus = $status;
                    break;
                }
            } else {
                error_log("Unexpected cache line format: $line");
            }
        }
    }
    
    if ($tokenStatus === 'unused') {
        try {
            $decoded = JWT::decode($bookjwt, new Key($bookkey, 'HS256'));
            $bookid = $decoded->data->bookid;
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "library";

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "UPDATE books SET status = 'borrowed' WHERE bookid = '".$bookid."'";
            $conn->exec($sql);

            $updatedLines = [];
            foreach ($lines as $line) {
                if (strpos($line, $bookjwt) === 0) {
                    $updatedLines[] = $bookjwt . "|used";
                } else {
                    $updatedLines[] = $line;
                }
            }
            file_put_contents($tokenFile, implode(PHP_EOL, $updatedLines) . PHP_EOL);

            $returnKey = 'return_hack';
            $returnPayload = [
                'iss' => 'http://library.org',
                'aud' => 'http://library.com',
                'iat' => time(),
                'exp' => time() + 600,
                'data' => [
                    "bookid" => $bookid
                ]
            ];
            $returnJwt = JWT::encode($returnPayload, $returnKey, 'HS256');
            file_put_contents($returnTokenFile, $returnJwt . "|unused" . PHP_EOL, FILE_APPEND);

            $response->getBody()->write(json_encode(array("status" => "success", "return_token" => $returnJwt)));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Invalid or Expired Token"))));
        }
    } else {
        $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Token already used or not found"))));
    }

    return $response;
});



$app->post('/book/return', function(Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());
    $returnJwt = $data->return_token;
    $returnKey = 'return_hack';
    $cacheDir = 'cache/';
    $returnTokenFile = $cacheDir . "return_token.token";
    $tokenStatus = null;
    
    if (file_exists($returnTokenFile)) {
        $lines = file($returnTokenFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            if (count($parts) === 2) {
                list($storedToken, $status) = $parts;
                if ($storedToken === $returnJwt) {
                    $tokenStatus = $status;
                    break;
                }
            } else {
                error_log("Unexpected cache line format: $line");
            }
        }
    }
    
    if ($tokenStatus === 'unused') {
        try {
            $decoded = JWT::decode($returnJwt, new Key($returnKey, 'HS256'));
            $bookid = $decoded->data->bookid;
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "library";

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "UPDATE books SET status = 'available' WHERE bookid = '".$bookid."'";
            $conn->exec($sql);

            $updatedLines = [];
            foreach ($lines as $line) {
                if (strpos($line, $returnJwt) === 0) {
                    $updatedLines[] = $returnJwt . "|used";
                } else {
                    $updatedLines[] = $line;
                }
            }
            file_put_contents($returnTokenFile, implode(PHP_EOL, $updatedLines) . PHP_EOL);

            $response->getBody()->write(json_encode(array("status" => "success")));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Invalid or Expired Token"))));
        }
    } else {
        $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Token already used or not found"))));
    }

    return $response;
});












//////////////////////////// A U T H O R //////////////////////////////////

$app->post('/author/list', function(Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());
    $jwt = $data->token;
    $key = 'server_hack';
    $cacheDir = 'cache/';
    $authorTokenFile = $cacheDir . "author_token.token";
    $tokenFile = $cacheDir . "user_token.token";
    
    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $userid = $decoded->data->userid;

        $tokens = [];
        if (file_exists($tokenFile)) {
            $tokens = file($tokenFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }
        $isTokenUsed = false;

        foreach ($tokens as $key => $tokenLine) {
            list($storedToken, $status) = explode("|", $tokenLine);
            if ($storedToken === $jwt) {
                if ($status === "used") {
                    $isTokenUsed = true;
                    break;
                } else {
                    $tokens[$key] = $storedToken . "|used";
                }
            }
        }

        if ($isTokenUsed) {
            return $response->withJson(array("status" => "fail", "data" => array("title" => "Token already used")), 403);
        }
        file_put_contents($tokenFile, implode(PHP_EOL, $tokens) . PHP_EOL);

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "library";

        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT * FROM authors";
        $stat = $conn->query($sql);
        $authors = $stat->fetchAll(PDO::FETCH_ASSOC);

        $authorKey = 'author_hack';

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        foreach ($authors as &$author) {
            $iat = time();
            $payload = [
                'iss' => 'http://library.org',
                'aud' => 'http://library.com',
                'iat' => $iat,
                'exp' => $iat + 600,
                'data' => [
                    "author" => $author['author']
                ]
            ];
            $authorJwt = JWT::encode($payload, $authorKey, 'HS256');

            file_put_contents($authorTokenFile, $authorJwt . "|unused" . PHP_EOL, FILE_APPEND);

            $author['author_token'] = $authorJwt;
        }

        $response->getBody()->write(json_encode(array("status" => "success", "authors" => $authors)));
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Invalid or Expired Token"))));
    }

    return $response;
});





$app->post('/author/view', function(Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody());
    $jwt = $data->author_token;
    $key = 'author_hack';
    $cacheDir = 'cache/';
    $tokenFile = $cacheDir . "author_token.token";
    $tokenStatus = null;

    if (file_exists($tokenFile)) {
        $lines = file($tokenFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            if (count($parts) === 2) {
                list($storedToken, $status) = $parts;
                if ($storedToken === $jwt) {
                    $tokenStatus = $status;
                    break;
                }
            } else {
                error_log("Unexpected cache line format: $line");
            }
        }
    }

    if ($tokenStatus === 'unused') {
        try {
            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
            $author = $decoded->data->author;
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "library";

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT * FROM books WHERE author = '".$author."'";
            $stat = $conn->query($sql);
            $authorlist = $stat->fetchAll(PDO::FETCH_ASSOC);

            $response->getBody()->write(json_encode(array("status" => "success", "author" => array($author, $authorlist))));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Invalid or Expired Token"))));
        }
    } else {
        $response->getBody()->write(json_encode(array("status" => "fail", "data" => array("title" => "Token already used or not found"))));
    }

    return $response;
});




$app->run();
?>
