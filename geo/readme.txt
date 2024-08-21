Next jobs:
<<<<<<< HEAD
- only show checkpoints that are available
- show details on page load
- start/end a game
- include a timer
- allow multiple players
- add commentary/score
=======
- show checkpoints as values not numbers
- only show checkpoints that are available
- insert accurate timings
- game start function
- select/allocate player option
>>>>>>> 0144168110a3bfcb0a130967e7a93d2e18e90c4a

Structure of app:

index.html
- targets.js = object containing the checkpoint locations
- distance.js = check on targets - within range?
- rows.js = putting of targets into the main body
- test.js = on button click - ajax call to test.php
    - test.php
        - script.php = database insert
        - scrabble.php = game rules
- main.css