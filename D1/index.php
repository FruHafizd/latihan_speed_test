<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roman Converter</title>
    <style type="text/css">
        .box{
            border-radius:10px;
            padding:10px;
            border:solid 1px rgba(0,0,0,0.2);
            margin-top:10px;
            min-height: 20px;
        }
        .date{
            display:block;
            color:rgba(0,0,0,0.6);
            margin-bottom: 10px;
        }
        h3{
            margin:0;
        }
        .roman-bold {
            font-weight: bold;
            color: #000;
        }
    </style>
</head>
<body>
    <h1>Roman Converter</h1>
    <form method="post">
        Input Text : <input type="text" name="inputText" placeholder="Enter text with numbers" value="<?php echo isset($_POST['inputText']) ? htmlspecialchars($_POST['inputText']) : ''; ?>"> 
        <button type="submit" name="convert">Convert</button>
    </form>
    <div class="box">
        <?php
        if (isset($_POST['convert']) && !empty($_POST['inputText'])) {
            $input = $_POST['inputText'];
            echo convertNumbersToRoman($input);
        }

        function convertNumbersToRoman($text) {
            // Find all numbers in the text
            preg_match_all('/\d+/', $text, $matches);
            
            foreach ($matches[0] as $number) {
                if (is_numeric($number) && $number > 0 && $number < 4000) {
                    $roman = intToRoman($number);
                    // Replace number with bold Roman numeral
                    $text = preg_replace('/\b'.$number.'\b/', '<span class="roman-bold">'.$roman.'</span>', $text);
                }
            }
            
            return $text;
        }

        function intToRoman($num) {
            $num = intval($num);
            $result = '';
            
            $lookup = array(
                'M' => 1000,
                'CM' => 900,
                'D' => 500,
                'CD' => 400,
                'C' => 100,
                'XC' => 90,
                'L' => 50,
                'XL' => 40,
                'X' => 10,
                'IX' => 9,
                'V' => 5,
                'IV' => 4,
                'I' => 1
            );
            
            foreach ($lookup as $roman => $value) {
                $matches = intval($num / $value);
                $result .= str_repeat($roman, $matches);
                $num = $num % $value;
            }
            
            return $result;
        }
        ?>
    </div>
</body>
</html>