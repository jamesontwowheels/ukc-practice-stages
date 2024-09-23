Next jobs:

- create a FAQ/instructions page
- explain why it's not loading
- get it to load immediately...
- get the timer to work...
- explain the scoring better
- more feedback for checking in
- tell people to remember the password, and email it to them
- stringtolower everything 

Must do before tomorrow:
- design map and upload checkpoints
- create the rule for double/triple letter
- check scoring/timings align to stage notes
- explain the scrabble scores

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