<?php
require_once 'LexicalAnalyzer.php'; 
require_once 'SyntaxAnalyzer.php'
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lexical Analyzer</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 30%;
            margin: auto;
            padding: 20px;
        }
        header {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
        }
        header h1 {
            margin: 0;
        }
        #main {
            margin-top: 40px;
        }
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }
        input[type="submit"] {
            background-color: #e8491d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;            
            cursor: pointer;
            margin-top: 10px;
        }
        input[type="submit"]:hover {
            background-color: #c53005;
        }
        pre {
            background-color: #e8491d;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            padding: 20px;
            padding-left: 80px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1>Lexical & Syntax Analyzer</h1>
            </div>
        </div>
    </header>

    <div class="container">
        <section id="main">
            <h5>Enter a C syntax below and submit to see the tokens:</h5>
            <form action="main.php" method="post">
                <textarea name="code" rows="1" cols="50"></textarea><br>
                <input type="submit" value="Analyze">
            </form>
        </section>
    </div>
</body>
</html>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputCode = $_POST["code"];
    
    $lexicalAnalyzer = new LexicalAnalyzer();
    $tokens = $lexicalAnalyzer->tokenize($inputCode);
    
    try {
        $syntaxAnalyzer = new SyntaxAnalyzer($tokens);
        $syntaxAnalyzer->parse();
        
        echo "<div style='max-width: 30%; margin: auto; padding: 20px;'>";
            echo "<h4>The syntax is correct.</h4>";
        echo "</div>";
    } catch (Exception $e) {
        echo "<div style='max-width: 30%; margin: auto; padding: 20px;'>";
            echo $e->getMessage() ;
        echo "</div>";
    }
    
    echo "<div style='max-width: 30%; margin: auto; padding: 20px;'>";
        echo "<h4>Tokens:</h4><pre>";
            print_r($tokens);
        echo "</pre>";
    echo "</div>";
}
?>
