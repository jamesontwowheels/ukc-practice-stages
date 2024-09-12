<?php
// Create an array representing the letter distribution in Junior Scrabble
$letters = array_merge(
    array_fill(0, 8, 'A'),
    array_fill(0, 2, 'B'),
    array_fill(0, 2, 'C'),
    array_fill(0, 4, 'D'),
    array_fill(0, 10, 'E'),
    array_fill(0, 2, 'F'),
    array_fill(0, 3, 'G'),
    array_fill(0, 2, 'H'),
    array_fill(0, 6, 'I'),
    array_fill(0, 1, 'J'),
    array_fill(0, 1, 'K'),
    array_fill(0, 4, 'L'),
    array_fill(0, 2, 'M'),
    array_fill(0, 6, 'N'),
    array_fill(0, 8, 'O'),
    array_fill(0, 2, 'P'),
    array_fill(0, 1, 'Q'),
    array_fill(0, 6, 'R'),
    array_fill(0, 4, 'S'),
    array_fill(0, 6, 'T'),
    array_fill(0, 4, 'U'),
    array_fill(0, 2, 'V'),
    array_fill(0, 2, 'W'),
    array_fill(0, 1, 'X'),
    array_fill(0, 2, 'Y'),
    array_fill(0, 1, 'Z')
);

// Shuffle the letters array
shuffle($letters);

// Convert the array into a PHP string representation
$lettersArrayString = var_export($letters, true);

// Create the PHP content to store the array as `$game_letters`
$phpCode = "<?php\n\$game_letters = $lettersArrayString;\n";

// Save the content to a file called `game_letters.php`
file_put_contents('game_letters.php', $phpCode);

echo "The letters have been saved to 'game_letters.php'.\n";