<?PHP
$cp_bible = [
    1 => [
        
        "cp" => 1,
        "name" => "One",
        "type" => "target",
        "score" => [29,2,3],
        "puzzle" => false,
        "puzzle_q" => "What is the next letter in this sequence: J, A, S, O, N...",
        "puzzle_a" => "d",
        "message" => "You found Pip the Plumpkin<br><br> <img class='puzzle_pic' src='assets/img/w1.png'>",
        "options" => [
            1 => "Collect critter"
        ],
        "available" => false
    ],
    2 => [
        
        "cp" => 2,
        "name" => "Two",
        "type" => "target",
        "score" => [29,2,3],
        "puzzle" => False,
        "puzzle_q" => "A train leaves Brussels at 11:00 am, averaging 60 mph. Another train headed in the opposite direction leaves Brussels at 1:00 pm, averaging 90 mph.
To the nearest mile, how far are the two trains from each other at 3:00 pm
?",
        "puzzle_a" => "420",
        "message" => "You found Boo the Batlet<br><br> <img class='puzzle_pic' src='assets/img/w2.png'><br>",
        "options" => [
            1 => "Collect critter"
        ],
        "available" => false
    ],
    3 => [
            
        "cp" => 3,
        "name" => "Three",
        "type" => "target",
        "score" => [29,2,3],
        "puzzle" => False,
        "puzzle_q" => "Rearrange the letters of the following two words to give one word: RING + DENT =",
        "puzzle_a" => "trending",
        "message" => "You found Glimmer the Friendly Fright<br><br> <img class='puzzle_pic' src='assets/img/w3.png'>",
        "options" => [
            1 => "Collect critter"
        ],
        "available" => false
    ], 
    4 => [
        
        "cp" => 4,
        "name" => "Four",
        "type" => "target",
        "score" => [29,2,3],
        "puzzle" => false,
        "puzzle_q" => "If the mass of the shapes in the first image is 56g and the mass of the shapes in the second image is 76g what is the mass of the cube in grams<img class='puzzle_pic' src='assets/img/cones.png'>",
        "puzzle_a" => "40",
        "message" => "You found Midnight Whiskers<br><br> <div class='foggy-container'><img class='puzzle_pic' src='assets/img/w4.png'><div class='fog-layer'></div>
  <div class='fog-layer fog-layer2'></div></div> ",
        "options" => [
            1 => "Collect critter"
        ],
        "available" => false
    ],
    5 => [
        
        "cp" => 5,
        "name" => "Five",
        "type" => "target",
        "score" => [29,2,3],
        "puzzle" => false,
        "puzzle_q" => "What number should replace the question mark <img class='puzzle_pic' src='assets/img/spider.png'>",
        "puzzle_a" => "8",
        "message" => "You found Hootabelle<br><br> <img class='puzzle_pic' src='assets/img/w5.png'>",
        "options" => [
            1 => "Collect critter"
        ],
        "available" => false
    ],
    6 => [
            
        "cp" => 6,
        "name" => "Six",
        "type" => "target",
        "score" => [29,2,3],
        "puzzle" => false,
        "puzzle_q" => "The square has a perimeter of 48cm. The square is cut in half. The two halves are put together to make the second shape. What is the perimeter of the second shape in cms<br> <img class='puzzle_pic' src='assets/img/perimeter.png'>",
        "puzzle_a" => "60",
        "message" => "You found Webby Wiggles<br><br> <img class='puzzle_pic' src='assets/img/w6.png'>",
        "options" => [
            1 => "Collect critter"
        ],
        "available" => false
    ],
    7 => [
        
        "cp" => 7,
        "name" => "Seven",
        "type" => "target",
        "score" => [29,2,3],
        "puzzle" => false,
        "puzzle_q" => "What is the greatest number of doughnuts you can buy with £10 <img class='puzzle_pic' src='assets/img/donuts.png'>",
        "puzzle_a" => "22",
        "message" => "You found Hedgewitch<br><br> <img class='puzzle_pic' src='assets/img/w7.png'>",
        "options" => [
            1 => "Collect critter"
        ],
        "available" => false
    ],
    8 => [
        
        "cp" => 8,
        "name" => "Eight",
        "type" => "target",
        "score" => [29,2,3],
        "puzzle" => false,
        "puzzle_q" => "What is the greatest number of doughnuts you can buy with £10 <img class='puzzle_pic' src='assets/img/donuts.png'>",
        "puzzle_a" => "22",
        "message" => "You found Mummy Bunny<br><br> <img class='puzzle_pic' src='assets/img/w8.png'>",
        "options" => [
            1 => "Collect critter"
        ],
        "available" => false
    ],
    9 => [
        
        "cp" => 9,
        "name" => "Nine",
        "type" => "target",
        "score" => [29,2,3],
        "puzzle" => false,
        "puzzle_q" => "What is the greatest number of doughnuts you can buy with £10 <img class='puzzle_pic' src='assets/img/donuts.png'>",
        "puzzle_a" => "22",
        "message" => "You found Count Frogcula<br><br> <img class='puzzle_pic' src='assets/img/w9.png'>",
        "options" => [
            1 => "Collect critter"
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
            "message" => "Welcome to the EPPA Halloween Trail! A host of naughty critters have escaped from Epsom Primary, can you collect them up to earn a sweet reward?",
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
            "form" => true,
            "puzzle_q" => "x",
            "puzzle_a" => "x",
            "message" => '<img class="puzzle_pic" src="assets/img/prize.jpeg"><br>Well done on capturing the critters<br>Enter your details to enter the prize draw: <input type="text" id="name" name="name" placeholder="Name of Child/Children">
  <input type="text" id="classname" name="classname" placeholder="Class Name">',
            "options" => [
                1 => "Finish"
            ],
            "available" => false
        ]
         //etc
    ];