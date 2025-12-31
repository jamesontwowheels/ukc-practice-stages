For the template:
- put variables in for game table
- move the commentary to the end for each CP action
- check the JS (also on cancel allow re-click)

For the next game:
- shutter the start?

//Tech Debt
- Point the main pages to a single place for images/css/etc
- Unified footer
- Shutter the starts
- Better leaderboard
- Independent course loads (!)
- Email reminder for passwords
- Next game
- That damn countdown



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