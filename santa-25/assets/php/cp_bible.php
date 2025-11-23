<?PHP

 $cps_resources = [1,2,3,4,5,6];
     $cps_elves = [11,12,13,14,15,16];
     $cps_kids = [21,22,23,24,25,26,27];
     $cps_santas = [101,102];

     //special CPS;
     $cp_workshop = 51;
     $cp_start_finish = [998,999];
     $cp_ = 34;
    
    $all_cps = [1,2,3,4,5,6,11,12,13,14,15,16,21,22,23,24,25,26,51,101,102,998,999];
    $outside_cps = [1,2,3,4,5,6,21,22,23,24,25,26,51,102,998];
    $inside_cps = [11,12,13,14,15,16,51,101,102,998];
    
    $santa_info = [
        101 => "The children are located as follows:  CH1 = Olivia, CH2 = Noah, CH3 = Amelia, CH4 = George, CH5 = Isla, CH6 = Leo",
        102 => "Mrs Claus says thanks for selling her your stuff!"//"The Resources are located as follows: R1 = Wool, R2 = Wood, R3 = Plastic, R4 = Carbon, R5 = Metal, R6 = Lithium"
    ];

    $cp_names = [
        1 => "Wool",
        2 => "Wood",
        3 => "Plastic",
        4 => "Carbon",
        5 => "Metal",
        6 => "Lithium",
        11 => "Jumper",
        12 => "Tree House",
        13 => "Bike",
        14 => "Playstation",
        15 => "RC Car",
        16 => "Laptop",
        21 => "Ada",
        22 => "Ben",
        23 => "Cat",
        24 => "Dom",
        25 => "Isla",
        26 => "Leo",
        51 => "W/shop door",
        //101 => "Mr Claus",
        102 => "Mrs Claus",
        998 => "Finish",
        999 => "Start"
        ];

$starting_cps = [];

$cp_bible = [
    1 => [  
        "cp" => 1,
        "name" => "Wool",
        "type" => "resource",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wool.png"],
        "available" => false
    ],
    2 => [  
        "cp" => 2,
        "name" => "Wood",
        "type" => "resource",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"wood.png"],
        "available" => false
    ],
    3 => [  
        "cp" => 3,
        "name" => "Plastic",
        "type" => "resource",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"plastic.png"],
        "available" => false
    ],
    4 => [  
        "cp" => 4,
        "name" => "Carbon",
        "type" => "resource",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"carbon.png"],
        "available" => false
    ],
    5 => [  
        "cp" => 5,
        "name" => "Metal",
        "type" => "resource",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"metal.png"],
        "available" => false
    ],
    6 => [  
        "cp" => 6,
        "name" => "Lithium",
        "type" => "resource",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Collect"],
        "image" => [1,"lithium.png"],
        "available" => false
    ],
    11 => [  
        "cp" => 11,
        "name" => "Jumper",
        "type" => "toy",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Build"],
        "image" => [1,"a_11.png"],
        "available" => false
    ],
    12 => [  
        "cp" => 12,
        "name" => "Tree House",
        "type" => "toy",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Build"],
        "image" => [1,"a_12.png"],
        "available" => false
    ],
    13 => [  
        "cp" => 13,
        "name" => "Bike",
        "type" => "toy",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Build"],
        "image" => [1,"a_13.png"],
        "available" => false
    ],
    14 => [  
        "cp" => 14,
        "name" => "RC Car",
        "type" => "toy",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Build"],
        "image" => [1,"a_14.png"],
        "available" => false
    ],
    15 => [  
        "cp" => 15,
        "name" => "Switch",
        "type" => "toy",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Build"],
        "image" => [1,"a_15.png"],
        "available" => false
    ],
    16 => [  
        "cp" => 16,
        "name" => "Laptop",
        "type" => "toy",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Build"],
        "image" => [1,"a_16.png"],
        "available" => false
    ],
    21 => [  
        "cp" => 21,
        "name" => "Ada",
        "type" => "child",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Deliver"],
        "image" => [1,"ada.png"],
        "available" => false
    ],
    22 => [  
        "cp" => 22,
        "name" => "Ben",
        "type" => "child",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Deliver"],
        "image" => [1,"ben.png"],
        "available" => false
    ],
    23 => [  
        "cp" => 23,
        "name" => "Cat",
        "type" => "child",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Deliver"],
        "image" => [1,"cat.png"],
        "available" => false
    ],
    24 => [  
        "cp" => 24,
        "name" => "Dom",
        "type" => "child",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Deliver"],
        "image" => [1,"dom.png"],
        "available" => false
    ],
    25 => [  
        "cp" => 25,
        "name" => "Isla",
        "type" => "child",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Deliver"],
        "image" => [1,"isla.png"],
        "available" => false
    ],
    26 => [  
        "cp" => 26,
        "name" => "Leo",
        "type" => "child",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Deliver"],
        "image" => [1,"leo.png"],
        "available" => false
    ],
    51 => [  
        "cp" => 51,
        "name" => "Portal",
        "type" => "portal",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Leave workshop"],
        "image" => [1,"portal_1.png"],
        "available" => false,
        "animation" => [true,"portal"]  
    ],
    102 => [  
        "cp" => 102,
        "name" => "Mrs Claus",
        "type" => "Mrs Claus",
        "puzzle" => false,
        "message" => "",
        "options" => [1 => "Sell Everything"],
        "image" => [1,"mrs_c.png"],
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