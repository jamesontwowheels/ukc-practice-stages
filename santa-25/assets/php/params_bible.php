<?PHP
$team_params = [
    "resource_picked" => [
        1 => 0,
        2 => 0,
        3 => 0,
        4 => 0,
        5 => 0,
        6 => 0
    ],
    "wishlists" => [
        21 => ["Jumper","Tree House","Bike"],
        22 => ["RC Car","Switch","Laptop"],
        23 => ["Switch","Jumper","Tree House"],
        24 => ["Bike","RC Car","Laptop"],
        25 => ["Tree House","RC Car","Jumper"],
        26 => ["Switch","Bike","Laptop"]
    ],
    "gift_states" => [
        11 => 0,
        12 => 0,
        13 => 0,
        14 => 0,
        15 => 0,
        16 => 0
    ],
    "gift_count" => [
        11 => 0,
        12 => 0,
        13 => 0,
        14 => 0,
        15 => 0,
        16 => 0,
        "Jumper" => 0,
        "Bike" => 0,
        "Tree House" => 0,
        "Switch" => 0,
        "RC Car" => 0,
        "Laptop" => 0,
    ],
    "build_states" => [
        11 => [0,[],0],
        12 => [0,[],0],        
        13 => [0,[],0],
        14 => [0,[],0],
        15 => [0,[],0],
        16 => [0,[],0]
    ],
    "gifted" => []
    ];

$player_params = [
    "map_level" => 1
];

$player_inventory = [
    "resources" => [],
    "presents" => []
];

$private_inventory = [
    "resources" => [],
    "presents" => []
];

$game_params = [
    "stage_time" => 75*60,
    "level_cps" => [
        0 => [1,2,3,4,5,6,21,22,23,24,25,26,51,101],
        1 => [11,12,13,14,15,16,51,102,998]
    ],
    "outside_cps" => [],
    "gift_times" => [
        11 => 120,
        12 => 30,
        13 => 240,
        14 => 240,
        15 => 360,
        16 => 480],
    "gift_recipes" => [
        11 => ["Wool","Wool","Wool"],
        12 => ["Wood","Wood","Metal","Wood"],
        13 => ["Carbon","Carbon","Metal","Plastic"],
        14 => ["Plastic","Metal","Plastic","Lithium"],
        15 => ["Plastic","Carbon","Metal","Lithium"],
        16 => ["Metal","Plastic","Lithium","Lithium"]
    ],
    "gift_score" => [
        "Jumper" => 4,
        "Tree House" => 6,
        "Bike" => 8,
        "RC Car" => 12,
        "Switch" => 14,
        "Laptop" => 16
    ],
    "resource_start" => [
        1 => 3,
        2 => 3,
        3 => 1,
        4 => 1,
        5 => 6,
        6 => 6
    ],
    "resource_refresh" => [
        1 => 300,
        2 => 300,
        3 => 600,
        4 => 600,
        5 => 900,
        6 => 900
    ],
    "resource_refresh_vol" => [
        "1" => 1,
        "2" => 1,
        "3" => 4,
        "4" => 2,
        "5" => 2,
        "6" => 2
    ],

    ];


