Next jobs:
- only show checkpoints that are available - test they activate and deactivate
- start/end a game - are you sure?
    - Are you sure?
    - prevent checkpoints while game isn't live
    - 
- include a timer - add some style and class

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