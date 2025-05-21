<?PHP
$cp_bible = [
    1 => [
        "cp" => 1,
        "name" => "H",
        "type" => "letter",
        "puzzle" => false,
        "message" => "",
        "options" => [
            1 => "Play letter",
            2 => "Use letter bonus",
            3 => "Use word bonus"
        ],
        "available" => false
    ],
    2 => [
        "cp" => 2,
        "name" => "E",
        "type" => "letter",
        "puzzle" => false,
        "message" => "",
        "options" => [
            1 => "Play letter",
            2 => "Use letter bonus",
            3 => "Use word bonus"
        ],
        "available" => false
    ],
    3 => [
            
        "cp" => 3,
        "name" => "L",
        "type" => "letter",
        "puzzle" => false,
        "message" => "",
        "options" => [
            1 => "Play letter",
            2 => "Use letter bonus",
            3 => "Use word bonus"
        ],
        "available" => false
    ], 
    4 => [
        
        "cp" => 4,
        "name" => "L",
        "type" => "letter",
        "puzzle" => false,
        "message" => "",
        "options" => [
            1 => "Play letter",
            2 => "Use letter bonus",
            3 => "Use word bonus"
        ],
        "available" => false
    ],
    5 => [
        
        "cp" => 5,
        "name" => "O",
        "type" => "letter",
        "puzzle" => false,
        "message" => "",
        "options" => [
            1 => "Play letter",
            2 => "Use letter bonus",
            3 => "Use word bonus"
        ],
        "available" => false
    ],
    6 => [
            
        "cp" => 6,
        "name" => "G",
        "type" => "letter",
        "puzzle" => false,
        "message" => "",
        "options" => [
            1 => "Play letter",
            2 => "Use letter bonus",
            3 => "Use word bonus"
        ],
        "available" => false
    ],
    7 => [
        
        "cp" => 7,
        "name" => "X",
        "type" => "letter",
        "puzzle" => false,
        "message" => "",
        "options" => [
            1 => "Play letter",
            2 => "Use letter bonus",
            3 => "Use word bonus"
        ],
        "available" => false
    ],
    11 => [
        
        "cp" => 11,
        "name" => "2x Letter",
        "type" => "puzzle point",
        "bonus" => ["type" => "letter", "value" => 2],
        "puzzle" => true,
        "puzzle_q" => "Well done reaching the top of the hill. What is 1 + 1?",
        "puzzle_a" => "2",
        "message" => "",
        "options" => [
            1 => "Solve Puzzle"
        ],
        "available" => false
    ],
    12 => [
        
        "cp" => 12,
        "name" => "3x Letter",
        "type" => "puzzle point",
        "bonus" => ["type" => "letter", "value" => 3],
        "puzzle" => true,
        "puzzle_q" => "Well done reaching the top of the hill. What is 1 + 1?",
        "puzzle_a" => "2",
        "message" => "",
        "options" => [
            1 => "Solve Puzzle"
        ],
        "available" => false
    ],
    13 => [
            
        "cp" => 13,
        "name" => "2x Word",
        "type" => "puzzle point",
        "bonus" => ["type" => "word", "value" => 2],
        "puzzle" => true,
        "puzzle_q" => "Well done reaching the top of the hill. What is 1 + 1?",
        "puzzle_a" => "2",
        "message" => "",
        "options" => [
            1 => "Solve Puzzle"
        ],
        "available" => false
    ], 
    14 => [
        
        "cp" => 14,
        "name" => "3x Word",
        "type" => "puzzle point",
        "bonus" => ["type" => "word", "value" => 3],
        "puzzle" => true,
        "puzzle_q" => "Well done reaching the top of the hill. What is 1 + 1?",
        "puzzle_a" => "2",
        "message" => "",
        "options" => [
            1 => "Solve Puzzle"
        ],
        "available" => false
    ],
        21 => [
            "cp" => 21,
            "name" => "Play word",
            "type" => "wsf",
            "puzzle" => false,
            "puzzle_q" => "",
            "puzzle_a" => "",
            "message" => "",
            "options" => [
                1 => "Play Word"
            ],
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
            "available" => false
        ]
         //etc
    ];