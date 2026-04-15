<?php
session_start();

if (isset($_GET['reset'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['board'])) {
    $_SESSION['board'] = array_fill(0, 9, '');
    $_SESSION['turn'] = 'X';
    $_SESSION['winner'] = null;
}

$winningCombos = [
    [0, 1, 2], [3, 4, 5], [6, 7, 8],
    [0, 3, 6], [1, 4, 7], [2, 5, 8],
    [0, 4, 8], [2, 4, 6]
];

function checkWinner($board) {
    global $winningCombos;
    foreach ($winningCombos as $combo) {
        if ($board[$combo[0]] !== '' &&
            $board[$combo[0]] === $board[$combo[1]] &&
            $board[$combo[1]] === $board[$combo[2]]) {
            return $board[$combo[0]];
        }
    }
    if (!in_array('', $board)) {
        return 'Tie';
    }
    return null;
}

if (isset($_GET['move']) && $_SESSION['winner'] === null) {
    $move = (int)$_GET['move'];
    if ($move >= 0 && $move <= 8 && $_SESSION['board'][$move] === '') {
        $_SESSION['board'][$move] = $_SESSION['turn'];
        $_SESSION['winner'] = checkWinner($_SESSION['board']);
        if ($_SESSION['winner'] === null) {
            $_SESSION['turn'] = ($_SESSION['turn'] === 'X') ? 'O' : 'X';
        }
    }
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Хрестики-нолики на PHP</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f9; margin: 0; }
        .game-container { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; }
        .board { display: grid; grid-template-columns: repeat(3, 100px); gap: 5px; margin: 20px auto; }
        .cell { width: 100px; height: 100px; display: flex; justify-content: center; align-items: center; font-size: 48px; text-decoration: none; color: #333; background-color: #eee; border-radius: 8px; transition: background-color 0.2s; font-weight: bold; }
        .cell:hover { background-color: #ddd; }
        .cell.x { color: #ff4757; }
        .cell.o { color: #2ed573; }
        .status { font-size: 24px; font-weight: bold; margin-bottom: 20px; color: #1a1a1a; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #1e90ff; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 10px; text-transform: uppercase; transition: background-color 0.2s; }
        .btn:hover { background-color: #0073e6; }
    </style>
</head>
<body>
    <div class="game-container">
        <div class="status">
            <?php
            if ($_SESSION['winner'] === 'Tie') {
                echo "Нічия!";
            } elseif ($_SESSION['winner']) {
                echo "Переможець: " . $_SESSION['winner'] . "!";
            } else {
                echo "Хід гравця: " . $_SESSION['turn'];
            }
            ?>
        </div>
        <div class="board">
            <?php for ($i = 0; $i < 9; $i++): ?>
                <?php
                $val = $_SESSION['board'][$i];
                $class = $val ? strtolower($val) : '';
                if ($val === '' && $_SESSION['winner'] === null) {
                    echo "<a href='?move=$i' class='cell'></a>";
                } else {
                    echo "<div class='cell $class'>$val</div>";
                }
                ?>
            <?php endfor; ?>
        </div>
        <a href="?reset=1" class="btn">Почати знову</a>
    </div>
</body>
</html>