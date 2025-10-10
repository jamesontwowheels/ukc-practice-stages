<?PHP

$starting_cps = [];

$cp_bible = [
    1 => [  
        "cp" => 1,
        "name" => "Lobby",
        "type" => "info",
        "puzzle" => false,
        "message" => "The entrance hall, above the door reads an inscription, 'Welcome to MINDGAMES Tower - est. <b>1518</b>'",
        "options" => [],
        "image" => [0,0],
        "available" => false,
        "level" => 1,
    ],
    2 => [  
        "cp" => 2,
        "name" => "Lounge",
        "type" => "info",
        "puzzle" => false,
        "message" => "A softly decorated room with a cat sleeping on a chair, taking a closer look you see the cat is wearing a collar reading 'Mittens - born <b>2018</b>'",
        "options" => [],
        "image" => [0,0],
        "available" => false,
        "level" => 1,
    ],
    3 => [  
        "cp" => 3,
        "name" => "Office",
        "type" => "info",
        "puzzle" => false,
        "message" => "A small office, on the desk is an American format calendar, with the date <b>January 16th</b> highlighted.",
        "options" => [],
        "image" => [0,0],
        "available" => false,
        "level" => 1,
    ],
    4 => [  
        "cp" => 4,
        "name" => "Kitchen",
        "type" => "info",
        "puzzle" => false,
        "message" => "A kitchen, there is a broken clock on the wall showing the time <b>quarter past four</b> in the morning.",
        "options" => [],
        "image" => [0,0],
        "available" => false,
        "level" => 1,
    ],
    5 => [  
        "cp" => 5,
        "name" => "Up",
        "type" => "trapdoor",
        "puzzle" => True,
        "puzzle_q" => $puzzle_bible[1][0][0],
        "puzzle_a" => $puzzle_bible[1][0][1],
        "message" => "A small room, there is a hole in the ceiling and a ladder locked to the wall with a combination padlock, the padlock is set to the code 'MINDGAME'",
        "options" => [1 => "enter code", 101 => "Buy hint (5 gold)"],
        "image" => [0,0],
        "available" => false,
        "level" => 1,
    ],
    6 => [
        
        "cp" => 6,
        "name" => "Note",
        "type" => "pair",
        "pair" => 1,
        "puzzle" => false,
        "message" => "A big button on the wall with the word <b>'NOTE'</b> on it",
        "options" => [
            1 => "Press the button"
        ],
        "image" => [0,0],
        "available" => false
    ],
        999 => [
            
            "cp" => 999,
            "name" => "Start",
            "type" => "start_finish",
            "score" => [1,2,3],
            "puzzle" => false,
            "puzzle_q" => "x",
            "puzzle_a" => "x",
            "message" => "Start the game for your team",
            "options" => [
                1 => "Go!"
            ],
        "image" => [0,0],
            "available" => true
        ],
        998 => [
            
            "cp" => 998,
            "name" => "Finish",
            "type" => "start_finish",
            "score" => [1,2,3],
            "puzzle" => false,
            "puzzle_q" => "x",
            "puzzle_a" => "x",
            "message" => "Click here to end your game",
            "options" => [
                1 => "Finish"
            ],
        "image" => [0,0],
            "available" => false
        ]
         //etc
    ];