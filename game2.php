<?php
session_start();

$boardSize = 15;

// Initialize or reset the board based on user action
if (!isset($_SESSION['board']) || isset($_POST['reset'])) {
    $_SESSION['board'] = array_fill(0, $boardSize, array_fill(0, $boardSize, null));
    $_SESSION['lastStone'] = 'white';  // This will make black the first stone to be placed.
}
// Initalize the player scores
if (!isset($_SESSION['scores']) || isset($_POST['reset'])) {
    $_SESSION['scores'] = ['black' => 0, 'white' => 0];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reset'])) {
        $_SESSION['board'] = array_fill(0, $boardSize, array_fill(0, $boardSize, null));
        $_SESSION['lastStone'] = 'white';  // This will make black the first stone to be placed.
    } elseif (isset($_POST['row'], $_POST['col']) && is_numeric($_POST['row']) && is_numeric($_POST['col'])) {
        $row = (int) $_POST['row'];
        $col = (int) $_POST['col'];
		
		//echo "Placed stone at ($row, $col)<br>";

	if (!isset($_SESSION['board'][$row][$col])) {
    $newStone = ($_SESSION['lastStone'] === 'white') ? 'black' : 'white';
    $_SESSION['board'][$row][$col] = $newStone;

    // Check if the stone placed itself is captured
    $visited = [];
    if (!check_liberties($_SESSION['board'], $row, $col, $visited)) {
        $_SESSION['board'] = remove_group($_SESSION['board'], $row, $col, $newStone);
    }

    // Check adjacent positions for captures
    foreach (get_adjacent_positions($row, $col) as $position) {
        list($adj_x, $adj_y) = $position;
        if ($adj_x >= 0 && $adj_x < 15 && $adj_y >= 0 && $adj_y < 15) {
            $adjacent_stone = $_SESSION['board'][$adj_x][$adj_y];
            if ($adjacent_stone && $adjacent_stone !== $newStone) {
                $visited = [];
                if (!check_liberties($_SESSION['board'], $adj_x, $adj_y, $visited)) {
                    $_SESSION['board'] = remove_group($_SESSION['board'], $adj_x, $adj_y, $adjacent_stone);
                }
            }
        }
    }
    
    $_SESSION['lastStone'] = $newStone;
}
    }
}



function get_adjacent_positions($x, $y) {
    return [
        [$x - 1, $y],  // top
        [$x + 1, $y],  // bottom
        [$x, $y - 1],  // left
        [$x, $y + 1]   // right
    ];
}

function check_liberties($board, $x, $y, &$visited) {
	//echo "Checking liberties for ($x, $y)<br>";
    if ($x < 0 || $x >= 15 || $y < 0 || $y >= 15 || isset($visited["$x,$y"])) {
        return false;
    }

	 //echo "Checking cell ($x, $y)<br>"; // Add this line for debugging

    if (!$board[$x][$y]) {
        return true;
    }

    $visited["$x,$y"] = true;
    $stone = $board[$x][$y];
    foreach (get_adjacent_positions($x, $y) as $position) {
        list($adj_x, $adj_y) = $position;
        if ($adj_x < 0 || $adj_x >= 15 || $adj_y < 0 || $adj_y >= 15) {
			//echo "Skipping out of bounds cell ($adj_x, $adj_y)<br>"; // Add this line for debugging
            continue;
        }
        if (!$board[$adj_x][$adj_y]) {
			//echo "Liberty found at ($adj_x, $adj_y)<br>"; // Add this line for debugging
            return true;
        }
        if ($board[$adj_x][$adj_y] == $stone && !isset($visited["$adj_x,$adj_y"]) && check_liberties($board, $adj_x, $adj_y, $visited)) {
			//echo "Connected stone with liberty at ($adj_x, $adj_y)<br>"; // Add this line for debugging
            return true;
        }
    }
    return false;
}
function remove_group($board, $x, $y, $stone) {
    $capturingStone = ($stone == 'black') ? 'white' : 'black';
    $board[$x][$y] = null;
    $_SESSION['scores'][$capturingStone] += 1;  // update score here
    foreach (get_adjacent_positions($x, $y) as $position) {
        list($adj_x, $adj_y) = $position;
        if ($adj_x >= 0 && $adj_x < 15 && $adj_y >= 0 && $adj_y < 15 && $board[$adj_x][$adj_y] == $stone) {
            $board = remove_group($board, $adj_x, $adj_y, $stone);
        }
    }
    return $board;
}


$board = $_SESSION['board'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>15x15 Go Board</title>
    <style>
        table {
            border-collapse: collapse;
            width: 600px;
            height: 600px;
            margin: 0 auto;
			background-color: #ecc260;
        }

        td {
            border: 1px solid black;
            width: 40px;
            height: 40px;
            text-align: center;
            vertical-align: middle;
        }

        .stone {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-block;
        }

        .black {
            background-color: black;
        }

        .white {
            background-color: white;
        }
		
    </style>
</head>
<body>
	<div style="text-align: center; margin-top: 20px;">
    Black Score: <?php echo $_SESSION['scores']['black']; ?> <br>
    White Score: <?php echo $_SESSION['scores']['white']; ?> <br>
    <button type="submit" name="reset" value="1">Reset Board</button>
</div>
    <form action="" method="POST">
        <table>
            <?php
            for ($i = 0; $i < $boardSize; $i++) {
                echo "<tr>";
                for ($j = 0; $j < $boardSize; $j++) {
                    echo "<td onclick=\"setStone(this, $i, $j)\">";
                    if ($board[$i][$j]) {
                        echo "<div class='stone " . $board[$i][$j] . "'></div>";
                    }
                    echo "</td>";
                }
                echo "</tr>";
            }
            ?>
        </table>
        <input type="hidden" name="row" id="row">
        <input type="hidden" name="col" id="col">
        <input type="hidden" name="stone" id="stone">
		<!-- Add Reset button below the board -->
		<div style="text-align: center; margin-top: 20px;">
			<button type="submit" name="reset" value="1">Reset Board</button>
		</div>
    </form>
    <script>
        function setStone(td, row, col) {
			document.getElementById('row').value = row;
			document.getElementById('col').value = col;
    
			const stoneDiv = td.querySelector('.stone');
			if (stoneDiv && stoneDiv.classList.contains('white')) {
				document.getElementById('stone').value = 'black';
			} else {
				document.getElementById('stone').value = 'white';
			}
			
			document.forms[0].submit();
		}

    </script>
</body>
</html>
