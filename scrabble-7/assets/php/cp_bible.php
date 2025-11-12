<?PHP


$cp_bible_kids = [
    1 => [
        "cp" => 1,
        "name" => '1 - <div class="scrabble-tile letter-tile">
                    <span class="letter">D</span>
                    <span class="points">2</span>
                    </div>',
        "value" => "D",
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
        "name" => '2 - <div class="scrabble-tile letter-tile">
                    <span class="letter">I</span>
                    <span class="points">1</span>
                    </div>',
        "value" => "I",
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
        "name" => '3 - <div class="scrabble-tile letter-tile">
                    <span class="letter">B</span>
                    <span class="points">3</span>
                    </div>',
        "value" => "B",
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
        "name" => '4 - <div class="scrabble-tile letter-tile">
                    <span class="letter">E</span>
                    <span class="points">1</span>
                    </div>',
        "value" => "E",
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
        "name" => '5 - <div class="scrabble-tile letter-tile">
                    <span class="letter">F</span>
                    <span class="points">4</span>
                    </div>',
        "value" => "F",
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
        "name" => '6 - <div class="scrabble-tile letter-tile">
                    <span class="letter">R</span>
                    <span class="points">1</span>
                    </div>',
        "value" => "R",
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
        "name" => '7 - <div class="scrabble-tile letter-tile">
                    <span class="letter">L</span>
                    <span class="points">1</span>
                    </div>',
        "value" => "L",
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
        "name" => '<div class="scrabble-tile bonus dl">DL</div>',
        "type" => "puzzle point",
        "bonus" => ["type" => "letter", "value" => 2],
        "puzzle" => true,
        "puzzle_q" => "What is 1 + 2 + 3",
        "puzzle_a" => "6",
        "message" => "",
        "options" => [
            1 => "Solve Puzzle"
        ],
        "available" => false
    ],
    12 => [
        
        "cp" => 12,
        "name" => '<div class="scrabble-tile bonus tl">TL</div>',
        "type" => "puzzle point",
        "bonus" => ["type" => "letter", "value" => 3],
        "puzzle" => true,
        "puzzle_q" => "what is the fourth letter of the alphabet",
        "puzzle_a" => "d",
        "message" => "",
        "options" => [
            1 => "Solve Puzzle"
        ],
        "available" => false
    ],
    13 => [
            
        "cp" => 13,
        "name" => '<div class="scrabble-tile bonus dw">DW</div>',
        "type" => "puzzle point",
        "bonus" => ["type" => "word", "value" => 2],
        "puzzle" => true,
        "puzzle_q" => "what is 7 + 6 - 2",
        "puzzle_a" => "11",
        "message" => "",
        "options" => [
            1 => "Solve Puzzle"
        ],
        "available" => false
    ], 
    14 => [
        
        "cp" => 14,
        "name" => '<div class="scrabble-tile bonus tw">TW</div>',
        "type" => "puzzle point",
        "bonus" => ["type" => "word", "value" => 3],
        "puzzle" => true,
        "puzzle_q" => "What is the seventh letter of the alphabet",
        "puzzle_a" => "g",
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

$cp_bible = [
    1 => [
        "cp" => 1,
        "name" => '1 - <div class="scrabble-tile letter-tile">
                    <span class="letter">D</span>
                    <span class="points">2</span>
                    </div>',
        "value" => "R",
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
        "name" => '2 - <div class="scrabble-tile letter-tile">
                    <span class="letter">I</span>
                    <span class="points">1</span>
                    </div>',
        "value" => "A",
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
        "name" => '3 - <div class="scrabble-tile letter-tile">
                    <span class="letter">B</span>
                    <span class="points">3</span>
                    </div>',
        "value" => "X",
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
        "name" => '4 - <div class="scrabble-tile letter-tile">
                    <span class="letter">E</span>
                    <span class="points">1</span>
                    </div>',
        "value" => "M",
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
        "name" => '5 - <div class="scrabble-tile letter-tile">
                    <span class="letter">F</span>
                    <span class="points">4</span>
                    </div>',
        "value" => "D",
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
        "name" => '6 - <div class="scrabble-tile letter-tile">
                    <span class="letter">R</span>
                    <span class="points">1</span>
                    </div>',
        "value" => "A",
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
        "name" => '7 - <div class="scrabble-tile letter-tile">
                    <span class="letter">L</span>
                    <span class="points">1</span>
                    </div>',
        "value" => "E",
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
        "name" => '<div class="scrabble-tile bonus dl">DL</div>',
        "type" => "puzzle point",
        "bonus" => ["type" => "letter", "value" => 2],
        "puzzle" => true,
        "puzzle_q" => "Find the solution to the equation.<br><br><img class='puzzle_pic' src='assets/img/puzzle-1.png'>",
        "puzzle_a" => "55",
        "message" => "",
        "options" => [
            1 => "Solve Puzzle"
        ],
        "available" => false
    ],
    12 => [
        
        "cp" => 12,
        "name" => '<div class="scrabble-tile bonus tl">TL</div>',
        "type" => "puzzle point",
        "bonus" => ["type" => "letter", "value" => 3],
        "puzzle" => true,
        "puzzle_q" => "Fill in the blank.<br><br><img class='puzzle_pic' src='assets/img/puzzle-2.png'>",
        "puzzle_a" => "cdo",
        "message" => "",
        "options" => [
            1 => "Solve Puzzle"
        ],
        "available" => false
    ],
    13 => [
            
        "cp" => 13,
        "name" => '<div class="scrabble-tile bonus dw">DW</div>',
        "type" => "puzzle point",
        "bonus" => ["type" => "word", "value" => 2],
        "puzzle" => true,
        "puzzle_q" => "What is the sum of the next number in the sequence?<br><br><img class='puzzle_pic' src='assets/img/puzzle-5.png'>",
        "puzzle_a" => "16",
        "message" => "",
        "options" => [
            1 => "Solve Puzzle"
        ],
        "available" => false
    ], 
    14 => [
        
        "cp" => 14,
        "name" => '<div class="scrabble-tile bonus tw">TW</div>',
        "type" => "puzzle point",
        "bonus" => ["type" => "word", "value" => 3],
        "puzzle" => true,
        "puzzle_q" => "Unscramble this anagram into a single word to unlock the bonus.<br><br><img class='puzzle_pic' src='assets/img/puzzle-4.png'>",
        "puzzle_a" => "encryption",
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

$cp_bible_old = [
    1 => [
        "cp" => 1,
        "name" => '1 - <div class="scrabble-tile letter-tile">
                    <span class="letter">D</span>
                    <span class="points">2</span>
                    </div>',
        "value" => "D",
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
        "name" => '2 - <div class="scrabble-tile letter-tile">
                    <span class="letter">I</span>
                    <span class="points">1</span>
                    </div>',
        "value" => "I",
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
        "name" => '3 - <div class="scrabble-tile letter-tile">
                    <span class="letter">B</span>
                    <span class="points">3</span>
                    </div>',
        "value" => "B",
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
        "name" => '4 - <div class="scrabble-tile letter-tile">
                    <span class="letter">E</span>
                    <span class="points">1</span>
                    </div>',
        "value" => "E",
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
        "name" => '5 - <div class="scrabble-tile letter-tile">
                    <span class="letter">F</span>
                    <span class="points">4</span>
                    </div>',
        "value" => "F",
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
        "name" => '6 - <div class="scrabble-tile letter-tile">
                    <span class="letter">R</span>
                    <span class="points">1</span>
                    </div>',
        "value" => "R",
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
        "name" => '7 - <div class="scrabble-tile letter-tile">
                    <span class="letter">L</span>
                    <span class="points">1</span>
                    </div>',
        "value" => "L",
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
        "name" => '<div class="scrabble-tile bonus dl">DL</div>',
        "type" => "puzzle point",
        "bonus" => ["type" => "letter", "value" => 2],
        "puzzle" => true,
        "puzzle_q" => "What is the missing number?<br><br><img class='puzzle_pic' src='assets/img/spider.png'>",
        "puzzle_a" => "8",
        "message" => "",
        "options" => [
            1 => "Solve Puzzle"
        ],
        "available" => false
    ],
    12 => [
        
        "cp" => 12,
        "name" => '<div class="scrabble-tile bonus tl">TL</div>',
        "type" => "puzzle point",
        "bonus" => ["type" => "letter", "value" => 3],
        "puzzle" => true,
        "puzzle_q" => "Which staple is represented by the image below?<br><br><img class='puzzle_pic' src='assets/img/potatoes.png'>",
        "puzzle_a" => "potatoes",
        "message" => "",
        "options" => [
            1 => "Solve Puzzle"
        ],
        "available" => false
    ],
    13 => [
            
        "cp" => 13,
        "name" => '<div class="scrabble-tile bonus dw">DW</div>',
        "type" => "puzzle point",
        "bonus" => ["type" => "word", "value" => 2],
        "puzzle" => true,
        "puzzle_q" => "What troublesome character is represented by the image below?<br><br><img class='puzzle_pic' src='assets/img/criminal.png'>",
        "puzzle_a" => "criminal",
        "message" => "",
        "options" => [
            1 => "Solve Puzzle"
        ],
        "available" => false
    ], 
    14 => [
        
        "cp" => 14,
        "name" => '<div class="scrabble-tile bonus tw">TW</div>',
        "type" => "puzzle point",
        "bonus" => ["type" => "word", "value" => 3],
        "puzzle" => true,
        "puzzle_q" => "What is the sum of the next two numbers?<br><br><img class='puzzle_pic' src='assets/img/puzzle-3.png'>",
        "puzzle_a" => "38",
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