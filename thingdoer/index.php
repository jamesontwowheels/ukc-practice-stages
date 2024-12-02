<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Query Count</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: #ffffff;
        }
        .count-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.5);
        }
        .count-container h1 {
            font-size: 4rem;
            margin: 0;
            color: #ffcc00;
        }
        .count-container p {
            font-size: 1.2rem;
            margin-top: 10px;
            color: #cccccc;
        }
    </style>
</head>
<body>
    <div class="count-container">
        <h1 id="count">0</h1>
        <p>Records Found</p>
    </div>

    <script>
        // Example: Fetch the count dynamically via API or inline script
        fetch('/assets/php/get_count.php') // Replace with your API endpoint
            .then(response => response.json())
            .then(data => {
                document.getElementById('count').textContent = data.count; // Assuming the response has a `count` field
            })
            .catch(error => {
                console.error('Error fetching count:', error);
                document.getElementById('count').textContent = 'Error';
            });
    </script>
</body>
</html>
