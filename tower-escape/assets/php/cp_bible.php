<?PHP
$cp_bible = [
    11 => [  
        "cp" => 11,
        "name" => "Lobby",
        "type" => "info",
        "puzzle" => false,
        "message" => "The entrance hall, above the door reads an inscription, 'Welcome to MINDGAMES Tower - est. <b>1518</b>'",
        "options" => [],
        "image" => [0,0],
        "available" => false,
        "level" => 1,
    ],
    12 => [  
        "cp" => 12,
        "name" => "Lounge",
        "type" => "info",
        "puzzle" => false,
        "message" => "A softly decorated room with a cat sleeping on a chair, taking a closer look you see the cat is wearing a collar reading 'Mittens - born <b>2018</b>'",
        "options" => [],
        "image" => [0,0],
        "available" => false,
        "level" => 1,
    ],
    13 => [  
        "cp" => 13,
        "name" => "Office",
        "type" => "info",
        "puzzle" => false,
        "message" => "A small office, on the desk is an American format calendar, with the date <b>January 16th</b> highlighted.",
        "options" => [],
        "image" => [0,0],
        "available" => false,
        "level" => 1,
    ],
    14 => [  
        "cp" => 14,
        "name" => "Kitchen",
        "type" => "info",
        "puzzle" => false,
        "message" => "A kitchen, there is a broken clock on the wall showing the time <b>quarter past four</b> in the morning.",
        "options" => [],
        "image" => [0,0],
        "available" => false,
        "level" => 1,
    ],
    15 => [  
        "cp" => 15,
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
    21 => [
        
        "cp" => 21,
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
    22 => [
        
        "cp" => 22,
        "name" => "Table",
        "type" => "pair",
        "pair" => 2,
        "puzzle" => false,
        "message" => "A big button on the wall with the word <b>'Table'</b> on it",
        "options" => [
            1 => "Press the button"
        ],
        "image" => [0,0],
        "available" => false
    ],
    23 => [
        
        "cp" => 23,
        "name" => "Top",
        "type" => "pair",
        "pair" => 3,
        "puzzle" => false,
        "message" => "A big button on the wall with the word <b>'Top'</b> on it",
        "options" => [
            1 => "Press the button"
        ],
        "image" => [0,0],
        "available" => false
    ],
    24 => [
        
        "cp" => 24,
        "name" => "Book",
        "type" => "pair",
        "pair" => 1,
        "puzzle" => false,
        "message" => "A big button on the wall with the word <b>'Book'</b> on it",
        "options" => [
            1 => "Press the button"
        ],
        "image" => [0,0],
        "available" => false
    ],
    25 => [
        
        "cp" => 25,
        "name" => "Lap",
        "type" => "pair",
        "pair" => 3,
        "puzzle" => false,
        "message" => "A big button on the wall with the word <b>'Lap'</b> on it",
        "options" => [
            1 => "Press the button"
        ],
        "image" => [0,0],
        "available" => false
    ],
    26 => [
        
        "cp" => 26,
        "name" => "Turn",
        "type" => "pair",
        "pair" => 2,
        "puzzle" => false,
        "message" => "A big button on the wall with the word <b>'Turn'</b> on it",
        "options" => [
            1 => "Press the button"
        ],
        "image" => [0,0],
        "available" => false
    ],
    27 => [  
        "cp" => 27,
        "name" => "Stairs",
        "type" => "ladder",
        "puzzle" => false,
        "message" => "The door to the staircase is locked, next to the door there are three lights, each light has two wires that lead off to different rooms. The instructions read 'Spell the correct words to activate the lights. You cannot press the same button twice in a row'",
        "options" => [101 => "Buy Solution (10 Gold)"],
        "image" => [0,0],
        "available" => false,
        "level" => 1,
    ],
    31 => [
            
        "cp" => 31,
        "name" => "Room",
        "type" => "quest",
        "puzzle" => false,
        "message" => "You enter the room and immediately a huge dogs pins you against the door, behind it is a pile of gold... if only you could reach it",
        "options" => [
            1 => "grab the gold"
        ],
        "image" => [0,0],
        "available" => false
    ], 
    41 => [
        
        "cp" => 41,
        "name" => "Room 4.1",
        "type" => "nim",
        "puzzle" => false,
        "message" => "An empty room with a hole in the ceiling to the next level, there's a rope-ladder tangled in the hole, but it's too high reach and there's nothing to climb on",
        "options" => [ 1 => "Pick block up",2 => "Put block down"],
        "image" => [0,0],
        "available" => false
    ],
    42 => [
        
        "cp" => 42,
        "name" => "Room 4.2",
        "type" => "nim",
        "puzzle" => false,
        "message" => "There are a pile of five blocks stacked in size order, you can climb up them and touch the ceiling, they don't look strong though, you can definitely only carry one at a time and it wouldn't be safe to put a larger one down on top of a smaller one...",
        "options" => [ 1 => "Pick block up",2 => "Put block down"],
        "image" => [1,"nim_54321.png"],
        "available" => false
    ],
    43 => [
        
        "cp" => 43,
        "name" => "Room 4.3",
        "type" => "nim",
        "puzzle" => false,
        "message" => "Aside from a bookcase the room is empty, it looks like a good place to put a block down. Actually, is there something catching the light on top of the bookcase...?",
        "options" => [ 1 => "Pick block up",2 => "Put block down"],
        "image" => [0,0],
        "available" => false
    ],
    51 => [
        
        "cp" => 51,
        "name" => "Princess",
        "type" => "quest",
        "puzzle" => false,
        "message" => "'What are you doing here? I said I didn't want to see anyone until my cat was returned to me, didn't you see the poster?'",
        "options" => [1 => "offer to help"],
        "image" => [1,"mittens.png"],
        "available" => false
    ],
    52 => [
            
        "cp" => 52,
        "name" => "Jester",
        "type" => "quest",
        "15-puzzle" => true,
        "puzzle" => false,
        "message" => "I've mixed up this portrait - 25 gold coins says you can't fix it",
        "options" => [
            1 => "Claim Gold"
        ],
        "image" => [0,0],
        "available" => false
    ],
    53 => [
            
        "cp" => 53,
        "name" => "Chef",
        "type" => "quest",
        "puzzle" => false,
        "message" => '"I made too much food again, do you want some sausages?"',
        "options" => [
            1 => "Pick-up sausages"
        ],
        "image" => [0,0],
        "available" => false
    ],
    54 => [
            
        "cp" => 54,
        "name" => "Dragon",
        "blink-game" => true,
        "type" => "quest",
        "puzzle" => false,
        "message" => "A large dragon rests her hand on 25 gold pieces, every ten seconds she blinks, but only for 10ms",
        "options" => [
            1 => "Snatch gold"
        ],
        "image" => [0,0],
        "available" => false
    ],
    55 => [
        
        "cp" => 55,
        "name" => "King",
        "type" => "puzzle point",
        "puzzle" => true,
        "puzzle_q" => $puzzle_bible[5][0][0],
        "puzzle_a" => $puzzle_bible[5][0][1],
        "message" => "I'm stuck on this puzzle, help me out and I'll give you 20 gold coins",
        "options" => [
            1 => "solve"
        ],
        "image" => [0,0],
        "available" => false
    ],
    56 => [
        
        "cp" => 56,
        "name" => "Queen",
        "type" => "puzzle point",
        "puzzle" => true,
        "puzzle_q" => $puzzle_bible[5][1][0],
        "puzzle_a" => $puzzle_bible[5][1][1],
        "message" => "I'm stuck on this puzzle, help me out and I'll give you 20 gold coins",
        "options" => [
            1 => "solve"
        ],
        "image" => [0,0],
        "available" => false
    ],
    57 => [
        
        "cp" => 57,
        "name" => "Up",
        "type" => "ladder",
        "puzzle" => false,
        "message" => "Finally, daylight above, you can see the roof, and rescue",
        "options" => [
            6 => "Go up"
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