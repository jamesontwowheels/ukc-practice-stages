For the template:
- put variables in for game table
- move the commentary to the end for each CP action
- check the JS (also on cancel allow re-click)

For the next game:
- shutter the start?

//Tech Debt
- Point the main pages to a single place for images/css/etc
- Unified footer
- Teams or Solo option.
- Better leaderboard
- That damn countdown
- Email reminder for passwords
- Segment the code

//Features:

- Shutter the starts >>         requires an admin mode/portal
- Independent course loads (!)
- Next game mechanics >>        requires the next game
- Multi-functional buttons

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