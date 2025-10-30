<?php
if (http_response_code() !== 404) {
    http_response_code(404);
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>404 Not Found</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 2rem; color: #333; }
    h1 { color: #c00; }
    a { color: #06c; }
  </style>
</head>
<body>
  <h1>404 Not Found</h1>
  <p>The requested URL was not found on this server.</p>
  <p><a href="/ocr/">Go to home</a></p>
</body>
</html>
