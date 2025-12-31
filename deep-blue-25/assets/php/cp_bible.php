<?PHP

     $cps_fish = [11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29];
     $cps_seals = [31,32,33];
     $cps_oxygen = [102,202,302];
     //special CPS;
     $cp_start_finish = [998,999];
     $cp_walrus = 34;
     $cp_snow_bank = 777;
    
    $all_cps = [11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,31,32,33,34,102,202,302,333,777,998,999];
    $above_cps = [31,32,33,34,102,202,302,777,998];
    $below_cps = [11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,102,202,302];
    $puzzle_cps = [31,32,33];
    $airholes = [102,202,302]; //need these separate so that I can alter their images/descriptions/options on surface/dive

    $cp_names = [
        ];

$starting_cps = [];

$cp_bible = [
    11 => [  
        "cp" => 11,
        "name" => "Eel 1",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    12 => [  
        "cp" => 12,
        "name" => "Eel 2",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    13 => [  
        "cp" => 13,
        "name" => "Eel 3",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    14 => [  
        "cp" => 14,
        "name" => "Eel 4",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    15 => [  
        "cp" => 15,
        "name" => "Eel 5",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    16 => [  
        "cp" => 16,
        "name" => "Cod 1",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    17 => [  
        "cp" => 17,
        "name" => "Cod 2",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    18 => [  
        "cp" => 18,
        "name" => "Cod 3",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    19 => [  
        "cp" => 19,
        "name" => "Cod 4",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    20 => [  
        "cp" => 20,
        "name" => "Cod 6",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    21 => [  
        "cp" => 21,
        "name" => "Cod 7",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    22 => [  
        "cp" => 22,
        "name" => "Cod 8",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    23 => [  
        "cp" => 23,
        "name" => "Tuna 1",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    24 => [  
        "cp" => 24,
        "name" => "Tuna 2",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    25 => [  
        "cp" => 25,
        "name" => "Tuna 3",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    26 => [  
        "cp" => 26,
        "name" => "Tuna 4",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    27 => [  
        "cp" => 27,
        "name" => "Tuna 5",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    28 => [  
        "cp" => 28,
        "name" => "Tuna 6",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    29 => [  
        "cp" => 29,
        "name" => "Tuna 7",
        "type" => "fish",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    31 => [  
        "cp" => 31,
        "name" => "Seal 1",
        "type" => "seal",
        "puzzle" => true,
        "puzzle_q" => "",
        "puzzle_a" => "",
        "message" => "Solve the puzzle to recruit the seal",
        "options" => [1 => "Solve"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    32 => [  
        "cp" => 32,
        "name" => "Seal 2",
        "type" => "seal",
        "puzzle" => true,
        "puzzle_q" => "",
        "puzzle_a" => "",
        "message" => "Solve the puzzle to recruit the seal",
        "options" => [1 => "Solve"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    33 => [  
        "cp" => 33,
        "name" => "Seal 3",
        "type" => "seal",
        "puzzle" => true,
        "puzzle_q" => "",
        "puzzle_a" => "",
        "message" => "Solve the puzzle to recruit the seal",
        "options" => [1 => "Solve"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    34 => [  
        "cp" => 34,
        "name" => "Walrus",
        "type" => "walrus",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    102 => [  
        "cp" => 102,
        "name" => "Air Hole",
        "type" => "hole",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Dive"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    202 => [  
        "cp" => 202,
        "name" => "Air Hole",
        "type" => "hole",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Dive"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    302 => [  
        "cp" => 302,
        "name" => "Airhole 3",
        "type" => "hole",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Dive"],
        "image" => [1,"wool.png"],
        "available" => false
    ],

    333 => [  
        "cp" => 333,
        "name" => "Seal 1",
        "type" => "hole",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],

    777 => [  
        "cp" => 777,
        "name" => "Snow bank",
        "type" => "bank",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
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
            "emoji" => "127937",
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