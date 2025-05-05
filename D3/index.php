<?php
session_start();

// Fungsi untuk membuat papan baru
function newBoard() {
    return [
        ['', '', ''],
        ['', '', ''],
        ['', '', '']
    ];
}

// Inisialisasi permainan jika session belum ada
if (!isset($_SESSION['board'])) {
    $_SESSION['board'] = newBoard();
    $_SESSION['winner'] = null;
    $_SESSION['gameOver'] = false;
}

// Handle reset game
if (isset($_GET['reset'])) {
    $_SESSION['board'] = newBoard();
    $_SESSION['winner'] = null;
    $_SESSION['gameOver'] = false;
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle langkah pemain
if (isset($_GET['row']) && isset($_GET['col'])) {
    $row = (int)$_GET['row'];
    $col = (int)$_GET['col'];
    
    // Cek apakah cell kosong dan permainan belum berakhir
    if ($_SESSION['board'][$row][$col] === '' && !$_SESSION['gameOver']) {
        // Langkah pemain X
        $_SESSION['board'][$row][$col] = 'X';
        
        // Cek kondisi menang setelah langkah pemain
        checkWinner();
        
        // Jika pemain belum menang dan permainan belum berakhir, komputer akan melangkah
        if (!$_SESSION['gameOver']) {
            computerMove();
            // Cek kondisi menang lagi setelah langkah komputer
            checkWinner();
        }
    }
    
    // Redirect untuk menghindari resubmission saat refresh
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Fungsi untuk langkah komputer (O)
function computerMove() {
    $emptyCells = [];
    
    // Cari semua sel yang masih kosong
    for ($i = 0; $i < 3; $i++) {
        for ($j = 0; $j < 3; $j++) {
            if ($_SESSION['board'][$i][$j] === '') {
                $emptyCells[] = [$i, $j];
            }
        }
    }
    
    // Jika masih ada sel kosong, komputer memilih secara acak
    if (count($emptyCells) > 0) {
        $randomIndex = array_rand($emptyCells);
        $move = $emptyCells[$randomIndex];
        $_SESSION['board'][$move[0]][$move[1]] = 'O';
    }
}

// Fungsi untuk memeriksa pemenang
function checkWinner() {
    $board = $_SESSION['board'];
    
    // Cek baris
    for ($i = 0; $i < 3; $i++) {
        if ($board[$i][0] !== '' && $board[$i][0] === $board[$i][1] && $board[$i][1] === $board[$i][2]) {
            $_SESSION['winner'] = $board[$i][0];
            $_SESSION['gameOver'] = true;
            return;
        }
    }
    
    // Cek kolom
    for ($j = 0; $j < 3; $j++) {
        if ($board[0][$j] !== '' && $board[0][$j] === $board[1][$j] && $board[1][$j] === $board[2][$j]) {
            $_SESSION['winner'] = $board[0][$j];
            $_SESSION['gameOver'] = true;
            return;
        }
    }
    
    // Cek diagonal kiri atas ke kanan bawah
    if ($board[0][0] !== '' && $board[0][0] === $board[1][1] && $board[1][1] === $board[2][2]) {
        $_SESSION['winner'] = $board[0][0];
        $_SESSION['gameOver'] = true;
        return;
    }
    
    // Cek diagonal kanan atas ke kiri bawah
    if ($board[0][2] !== '' && $board[0][2] === $board[1][1] && $board[1][1] === $board[2][0]) {
        $_SESSION['winner'] = $board[0][2];
        $_SESSION['gameOver'] = true;
        return;
    }
    
    // Cek seri (papan penuh)
    $boardFull = true;
    foreach ($board as $row) {
        foreach ($row as $cell) {
            if ($cell === '') {
                $boardFull = false;
                break 2;
            }
        }
    }
    
    if ($boardFull) {
        $_SESSION['gameOver'] = true;
        $_SESSION['winner'] = 'tie';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tic Tac Toe PHP</title>
    <style>
        html, body {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        table {
            border-collapse: collapse;
        }

        td {
            width: 60px;
            height: 60px;
            border: 1px solid rgba(0, 0, 0, 0.3);
            text-align:center;
            font-weight:bold;
            font-size:40px;
        }
        .green{
            background:green;
            padding:10px;
            color:#FFF;
            margin-bottom:10px;
            display: <?php echo ($_SESSION['winner'] ? 'block' : 'none'); ?>;
        }
        button{
            padding:5px;
            margin:5px 0;
            width: 100%;
        }
        td a {
            display: block;
            width: 100%;
            height: 100%;
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>

<div class="container">
    <!--the prompt will be hidden at initial status-->
    <div class="green">
        <b>
            <?php 
            if ($_SESSION['winner'] === 'X') {
                echo 'X Win!';
            } elseif ($_SESSION['winner'] === 'O') {
                echo 'O Win!';
            } elseif ($_SESSION['winner'] === 'tie') {
                echo 'It\'s a tie!';
            }
            ?>
        </b>
    </div>
    
    <table>
        <tbody>
        <?php for ($i = 0; $i < 3; $i++): ?>
            <tr>
                <?php for ($j = 0; $j < 3; $j++): ?>
                    <td>
                        <?php if ($_SESSION['board'][$i][$j] === ''): ?>
                            <?php if (!$_SESSION['gameOver']): ?>
                                <a href="?row=<?php echo $i; ?>&col=<?php echo $j; ?>"> </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php echo $_SESSION['board'][$i][$j]; ?>
                        <?php endif; ?>
                    </td>
                <?php endfor; ?>
            </tr>
        <?php endfor; ?>
        </tbody>
    </table>
    <a href="?reset=1"><button class="button">Reset</button></a>
</div>

</body>
</html>