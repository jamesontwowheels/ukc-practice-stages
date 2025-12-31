<?PHP
$team_params = [
    "seal_state" => [
        31 => [
            "active" => 0,
            "started" => 0,
            "collected" => 0],
        32 => [
            "active" => 0,
            "started" => 0,
            "collected" => 0],
        33 => [
            "active" => 0,
            "started" => 0,
            "collected" => 0],
        ],
    "fish_level" => 0,
    "fish_caught" => []
    ];

$player_params = [
    "fish_level" => 1,
    "fish_held" => [],
    "oxygen" => [
        "active" => 0,
        "end" => 0
        ]
];

$player_inventory = [
    "Fish (kg)" => 0
];

$private_inventory = [
    "resources" => [],
    "presents" => []
];

$fish_weights = [
    0 => [
        "Eel" => 1,
        "Cod" => 2,
        "Ray" => 3
    ],
    1 => [
        "Eel" => 2,
        "Cod" => 4,
        "Ray" => 6
    ],
    2 => [
        "Eel" => 3,
        "Cod" => 6,
        "Ray" => 9
    ],
    ];

$lesson_cost = 
[0 => 10,
1 => 20,
2 => 30];

$game_params = [
    "stage_time" => 800*60,
    "level_cps" => [
        0 => [1,2,3,4,5,6,21,22,23,24,25,26,51,101],
        1 => [11,12,13,14,15,16,51,102,998]
    ],
    "oxygen_time" => 210

    ];


